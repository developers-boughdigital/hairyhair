<?php
/**
 * Local lead association for newly registered users.
 *
 * Works directly on {$wpdb->prefix}hh_lead_results so it does not depend on the active theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HH_Registration_Lead {

	/** User meta: Unix time the account's email ownership was proven (emailed password set/reset link). */
	const META_EMAIL_VERIFIED = '_hh_email_verified';

	/**
	 * @return string Lead table name.
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'hh_lead_results';
	}

	/**
	 * @return string Quiz results table name (theme quiz).
	 */
	public static function quiz_table() {
		global $wpdb;
		return $wpdb->prefix . 'hh_quiz_results';
	}

	/**
	 * Whether the account has proven it controls its email address.
	 *
	 * WooCommerce registration (My Account and checkout) logs the new customer in immediately and
	 * emails a "set your password" link, so registering or being logged in proves nothing about the
	 * email. Completing an emailed password set/reset link does (see mark_email_verified()).
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool
	 */
	public static function is_email_verified( $user_id ) {
		$user_id  = absint( $user_id );
		$verified = $user_id && (bool) get_user_meta( $user_id, self::META_EMAIL_VERIFIED, true );

		/**
		 * Whether guest leads/quiz results with this account's email may be linked to it.
		 * Use to plug in another proof of email ownership (e.g. an email-verification plugin).
		 *
		 * @param bool $verified Default decision.
		 * @param int  $user_id  User ID.
		 */
		return (bool) apply_filters( 'hh_lead_link_email_verified', $verified, $user_id );
	}

	/**
	 * Records proof of email ownership. Called after an emailed password set/reset link is used.
	 *
	 * @param int $user_id WordPress user ID.
	 */
	public static function mark_email_verified( $user_id ) {
		$user_id = absint( $user_id );
		if ( $user_id && ! get_user_meta( $user_id, self::META_EMAIL_VERIFIED, true ) ) {
			update_user_meta( $user_id, self::META_EMAIL_VERIFIED, time() );
		}
	}

	/**
	 * Links the user's guest leads and guest quiz results (same email, not owned by anyone) to the
	 * account, if the account's email is verified. Shared entry point for registration, login and
	 * password reset. Never creates rows, never changes a row owned by a user, never calls Zoho.
	 * Cheap no-op (one SELECT per table, no writes) when there is nothing to link.
	 *
	 * Fires `hh_leads_linked` ( $user_id, $email, $result ) only when rows were newly linked.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array{status: string, leads: int[], quiz: int[], conflicts: int[]}
	 *         status: linked | nothing_to_link | unverified | invalid
	 */
	public static function link_user( $user_id ) {
		global $wpdb;
		$result  = array( 'status' => 'invalid', 'leads' => array(), 'quiz' => array(), 'conflicts' => array() );
		$user_id = absint( $user_id );
		$user    = $user_id ? get_userdata( $user_id ) : false;
		$email   = $user ? self::normalize_email( $user->user_email ) : '';
		if ( '' === $email ) {
			return $result;
		}

		if ( ! self::is_email_verified( $user_id ) ) {
			$result['status'] = 'unverified';
			return $result;
		}

		$has_leads = self::table_exists() && $wpdb->get_var(
			$wpdb->prepare( 'SELECT 1 FROM `' . self::table() . '` WHERE email = %s AND user_id = 0 LIMIT 1', $email )
		);
		$has_quiz  = self::quiz_table_exists() && $wpdb->get_var(
			$wpdb->prepare( 'SELECT 1 FROM `' . self::quiz_table() . '` WHERE email = %s AND ( user_id IS NULL OR user_id = 0 ) LIMIT 1', $email )
		);
		if ( ! $has_leads && ! $has_quiz ) {
			$result['status'] = 'nothing_to_link';
			return $result;
		}

		if ( $has_leads ) {
			$leads               = self::associate( $user_id, $email );
			$result['leads']     = $leads['associated'];
			$result['conflicts'] = $leads['conflicts'];
		}
		if ( $has_quiz ) {
			$result['quiz'] = self::associate_quiz_results( $user_id, $email );
		}

		$result['status'] = ( $result['leads'] || $result['quiz'] ) ? 'linked' : 'nothing_to_link';
		if ( 'linked' === $result['status'] ) {
			/**
			 * Guest leads and/or quiz results were just linked to a verified account.
			 *
			 * @param int    $user_id User ID.
			 * @param string $email   Normalised email.
			 * @param array  $result  Linked row IDs.
			 */
			do_action( 'hh_leads_linked', $user_id, $email, $result );
		}
		return $result;
	}

	/**
	 * Links guest quiz results (user_id NULL or 0) with this email to the user. Idempotent.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $email   Normalised email.
	 * @return int[] Newly linked quiz result IDs.
	 */
	public static function associate_quiz_results( $user_id, $email ) {
		global $wpdb;
		$table = self::quiz_table();
		$ids   = array_map(
			'intval',
			(array) $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM `{$table}` WHERE email = %s AND ( user_id IS NULL OR user_id = 0 ) ORDER BY ID ASC", $email ) )
		);
		if ( ! $ids ) {
			return array();
		}
		$updated = $wpdb->query(
			$wpdb->prepare( "UPDATE `{$table}` SET user_id = %d WHERE email = %s AND ( user_id IS NULL OR user_id = 0 )", absint( $user_id ), $email )
		);
		if ( false === $updated ) {
			HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: quiz result association failed (DB_ERROR).', $user_id ) );
			return array();
		}
		HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: linked quiz result(s) #%s.', $user_id, implode( ', #', $ids ) ) );
		return $ids;
	}

	/**
	 * @return bool Whether the quiz results table exists.
	 */
	public static function quiz_table_exists() {
		global $wpdb;
		$table = self::quiz_table();
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	/**
	 * Lower-cases and validates an email address.
	 *
	 * @param string $email Raw email.
	 * @return string Normalised email, or '' when invalid.
	 */
	public static function normalize_email( $email ) {
		$email = strtolower( trim( sanitize_email( (string) $email ) ) );
		return is_email( $email ) ? $email : '';
	}

	/**
	 * @return bool Whether the lead table exists.
	 */
	public static function table_exists() {
		global $wpdb;
		$table = self::table();
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	/**
	 * @return bool Whether the zoho_id column exists (added by the theme's migration).
	 */
	public static function has_zoho_column() {
		global $wpdb;
		return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `' . self::table() . '` LIKE %s', 'zoho_id' ) );
	}

	/**
	 * Links every guest lead (user_id = 0) with this email to the user.
	 *
	 * Only user_id changes. Rows already owned by this user are left alone; rows owned by another
	 * user are never touched (logged as a conflict). Idempotent.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $email   Normalised email.
	 * @return array{associated: int[], conflicts: int[], ok: bool}
	 */
	public static function associate( $user_id, $email ) {
		global $wpdb;
		$result = array( 'associated' => array(), 'conflicts' => array(), 'ok' => false );
		$user_id = absint( $user_id );

		if ( ! $user_id || '' === $email || ! self::table_exists() ) {
			return $result;
		}

		$table = self::table();

		// Same lock the lead form uses, so a lead submission for this email cannot interleave.
		$lock = 'hh_lead_' . md5( $email );
		$got  = '1' === (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock, 5 ) );

		try {
			$rows = $wpdb->get_results(
				$wpdb->prepare( "SELECT ID, user_id FROM {$table} WHERE email = %s ORDER BY ID ASC", $email )
			);

			foreach ( $rows as $row ) {
				$owner = (int) $row->user_id;
				if ( 0 === $owner ) {
					$result['associated'][] = (int) $row->ID;
				} elseif ( $owner !== $user_id ) {
					$result['conflicts'][] = (int) $row->ID;
				}
			}

			if ( $result['associated'] ) {
				// The user_id = 0 condition makes this safe against concurrent changes.
				$updated = $wpdb->query(
					$wpdb->prepare( "UPDATE {$table} SET user_id = %d WHERE email = %s AND user_id = 0", $user_id, $email )
				);
				if ( false === $updated ) {
					HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: lead association failed (DB_ERROR).', $user_id ) );
					return $result;
				}
			}
		} finally {
			if ( $got ) {
				$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
			}
		}

		if ( $result['associated'] ) {
			HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: linked lead(s) #%s.', $user_id, implode( ', #', $result['associated'] ) ) );
		}
		if ( $result['conflicts'] ) {
			HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: lead(s) #%s with this email belong to another user; not changed.', $user_id, implode( ', #', $result['conflicts'] ) ) );
		}

		$result['ok'] = true;
		return $result;
	}

	/**
	 * The user's own lead with this email, preferring one that already has a zoho_id, then the
	 * newest. Only rows owned by the user (unlinked guest rows are never treated as the user's).
	 * Zoho target selection itself is done by HH_Zoho_Lead_Sync.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param string $email   Normalised email.
	 * @return object|null Row with ID and zoho_id, or null.
	 */
	public static function get_primary_lead( $user_id, $email ) {
		global $wpdb;
		if ( ! self::table_exists() ) {
			return null;
		}
		$table = self::table();
		$zoho  = self::has_zoho_column() ? 'zoho_id' : 'NULL AS zoho_id';
		$order = self::has_zoho_column() ? '(zoho_id IS NULL) ASC, ' : '';

		$row = $wpdb->get_row(
			$wpdb->prepare( "SELECT ID, {$zoho} FROM {$table} WHERE email = %s AND user_id = %d ORDER BY {$order}ID DESC LIMIT 1", $email, absint( $user_id ) )
		);
		return $row ?: null;
	}

	/**
	 * Stores a Zoho Lead ID on a lead, only if its zoho_id is still empty.
	 *
	 * @param int    $lead_id Lead primary key.
	 * @param string $zoho_id Validated Zoho Lead ID.
	 * @return bool False on database error.
	 */
	public static function save_zoho_id( $lead_id, $zoho_id ) {
		global $wpdb;
		if ( ! self::has_zoho_column() ) {
			return false;
		}
		$result = $wpdb->query(
			$wpdb->prepare(
				'UPDATE `' . self::table() . '` SET zoho_id = %s WHERE ID = %d AND zoho_id IS NULL',
				$zoho_id,
				(int) $lead_id
			)
		);
		return false !== $result;
	}
}
