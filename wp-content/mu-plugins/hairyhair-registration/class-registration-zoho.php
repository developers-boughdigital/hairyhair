<?php
/**
 * Zoho CRM sync for new registrations, run as a background job.
 *
 * All Zoho writes go through Connect_To_Zoho::updateInsertCRMrecord() (connect-with-zoho plugin):
 * empty record ID -> POST /Leads (create), record ID -> PUT /Leads/{id} (update); it returns the
 * Zoho Lead ID on SUCCESS and throws otherwise.
 *
 * Per-user state (user meta, no local lead row is created for tracking):
 *   _hh_reg_zoho_status    pending | done | failed | awaiting_verification | needs_review
 *   _hh_reg_zoho_attempts  number of attempts made
 *   _hh_reg_zoho_lead_id   Zoho Lead ID once a create/update succeeded
 *   _hh_reg_zoho_uncertain 1 while a CREATE outcome is unknown (retry searches before creating)
 *   _hh_reg_zoho_error     last error / review code (codes only)
 *
 * Which Zoho Lead to write to is decided by HH_Zoho_Lead_Sync::decide() (shared with the lead
 * form) under the shared per-email lock. An existing Lead found only by email is never updated
 * for an unverified account: the job waits (awaiting_verification) and is re-queued when the
 * customer proves the email with the emailed password set/reset link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HH_Registration_Zoho {

	const JOB_HOOK      = 'hh_registration_zoho_sync';
	const JOB_GROUP     = 'hairyhair-registration';
	const META_STATUS   = '_hh_reg_zoho_status';
	const META_ATTEMPTS = '_hh_reg_zoho_attempts';
	const META_LEAD_ID  = '_hh_reg_zoho_lead_id';
	const META_UNCERTAIN = '_hh_reg_zoho_uncertain';
	const META_ERROR    = '_hh_reg_zoho_error';
	const MAX_ATTEMPTS  = 4;
	const LEAD_SOURCE   = 'Website - SignUp'; // Existing value in the Zoho Leads "Lead Source" picklist.

	/**
	 * Queues the first sync attempt for a user, once.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return bool True if a job was queued.
	 */
	public static function schedule( $user_id ) {
		$user_id = absint( $user_id );

		// add_user_meta( ..., unique = true ) refuses a second value: one job per user.
		if ( ! $user_id || ! add_user_meta( $user_id, self::META_STATUS, 'pending', true ) ) {
			return false;
		}

		return self::queue( $user_id, 1, 0 );
	}

	/**
	 * Queues a job run with Action Scheduler (bundled with WooCommerce) or WP-Cron as a fallback.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $attempt Attempt number (1-based).
	 * @param int $delay   Seconds from now.
	 * @return bool
	 */
	protected static function queue( $user_id, $attempt, $delay ) {
		$args = array( (int) $user_id, (int) $attempt );

		if ( function_exists( 'as_schedule_single_action' ) ) {
			if ( function_exists( 'as_has_scheduled_action' ) && as_has_scheduled_action( self::JOB_HOOK, $args, self::JOB_GROUP ) ) {
				return true;
			}
			return (bool) as_schedule_single_action( time() + $delay, self::JOB_HOOK, $args, self::JOB_GROUP );
		}

		if ( wp_next_scheduled( self::JOB_HOOK, $args ) ) {
			return true;
		}
		return true === wp_schedule_single_event( time() + $delay, self::JOB_HOOK, $args, true );
	}

	/**
	 * Job callback: creates or updates the user's Zoho Lead.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $attempt Attempt number.
	 */
	public static function run( $user_id, $attempt = 1 ) {
		global $wpdb;
		$user_id = absint( $user_id );
		$attempt = max( 1, absint( $attempt ) );
		$user    = $user_id ? get_userdata( $user_id ) : false;

		if ( ! $user || in_array( get_user_meta( $user_id, self::META_STATUS, true ), array( 'done', 'failed', 'awaiting_verification', 'needs_review' ), true ) ) {
			return;
		}

		// Stop overlapping runs for the same user (e.g. a job queued twice).
		$lock = 'hh_reg_zoho_' . $user_id;
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', $lock ) ) ) {
			return;
		}

		try {
			self::sync( $user, $attempt );
		} catch ( \Throwable $e ) {
			self::fail( $user_id, $attempt, 'UNEXPECTED_ERROR' );
		} finally {
			$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
		}
	}

	/**
	 * @param WP_User $user    User.
	 * @param int     $attempt Attempt number.
	 */
	protected static function sync( $user, $attempt ) {
		$user_id = (int) $user->ID;
		update_user_meta( $user_id, self::META_ATTEMPTS, $attempt );

		$email = HH_Registration_Lead::normalize_email( $user->user_email );
		if ( '' === $email ) {
			update_user_meta( $user_id, self::META_STATUS, 'failed' );
			self::log( sprintf( 'HairyHair registration user #%d: Zoho sync skipped (INVALID_EMAIL).', $user_id ) );
			return;
		}

		if ( ! self::connector_available() ) {
			self::fail( $user_id, $attempt, 'CONNECTOR_UNAVAILABLE' );
			return;
		}

		// Shared per-email lock with lead-form syncs: never two Zoho writes for one email at once.
		if ( ! HH_Zoho_Lead_Sync::lock( $email, 0 ) ) {
			update_user_meta( $user_id, self::META_STATUS, 'pending' );
			update_user_meta( $user_id, self::META_ATTEMPTS, max( 0, $attempt - 1 ) ); // A busy lock is not a failed attempt.
			self::queue( $user_id, $attempt, MINUTE_IN_SECONDS );
			return;
		}

		try {
			self::sync_locked( $user, $email, $attempt );
		} finally {
			HH_Zoho_Lead_Sync::unlock( $email );
		}
	}

	/**
	 * @param WP_User $user    User.
	 * @param string  $email   Normalised account email.
	 * @param int     $attempt Attempt number.
	 */
	protected static function sync_locked( $user, $email, $attempt ) {
		$user_id   = (int) $user->ID;
		$fields    = self::registration_fields( $user, $email );
		$uncertain = (bool) get_user_meta( $user_id, self::META_UNCERTAIN, true );

		$decision = HH_Zoho_Lead_Sync::decide( array(
			'email'     => $email,
			'user_id'   => $user_id,
			'verified'  => HH_Registration_Lead::is_email_verified( $user_id ),
			'trusted'   => HH_Zoho_Lead_Sync::trusted_ids( $user_id, $email ),
			'uncertain' => $uncertain,
			'account'   => true,
		) );

		switch ( $decision['action'] ) {
			case 'wait':
				update_user_meta( $user_id, self::META_STATUS, 'awaiting_verification' );
				update_user_meta( $user_id, self::META_ERROR, $decision['reason'] );
				self::log( sprintf( 'HairyHair registration user #%d: possible existing Zoho Lead for this email; waiting for email verification (no Zoho write).', $user_id ) );
				return;
			case 'review':
				update_user_meta( $user_id, self::META_STATUS, 'needs_review' );
				update_user_meta( $user_id, self::META_ERROR, $decision['reason'] );
				self::log( sprintf( 'HairyHair registration user #%d: needs review (%s); no Zoho write.', $user_id, $decision['reason'] ) );
				return;
			case 'retry':
				self::fail( $user_id, $attempt, $decision['reason'] );
				return;
			case 'adopt':
				self::succeed( $user_id, $decision['id'], 'adopted after an uncertain create' );
				return;
		}

		$target_id = 'update' === $decision['action'] ? $decision['id'] : '';
		if ( '' === $target_id ) {
			if ( empty( $fields['Last_Name'] ) ) {
				update_user_meta( $user_id, self::META_STATUS, 'failed' );
				update_user_meta( $user_id, self::META_ERROR, 'MISSING_LAST_NAME' );
				self::log( sprintf( 'HairyHair registration user #%d: Zoho sync failed (MISSING_LAST_NAME); not retried.', $user_id ) ); // Mandatory in Zoho Leads; never invented.
				return;
			}
			$fields['Lead_Source'] = self::LEAD_SOURCE;
			update_user_meta( $user_id, self::META_UNCERTAIN, 1 ); // Cleared on success; survives a crash mid-request.
		}

		try {
			$zoho        = new Connect_To_Zoho();
			$returned_id = $zoho->updateInsertCRMrecord( 'Leads', $target_id, $fields );
		} catch ( \Throwable $e ) {
			$c = HH_Zoho_Lead_Sync::classify( $e, '' === $target_id ? 'POST' : 'PUT' );
			if ( 'uncertain' !== $c['kind'] ) {
				delete_user_meta( $user_id, self::META_UNCERTAIN ); // Zoho answered: nothing was created.
			}
			if ( 'permanent' === $c['kind'] ) {
				update_user_meta( $user_id, self::META_STATUS, 'failed' );
				update_user_meta( $user_id, self::META_ERROR, $c['code'] );
				self::log( sprintf( 'HairyHair registration user #%d: permanent Zoho error %s; not retried.', $user_id, $c['code'] ) );
			} elseif ( 'target_unavailable' === $c['kind'] ) {
				update_user_meta( $user_id, self::META_STATUS, 'needs_review' );
				update_user_meta( $user_id, self::META_ERROR, $c['code'] );
				self::log( sprintf( 'HairyHair registration user #%d: Zoho Lead %s unavailable (converted/deleted?); needs review, no replacement created.', $user_id, $target_id ) );
			} else {
				self::fail( $user_id, $attempt, $c['code'] );
			}
			return;
		}

		if ( ! self::is_valid_zoho_id( $returned_id ) ) {
			self::fail( $user_id, $attempt, 'INVALID_LEAD_ID' ); // Uncertain flag (if creating) stays set.
			return;
		}
		self::succeed( $user_id, trim( (string) $returned_id ), '' === $target_id ? 'created' : 'updated' );
	}

	/**
	 * Records a successful create/update/adopt for the account.
	 *
	 * @param int    $user_id     User ID.
	 * @param string $returned_id Zoho Lead ID.
	 * @param string $what        Log verb.
	 */
	protected static function succeed( $user_id, $returned_id, $what ) {
		global $wpdb;
		// Record the Zoho ID first, so a retry can never create a second Lead. Never replace an
		// existing, different account ID.
		if ( ! self::is_valid_zoho_id( get_user_meta( $user_id, self::META_LEAD_ID, true ) ) ) {
			update_user_meta( $user_id, self::META_LEAD_ID, $returned_id );
		}
		delete_user_meta( $user_id, self::META_UNCERTAIN );
		delete_user_meta( $user_id, self::META_ERROR );

		// Untracked legacy lead rows owned by this user get the ID (as before); tracked lead-form rows
		// keep their own questionnaire sync and are only re-evaluated, never marked synced here.
		$user  = get_userdata( $user_id );
		$email = $user ? HH_Registration_Lead::normalize_email( $user->user_email ) : '';
		if ( '' !== $email && HH_Registration_Lead::has_zoho_column() ) {
			$table   = HH_Registration_Lead::table();
			$tracked = HH_Zoho_Lead_Sync::schema_ready();
			$lead_id = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT ID FROM `{$table}` WHERE user_id = %d AND email = %s AND zoho_id IS NULL" . ( $tracked ? ' AND zoho_sync_status IS NULL' : '' ) . ' ORDER BY ID DESC LIMIT 1',
				$user_id,
				$email
			) );
			if ( $lead_id && ! HH_Registration_Lead::save_zoho_id( $lead_id, $returned_id ) ) {
				self::log( sprintf( 'HairyHair registration user #%d (lead #%d): Zoho Lead %s saved, but storing zoho_id locally failed.', $user_id, $lead_id, $returned_id ) );
			}
			if ( $tracked ) {
				HH_Zoho_Lead_Sync::requeue_after_verification( $user_id );
			}
		}

		update_user_meta( $user_id, self::META_STATUS, 'done' );
		self::log( sprintf( 'HairyHair registration user #%d: Zoho Lead %s %s.', $user_id, $returned_id, $what ) );
	}

	/**
	 * Re-queues a registration sync that was waiting for email verification. Idempotent.
	 *
	 * @param int $user_id User ID.
	 * @return bool True if queued.
	 */
	public static function resume_after_verification( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id || 'awaiting_verification' !== get_user_meta( $user_id, self::META_STATUS, true ) ) {
			return false;
		}
		update_user_meta( $user_id, self::META_STATUS, 'pending' );
		update_user_meta( $user_id, self::META_ATTEMPTS, 0 );
		return self::queue( $user_id, 1, 0 );
	}

	/**
	 * Registration fields for Zoho. Only non-empty values are sent, so an update never clears
	 * data in Zoho. No quiz or medical fields are included.
	 *
	 * @param WP_User $user  User.
	 * @param string  $email Normalised email.
	 * @return array
	 */
	protected static function registration_fields( $user, $email ) {
		$first = trim( (string) get_user_meta( $user->ID, 'first_name', true ) );
		$last  = trim( (string) get_user_meta( $user->ID, 'last_name', true ) );
		$phone = trim( (string) get_user_meta( $user->ID, 'billing_phone', true ) );

		if ( '' === $first ) {
			$first = trim( (string) get_user_meta( $user->ID, 'billing_first_name', true ) );
		}
		if ( '' === $last ) {
			$last = trim( (string) get_user_meta( $user->ID, 'billing_last_name', true ) );
		}

		return array_filter(
			array(
				'First_Name' => sanitize_text_field( $first ),
				'Last_Name'  => sanitize_text_field( $last ),
				'Email'      => $email,
				'Phone'      => sanitize_text_field( $phone ),
			),
			'strlen'
		);
	}

	/**
	 * Records a failed attempt and queues a retry with back-off, up to MAX_ATTEMPTS.
	 *
	 * @param int    $user_id WordPress user ID.
	 * @param int    $attempt Attempt that failed.
	 * @param string $code    Error code (no customer data).
	 * @param int    $lead_id Local lead ID, if any.
	 */
	protected static function fail( $user_id, $attempt, $code, $lead_id = 0 ) {
		$who = sprintf( 'HairyHair registration user #%d%s', $user_id, $lead_id ? " (lead #{$lead_id})" : '' );
		update_user_meta( $user_id, self::META_ERROR, $code );

		if ( $attempt < self::MAX_ATTEMPTS ) {
			$delays = array( 1 => 5 * MINUTE_IN_SECONDS, 2 => 30 * MINUTE_IN_SECONDS, 3 => 2 * HOUR_IN_SECONDS );
			$delay  = isset( $delays[ $attempt ] ) ? $delays[ $attempt ] : 2 * HOUR_IN_SECONDS;
			update_user_meta( $user_id, self::META_STATUS, 'pending' );
			self::queue( $user_id, $attempt + 1, $delay );
			self::log( sprintf( '%s: Zoho sync failed (%s), attempt %d of %d; retry queued.', $who, $code, $attempt, self::MAX_ATTEMPTS ) );
			return;
		}

		update_user_meta( $user_id, self::META_STATUS, 'failed' );
		self::log( sprintf( '%s: Zoho sync failed (%s), attempt %d of %d; giving up.', $who, $code, $attempt, self::MAX_ATTEMPTS ) );
	}

	/**
	 * @return bool Whether the connect-with-zoho plugin is active and loaded.
	 */
	public static function connector_available() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'connect-with-zoho/class-connect-with-zoho.php' ) && class_exists( 'Connect_To_Zoho' );
	}

	/**
	 * Searches Zoho Leads by email using the connector's saved domain, version and token.
	 * (The connector's searchZohoRecord() is hard-coded to zohoapis.in.)
	 *
	 * @param string $email Normalised email.
	 * @return string|WP_Error Zoho Lead ID, '' if none, or WP_Error (code only).
	 */
	public static function find_lead_id_by_email( $email ) {
		$domain  = untrailingslashit( esc_url_raw( (string) get_option( 'zoho_api_domain' ) ) );
		$version = (string) get_option( 'zoho_api_version' );
		$token   = (string) get_option( 'zoho_access_token' );

		if ( '' === $domain || ! preg_match( '/^v\d+$/', $version ) || '' === $token ) {
			return new WP_Error( 'CONFIG_MISSING' );
		}

		$response = wp_remote_get(
			$domain . '/crm/' . $version . '/Leads/search?email=' . rawurlencode( $email ),
			array(
				'timeout' => 15,
				'headers' => array( 'Authorization' => 'Bearer ' . $token ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'HTTP_REQUEST_FAILED' );
		}

		$status = (int) wp_remote_retrieve_response_code( $response );
		if ( 204 === $status ) {
			return '';
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( 200 === $status && ! empty( $body['data'] ) && is_array( $body['data'] ) ) {
			foreach ( $body['data'] as $record ) {
				if ( isset( $record['id'] ) && self::is_valid_zoho_id( $record['id'] ) ) {
					return trim( (string) $record['id'] );
				}
			}
			return new WP_Error( 'INVALID_RESPONSE' );
		}

		$code = ( is_array( $body ) && isset( $body['code'] ) && preg_match( '/^[A-Z0-9_]{1,64}$/', (string) $body['code'] ) ) ? $body['code'] : 'HTTP_' . $status;
		return new WP_Error( $code );
	}

	/**
	 * @param mixed $id Candidate ID.
	 * @return bool True for numeric Zoho record IDs.
	 */
	public static function is_valid_zoho_id( $id ) {
		return is_scalar( $id ) && (bool) preg_match( '/^\d{1,30}$/', trim( (string) $id ) );
	}

	/**
	 * Extracts only the Zoho error code from a connector exception message.
	 *
	 * @param string $message Exception message.
	 * @return string
	 */
	protected static function error_code( $message ) {
		if ( preg_match( '/"code"\s*:\s*"([A-Z0-9_]{1,64})"/', (string) $message, $m ) ) {
			return $m[1];
		}
		if ( 0 === strpos( (string) $message, 'cURL error' ) ) {
			return 'HTTP_REQUEST_FAILED';
		}
		return 'UNKNOWN_ERROR';
	}

	/**
	 * Writes one line to the Zoho log (connect-with-zoho/zohoLogs), or the PHP error log.
	 * Callers pass IDs and codes only.
	 *
	 * @param string $line Log line.
	 */
	public static function log( $line ) {
		if ( class_exists( 'Connect_To_Zoho' ) && method_exists( 'Connect_To_Zoho', 'logMessage_zoho' ) ) {
			// logMessage_zoho() is an instance method; the constructor only registers hooks.
			static $logger = null;
			if ( null === $logger ) {
				$logger = new Connect_To_Zoho();
			}
			$logger->logMessage_zoho( $line );
			return;
		}
		error_log( '[HairyHair] ' . $line );
	}
}
