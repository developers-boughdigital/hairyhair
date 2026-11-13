<?php
/**
 * WP-CLI: `wp hh-leads ...` — lead Zoho sync status, manual reprocessing, legacy repair DRY RUN.
 *
 * legacy-repair-plan is read-only by design: it has no apply mode, and it aborts if anything
 * other than a SELECT/SHOW query is attempted while it runs.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

class HH_Leads_CLI {

	/**
	 * Shows Zoho sync state of lead-form submissions (read-only).
	 *
	 * ## OPTIONS
	 *
	 * [--status=<status>]
	 * : List rows with this status (e.g. needs_review, failed, retry, create_uncertain).
	 *
	 * @subcommand sync-status
	 */
	public function sync_status( $args, $assoc ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();
		if ( ! HH_Zoho_Lead_Sync::schema_ready() ) {
			WP_CLI::error( 'Lead table is not at schema version 3 yet (sync-state columns missing).' );
		}
		$counts = $wpdb->get_results( "SELECT IFNULL(zoho_sync_status, '(untracked)') AS status, IFNULL(zoho_sync_error, '') AS code, COUNT(*) AS rows_count FROM `{$table}` GROUP BY 1, 2 ORDER BY 1, 2", ARRAY_A );
		WP_CLI\Utils\format_items( 'table', $counts, array( 'status', 'code', 'rows_count' ) );

		if ( ! empty( $assoc['status'] ) ) {
			$rows = $wpdb->get_results( $wpdb->prepare( "SELECT ID, user_id, zoho_id, zoho_sync_status, zoho_sync_error, zoho_sync_attempts, zoho_sync_at FROM `{$table}` WHERE zoho_sync_status = %s ORDER BY ID", $assoc['status'] ), ARRAY_A );
			foreach ( $rows as &$r ) {
				$r['zoho_id'] = self::mask_id( $r['zoho_id'] );
			}
			WP_CLI\Utils\format_items( 'table', $rows, array( 'ID', 'user_id', 'zoho_id', 'zoho_sync_status', 'zoho_sync_error', 'zoho_sync_attempts', 'zoho_sync_at' ) );
		}
	}

	/**
	 * Manually re-runs the Zoho sync for one lead-form submission, applying the same ownership rules.
	 *
	 * ## OPTIONS
	 *
	 * <lead_id>
	 * : Lead row ID.
	 *
	 * [--assign-zoho-id=<id>]
	 * : Staff decision after review: write this submission to this Zoho Lead (still refused if the
	 *   Lead belongs to a different WordPress user).
	 *
	 * [--allow-create]
	 * : Staff decision after review: create a separate Zoho Lead despite a possible duplicate.
	 *
	 * [--yes]
	 * : Skip the confirmation.
	 */
	public function reprocess( $args, $assoc ) {
		global $wpdb;
		$lead_id = absint( $args[0] );
		if ( ! $lead_id || ! HH_Zoho_Lead_Sync::schema_ready() ) {
			WP_CLI::error( 'Unknown lead or schema not ready.' );
		}
		WP_CLI::confirm( sprintf( 'Re-run Zoho sync for lead #%d? This may write to Zoho CRM.', $lead_id ), $assoc );
		$table = HH_Registration_Lead::table();
		$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET zoho_sync_attempts = 0 WHERE ID = %d", $lead_id ) );
		$status = HH_Zoho_Lead_Sync::sync_lead( $lead_id, array(
			'manual'       => true,
			'lock_wait'    => 10,
			'assign_id'    => isset( $assoc['assign-zoho-id'] ) ? trim( (string) $assoc['assign-zoho-id'] ) : '',
			'allow_create' => ! empty( $assoc['allow-create'] ),
		) );
		WP_CLI::log( sprintf( 'Lead #%d: %s', $lead_id, $status ) );
	}

	/**
	 * DRY RUN ONLY: plans the repair of legacy rows whose user_id holds a Zoho Lead ID.
	 *
	 * Executes SELECT/SHOW queries only. Proposes, per confirmed row: zoho_id = old user_id (only if
	 * zoho_id is empty) and user_id = 0. Never applies anything.
	 *
	 * ## OPTIONS
	 *
	 * [--zoho-prefix=<digits>]
	 * : Zoho org ID prefix (first 7 digits of known Zoho Lead IDs) confirmed by the reviewer in Zoho.
	 *   Used in addition to prefixes of Zoho IDs already stored locally.
	 *
	 * [--output=<file>]
	 * : Write the full plan (unmasked original values, forward and rollback SQL as TEXT) to this
	 *   JSON file (mode 0600) for review and audit. Terminal output is always masked.
	 *
	 * @subcommand legacy-repair-plan
	 */
	public function legacy_repair_plan( $args, $assoc ) {
		$guard = function ( $q ) {
			if ( ! preg_match( '/^\s*(SELECT|SHOW|DESCRIBE)\b/i', $q ) ) {
				WP_CLI::error( 'Aborted: dry run attempted a non-read query.' );
			}
			return $q;
		};
		add_filter( 'query', $guard, PHP_INT_MAX );
		try {
			$plan = self::build_legacy_plan( isset( $assoc['zoho-prefix'] ) ? (string) $assoc['zoho-prefix'] : '' );
		} finally {
			remove_filter( 'query', $guard, PHP_INT_MAX );
		}

		WP_CLI::log( 'DRY RUN - no changes were made. Schema: ' . wp_json_encode( $plan['schema'] ) );
		WP_CLI::log( 'Reference Zoho prefixes: ' . ( $plan['prefixes'] ? implode( ', ', $plan['prefixes'] ) : '(none - pass --zoho-prefix after checking Zoho)' ) );
		WP_CLI\Utils\format_items( 'table', array_map( function ( $k, $v ) { return array( 'classification' => $k, 'rows' => $v ); }, array_keys( $plan['summary'] ), $plan['summary'] ), array( 'classification', 'rows' ) );
		$masked = array_map(
			function ( $r ) {
				return array(
					'lead_id'        => $r['lead_id'],
					'user_id'        => self::mask_id( $r['original']['user_id'] ),
					'zoho_id'        => '' === (string) $r['original']['zoho_id'] ? '(empty)' : self::mask_id( $r['original']['zoho_id'] ),
					'email'          => self::mask_email( $r['email'] ),
					'classification' => $r['classification'],
					'notes'          => implode( '; ', $r['notes'] ),
				);
			},
			$plan['rows']
		);
		WP_CLI\Utils\format_items( 'table', $masked, array( 'lead_id', 'user_id', 'zoho_id', 'email', 'classification', 'notes' ) );

		if ( ! empty( $assoc['output'] ) ) {
			$file = (string) $assoc['output'];
			if ( false === file_put_contents( $file, wp_json_encode( $plan, JSON_PRETTY_PRINT ) ) ) {
				WP_CLI::error( 'Could not write ' . $file );
			}
			@chmod( $file, 0600 );
			WP_CLI::log( 'Full plan (contains personal data; keep private) written to ' . $file );
		}
		WP_CLI::success( sprintf( '%d candidate row(s), %d proposed, %d need review. Nothing was changed.', count( $plan['rows'] ), $plan['summary']['proposed'] ?? 0, count( $plan['rows'] ) - ( $plan['summary']['proposed'] ?? 0 ) ) );
	}

	/**
	 * Builds the legacy repair plan (read-only).
	 *
	 * @param string $prefix_arg Reviewer-confirmed Zoho prefix.
	 * @return array
	 */
	public static function build_legacy_plan( $prefix_arg = '' ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();

		$columns = array();
		foreach ( (array) $wpdb->get_results( "SHOW COLUMNS FROM `{$table}`" ) as $c ) {
			$columns[ $c->Field ] = $c->Type;
		}
		$schema = array(
			'table'   => $table,
			'user_id' => $columns['user_id'] ?? null,
			'zoho_id' => $columns['zoho_id'] ?? null,
		);
		$max_user = (int) $wpdb->get_var( "SELECT MAX(ID) FROM {$wpdb->users}" );

		// Prefixes of Zoho IDs from trusted sources (stored zoho_id values, account Lead IDs) + reviewer input.
		$known = array_merge(
			(array) ( isset( $columns['zoho_id'] ) ? $wpdb->get_col( "SELECT DISTINCT zoho_id FROM `{$table}` WHERE zoho_id REGEXP '^[0-9]{15,19}$'" ) : array() ),
			(array) $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value REGEXP '^[0-9]{15,19}$'", HH_Registration_Zoho::META_LEAD_ID ) )
		);
		$prefixes = array_map( function ( $id ) { return substr( $id, 0, 7 ); }, $known );
		if ( preg_match( '/^\d{7}$/', $prefix_arg ) ) {
			$prefixes[] = $prefix_arg;
		}
		$prefixes = array_values( array_unique( $prefixes ) );

		$cands = $wpdb->get_results(
			"SELECT l.ID, CAST(l.user_id AS CHAR) AS user_id, " . ( isset( $columns['zoho_id'] ) ? 'l.zoho_id' : 'NULL AS zoho_id' ) . ", l.email, l.submission_date
			 FROM `{$table}` l LEFT JOIN {$wpdb->users} u ON u.ID = l.user_id
			 WHERE l.user_id > 0 AND u.ID IS NULL ORDER BY l.ID"
		);
		$value_counts = array_count_values( array_map( function ( $r ) { return $r->user_id; }, $cands ) );

		$rows    = array();
		$summary = array();
		foreach ( $cands as $r ) {
			$uid   = (string) $r->user_id;
			$zid   = null === $r->zoho_id ? '' : trim( (string) $r->zoho_id );
			$notes = array();
			$zoho_like = (bool) preg_match( '/^\d{15,19}$/', $uid ) && (float) $uid > $max_user;
			$prefix_ok = $zoho_like && in_array( substr( $uid, 0, 7 ), $prefixes, true );
			$in_use    = $zoho_like && ( (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `{$table}` WHERE zoho_id = %s AND ID <> %d", $uid, $r->ID ) )
				+ (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s", HH_Registration_Zoho::META_LEAD_ID, $uid ) ) ) > 0;
			if ( $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->users} WHERE user_email = %s", $r->email ) ) ) {
				$notes[] = 'email has a WordPress account (P2 linking applies once verified)';
			}

			if ( ! isset( $columns['zoho_id'] ) ) {
				$class = 'blocked_no_zoho_id_column';
			} elseif ( ! $zoho_like ) {
				$class = 'review_orphan_user_reference'; // Missing WP user, but not a Zoho-shaped value.
			} elseif ( '' !== $zid && $zid !== $uid ) {
				$class = 'conflict_zoho_id_differs';
			} elseif ( $value_counts[ $uid ] > 1 ) {
				$class = 'conflict_duplicate_value';
			} elseif ( $in_use ) {
				$class = 'conflict_value_in_use_elsewhere';
			} elseif ( ! $prefix_ok ) {
				$class = 'review_unconfirmed_prefix';
			} else {
				$class = 'proposed';
			}
			$summary[ $class ] = ( $summary[ $class ] ?? 0 ) + 1;

			$entry = array(
				'lead_id'        => (int) $r->ID,
				'email'          => (string) $r->email,
				'submitted'      => (string) $r->submission_date,
				'original'       => array( 'user_id' => $uid, 'zoho_id' => $zid ),
				'classification' => $class,
				'notes'          => $notes,
			);
			if ( 'proposed' === $class ) {
				$entry['proposed'] = array( 'user_id' => '0', 'zoho_id' => '' === $zid ? $uid : $zid );
				// TEXT ONLY for review; guarded so it can only change the row if it is still unchanged.
				$entry['sql_forward']  = sprintf( "UPDATE `%s` SET zoho_id = IF(zoho_id IS NULL OR zoho_id = '', '%s', zoho_id), user_id = 0 WHERE ID = %d AND user_id = %s;", $table, $uid, $r->ID, $uid );
				$entry['sql_rollback'] = sprintf( 'UPDATE `%s` SET user_id = %s, zoho_id = %s WHERE ID = %d AND user_id = 0;', $table, $uid, '' === $zid ? 'NULL' : "'" . $zid . "'", $r->ID );
			}
			$rows[] = $entry;
		}
		ksort( $summary );

		return array( 'dry_run' => true, 'generated' => gmdate( 'c' ), 'schema' => $schema, 'max_wp_user_id' => $max_user, 'prefixes' => $prefixes, 'summary' => $summary, 'rows' => $rows );
	}

	/**
	 * @param string $id ID.
	 * @return string Masked (first 4 and last 4 characters).
	 */
	public static function mask_id( $id ) {
		$id = (string) $id;
		return strlen( $id ) > 8 ? substr( $id, 0, 4 ) . '…' . substr( $id, -4 ) : $id;
	}

	/**
	 * @param string $email Email.
	 * @return string Masked email.
	 */
	public static function mask_email( $email ) {
		$parts = explode( '@', (string) $email, 2 );
		return substr( $parts[0], 0, 1 ) . '***@' . ( isset( $parts[1] ) ? substr( $parts[1], 0, 1 ) . '***' : '' );
	}
}

WP_CLI::add_command( 'hh-leads', 'HH_Leads_CLI' );
