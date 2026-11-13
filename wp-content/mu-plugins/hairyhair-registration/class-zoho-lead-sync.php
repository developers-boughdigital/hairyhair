<?php
/**
 * Shared Zoho Lead matching, deduplication and retry handling for:
 * - lead-form submissions (theme: personal-information form, one local row per submission);
 * - the registration background job (HH_Registration_Zoho).
 *
 * Ownership rules (who may write to which Zoho Lead):
 * 1. Trusted Zoho IDs, in order: the submission's own zoho_id; zoho_id on earlier rows owned by
 *    the same WordPress user with the same email; the account's stored Zoho Lead ID (only when
 *    the submission email is the account email); the guest's own previous lead row, proven by the
 *    server-side guest-access token at submit time (passed in as prev_lead_id).
 * 2. A trusted ID is only used if it is not associated with a different WordPress user.
 * 3. Without a trusted ID, local rows / account metadata / a Zoho email search are only used to
 *    DETECT possible duplicates. They authorise an update only for an account whose email is
 *    verified (P2: emailed password set/reset link), with exactly one unconflicted match.
 * 4. Otherwise a possible duplicate is never updated and never replaced by a new Lead: the
 *    submission is kept locally and marked needs_review (or, for the registration job, waits for
 *    email verification).
 * 5. CREATE only when no possible duplicate exists. A CREATE whose outcome is unknown (timeout)
 *    is never repeated blindly: the retry searches Zoho first.
 *
 * All Zoho calls for one email run under a MySQL named lock, shared with the registration job.
 * Logs carry row/user IDs and error codes only; never answers, names, tokens or payloads.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HH_Zoho_Lead_Sync {

	const JOB_HOOK     = 'hh_lead_zoho_sync';
	const JOB_GROUP    = 'hairyhair-leads';
	const MAX_ATTEMPTS = 6;
	const LEAD_SOURCE  = 'Website - Quiz Questionnaire'; // Existing lead-form value, sent on CREATE only.

	const S_PENDING   = 'pending';
	const S_SYNCED    = 'synced';
	const S_RETRY     = 'retry';
	const S_UNCERTAIN = 'create_uncertain';
	const S_REVIEW    = 'needs_review';
	const S_FAILED    = 'failed';
	const S_SKIPPED   = 'skipped';

	/** user_id values at or above this are not WordPress user IDs (legacy rows stored Zoho IDs there). */
	const LEGACY_ID_FLOOR = 100000000000;

	/**
	 * @return int[] Delay in seconds before attempt n+1, after attempt n failed (bounded backoff).
	 */
	public static function delays() {
		return array( 1 => 5 * MINUTE_IN_SECONDS, 2 => 15 * MINUTE_IN_SECONDS, 3 => HOUR_IN_SECONDS, 4 => 4 * HOUR_IN_SECONDS, 5 => 12 * HOUR_IN_SECONDS );
	}

	/* ------------------------------------------------------------------ helpers */

	/**
	 * @return bool Whether the lead table has the v3 sync-state columns.
	 */
	public static function schema_ready() {
		global $wpdb;
		return HH_Registration_Lead::table_exists()
			&& (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `' . HH_Registration_Lead::table() . '` LIKE %s', 'zoho_sync_status' ) );
	}

	/**
	 * @param mixed $id Candidate.
	 * @return bool
	 */
	public static function valid_id( $id ) {
		return HH_Registration_Zoho::is_valid_zoho_id( $id );
	}

	/**
	 * Per-email lock shared by the lead form and the registration job.
	 *
	 * @param string $email Normalised email.
	 * @param int    $wait  Seconds to wait.
	 * @return bool
	 */
	public static function lock( $email, $wait = 0 ) {
		global $wpdb;
		return '1' === (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', self::lock_name( $email ), (int) $wait ) );
	}

	/**
	 * @param string $email Normalised email.
	 */
	public static function unlock( $email ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', self::lock_name( $email ) ) );
	}

	/**
	 * @param string $email Email.
	 * @return string
	 */
	protected static function lock_name( $email ) {
		return 'hh_zoho_' . md5( strtolower( trim( (string) $email ) ) );
	}

	/**
	 * @param int $user_id Candidate user ID from a lead row.
	 * @return int Real WordPress user ID, or 0 for guests and legacy (Zoho-ID-in-user_id) rows.
	 */
	public static function real_user_id( $user_id ) {
		$user_id = (int) $user_id;
		return ( $user_id > 0 && $user_id < self::LEGACY_ID_FLOOR && get_userdata( $user_id ) ) ? $user_id : 0;
	}

	/**
	 * Zoho IDs this identity may write to (rule 1), most specific first.
	 *
	 * @param int         $user_id      Real WordPress user ID or 0.
	 * @param string      $email        Normalised submission email.
	 * @param int         $prev_lead_id Guest's own previous lead (from the guest-access token), or 0.
	 * @param object|null $self_row     The submission row itself.
	 * @return string[]
	 */
	public static function trusted_ids( $user_id, $email, $prev_lead_id = 0, $self_row = null ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();
		$ids   = array();

		if ( $self_row && self::valid_id( $self_row->zoho_id ) ) {
			$ids[] = trim( (string) $self_row->zoho_id );
		}

		if ( $user_id > 0 ) {
			$owned = $wpdb->get_col( $wpdb->prepare(
				"SELECT zoho_id FROM `{$table}` WHERE user_id = %d AND email = %s AND zoho_id IS NOT NULL AND zoho_id <> '' ORDER BY ID DESC",
				$user_id,
				$email
			) );
			$ids = array_merge( $ids, (array) $owned );

			$user = get_userdata( $user_id );
			if ( $user && HH_Registration_Lead::normalize_email( $user->user_email ) === $email ) {
				$ids[] = (string) get_user_meta( $user_id, HH_Registration_Zoho::META_LEAD_ID, true );
			}
		}

		if ( $prev_lead_id > 0 ) {
			$prev = $wpdb->get_row( $wpdb->prepare( "SELECT user_id, email, zoho_id FROM `{$table}` WHERE ID = %d", $prev_lead_id ) );
			if ( $prev && HH_Registration_Lead::normalize_email( $prev->email ) === $email && in_array( (int) $prev->user_id, array( 0, (int) $user_id ), true ) ) {
				$ids[] = (string) $prev->zoho_id;
			}
		}

		return array_values( array_unique( array_map( 'trim', array_filter( $ids, array( __CLASS__, 'valid_id' ) ) ) ) );
	}

	/**
	 * Zoho IDs already associated locally with this email (rule 3: duplicate DETECTION only):
	 * other rows' zoho_id, legacy rows whose user_id holds a Zoho ID, and stored account Lead IDs.
	 *
	 * @param string $email          Normalised email.
	 * @param int    $exclude_lead_id Row to ignore.
	 * @return string[]
	 */
	public static function local_candidates( $email, $exclude_lead_id = 0 ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();
		$ids   = (array) $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT zoho_id FROM `{$table}` WHERE email = %s AND ID <> %d AND zoho_id IS NOT NULL AND zoho_id <> ''",
			$email,
			(int) $exclude_lead_id
		) );
		$legacy = (array) $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT l.user_id FROM `{$table}` l LEFT JOIN {$wpdb->users} u ON u.ID = l.user_id WHERE l.email = %s AND l.ID <> %d AND l.user_id >= %d AND u.ID IS NULL",
			$email,
			(int) $exclude_lead_id,
			self::LEGACY_ID_FLOOR
		) );
		$meta = (array) $wpdb->get_col( $wpdb->prepare(
			"SELECT um.meta_value FROM {$wpdb->usermeta} um JOIN {$wpdb->users} u ON u.ID = um.user_id WHERE um.meta_key = %s AND u.user_email = %s",
			HH_Registration_Zoho::META_LEAD_ID,
			$email
		) );
		return array_values( array_unique( array_map( 'trim', array_filter( array_merge( $ids, $legacy, $meta ), array( __CLASS__, 'valid_id' ) ) ) ) );
	}

	/**
	 * Rule 2: whether a Zoho Lead is associated with a WordPress user other than $user_id.
	 *
	 * @param string $zoho_id Zoho Lead ID.
	 * @param int    $user_id The writer (0 for guests: any user association is a conflict).
	 * @return bool
	 */
	public static function owned_by_other_user( $zoho_id, $user_id ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();
		$row   = $wpdb->get_var( $wpdb->prepare(
			"SELECT l.ID FROM `{$table}` l JOIN {$wpdb->users} u ON u.ID = l.user_id WHERE l.zoho_id = %s AND l.user_id <> %d LIMIT 1",
			$zoho_id,
			(int) $user_id
		) );
		if ( $row ) {
			return true;
		}
		return (bool) $wpdb->get_var( $wpdb->prepare(
			"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s AND user_id <> %d LIMIT 1",
			HH_Registration_Zoho::META_LEAD_ID,
			$zoho_id,
			(int) $user_id
		) );
	}

	/**
	 * Searches Zoho Leads by email (read-only GET) using the connector's saved domain/version/token.
	 *
	 * @param string $email Normalised email.
	 * @return string[]|WP_Error All matching Lead IDs ([] when none), or an error code.
	 */
	public static function search_ids( $email ) {
		$domain  = untrailingslashit( esc_url_raw( (string) get_option( 'zoho_api_domain' ) ) );
		$version = (string) get_option( 'zoho_api_version' );
		$token   = (string) get_option( 'zoho_access_token' );
		if ( '' === $domain || ! preg_match( '/^v\d+$/', $version ) || '' === $token ) {
			return new WP_Error( 'CONFIG_MISSING' );
		}

		$response = wp_remote_get(
			$domain . '/crm/' . $version . '/Leads/search?email=' . rawurlencode( $email ),
			array( 'timeout' => 15, 'headers' => array( 'Authorization' => 'Bearer ' . $token ) )
		);
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'HTTP_REQUEST_FAILED' );
		}
		$status = (int) wp_remote_retrieve_response_code( $response );
		if ( 204 === $status ) {
			return array();
		}
		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( 200 === $status && isset( $body['data'] ) && is_array( $body['data'] ) ) {
			$ids = array();
			foreach ( $body['data'] as $record ) {
				if ( isset( $record['id'] ) && self::valid_id( $record['id'] ) ) {
					$ids[] = trim( (string) $record['id'] );
				}
			}
			return array_values( array_unique( $ids ) );
		}
		$code = ( is_array( $body ) && isset( $body['code'] ) && preg_match( '/^[A-Z0-9_]{1,64}$/', (string) $body['code'] ) ) ? $body['code'] : 'HTTP_' . $status;
		return new WP_Error( $code );
	}

	/* ------------------------------------------------------------------ decision */

	/**
	 * Decides what to do for one identity. Shared by lead form and registration job.
	 *
	 * @param array $c {
	 *   email: string, user_id: int, verified: bool, trusted: string[], exclude_lead_id: int,
	 *   uncertain: bool (previous CREATE outcome unknown), account: bool (registration job),
	 *   assign_id: string (staff decision, manual reprocess), allow_create: bool (staff decision)
	 * }
	 * @return array{action: string, id?: string, reason?: string}
	 *         action: update | create | adopt | review | wait | retry
	 */
	public static function decide( array $c ) {
		$c = array_merge( array( 'trusted' => array(), 'exclude_lead_id' => 0, 'uncertain' => false, 'account' => false, 'assign_id' => '', 'allow_create' => false, 'verified' => false, 'user_id' => 0 ), $c );
		$user_id = (int) $c['user_id'];

		if ( '' !== $c['assign_id'] ) {
			if ( ! self::valid_id( $c['assign_id'] ) ) {
				return array( 'action' => 'review', 'reason' => 'INVALID_ASSIGNED_ID' );
			}
			return self::owned_by_other_user( $c['assign_id'], $user_id )
				? array( 'action' => 'review', 'reason' => 'OWNED_BY_OTHER_USER' )
				: array( 'action' => 'update', 'id' => $c['assign_id'] );
		}

		if ( $c['trusted'] ) {
			$id = $c['trusted'][0];
			if ( count( $c['trusted'] ) > 1 ) {
				HH_Registration_Zoho::log( sprintf( 'HairyHair Zoho sync user #%d: several trusted Zoho Leads (%d); using the most specific one.', $user_id, count( $c['trusted'] ) ) );
			}
			return self::owned_by_other_user( $id, $user_id )
				? array( 'action' => 'review', 'reason' => 'OWNED_BY_OTHER_USER' )
				: array( 'action' => 'update', 'id' => $id );
		}

		$local  = self::local_candidates( $c['email'], $c['exclude_lead_id'] );
		$search = self::search_ids( $c['email'] );
		if ( is_wp_error( $search ) ) {
			return array( 'action' => 'retry', 'reason' => 'SEARCH_' . $search->get_error_code() );
		}

		if ( $c['uncertain'] ) {
			// Our earlier CREATE may have landed. Before it we found no possible duplicate, so a
			// single new, locally unassociated match is that Lead; nothing found = it did not land.
			$new = array_values( array_diff( $search, $local ) );
			if ( ! $search ) {
				return array( 'action' => 'create' );
			}
			if ( 1 === count( $search ) && 1 === count( $new ) && ! self::owned_by_other_user( $new[0], $user_id ) ) {
				return array( 'action' => 'adopt', 'id' => $new[0] );
			}
			return array( 'action' => 'review', 'reason' => 'UNCERTAIN_CREATE_MATCHES' );
		}

		$candidates = array_values( array_unique( array_merge( $local, $search ) ) );
		if ( ! $candidates || $c['allow_create'] ) {
			return array( 'action' => 'create' );
		}
		if ( ! $c['verified'] ) {
			return $c['account']
				? array( 'action' => 'wait', 'reason' => 'AWAITING_VERIFICATION' )
				: array( 'action' => 'review', 'reason' => 'POSSIBLE_DUPLICATE' );
		}
		foreach ( $candidates as $id ) {
			if ( self::owned_by_other_user( $id, $user_id ) ) {
				return array( 'action' => 'review', 'reason' => 'OWNED_BY_OTHER_USER' );
			}
		}
		return 1 === count( $candidates )
			? array( 'action' => 'update', 'id' => $candidates[0] )
			: array( 'action' => 'review', 'reason' => 'MULTIPLE_MATCHES' );
	}

	/**
	 * Classifies a connector exception (Connect_To_Zoho::updateInsertCRMrecord throws the transport
	 * error message, or 'Error inserting/updating record: ' + Zoho's JSON response).
	 *
	 * @param Throwable $e      Exception.
	 * @param string    $method POST (create) or PUT (update).
	 * @return array{kind: string, code: string} kind: retry | permanent | uncertain | target_unavailable
	 */
	public static function classify( $e, $method ) {
		$msg  = (string) $e->getMessage();
		$json = null;
		$pos  = strpos( $msg, '{' );
		if ( false !== $pos ) {
			$json = json_decode( substr( $msg, $pos ), true );
		}

		if ( is_array( $json ) ) {
			$rec  = ( isset( $json['data'][0] ) && is_array( $json['data'][0] ) ) ? $json['data'][0] : $json;
			$code = ( isset( $rec['code'] ) && preg_match( '/^[A-Z0-9_]{1,64}$/', (string) $rec['code'] ) ) ? $rec['code'] : 'UNKNOWN_ERROR';
			$api  = isset( $rec['details']['api_name'] ) ? (string) $rec['details']['api_name'] : '';

			if ( 'PUT' === $method && ( in_array( $code, array( 'RECORD_NOT_FOUND', 'ENTITY_ID_INVALID', 'INVALID_URL_PATTERN' ), true ) || ( 'INVALID_DATA' === $code && 'id' === $api ) ) ) {
				return array( 'kind' => 'target_unavailable', 'code' => 'ZOHO_LEAD_UNAVAILABLE' ); // converted, deleted or merged
			}
			if ( in_array( $code, array( 'INVALID_DATA', 'MANDATORY_NOT_FOUND', 'DUPLICATE_DATA', 'MULTIPLE_OR_MULTI_ERRORS', 'LIMIT_EXCEEDED', 'INVALID_MODULE', 'NOT_SUPPORTED', 'AUTHENTICATION_FAILURE', 'OAUTH_SCOPE_MISMATCH', 'NO_PERMISSION', 'NOT_ALLOWED' ), true ) ) {
				return array( 'kind' => 'permanent', 'code' => $code );
			}
			// INVALID_TOKEN / INVALID_OAUTHTOKEN: the connector's cron refreshes the token, so retry.
			return array( 'kind' => 'retry', 'code' => $code );
		}

		if ( preg_match( '/cURL error (\d+)/', $msg, $m ) ) {
			if ( in_array( (int) $m[1], array( 6, 7 ), true ) ) {
				return array( 'kind' => 'retry', 'code' => 'HTTP_CONNECT_FAILED' ); // never reached Zoho
			}
			$code = 28 === (int) $m[1] ? 'HTTP_TIMEOUT' : 'HTTP_REQUEST_FAILED';
			return array( 'kind' => 'POST' === $method ? 'uncertain' : 'retry', 'code' => $code );
		}
		return array( 'kind' => 'POST' === $method ? 'uncertain' : 'retry', 'code' => 'UNKNOWN_ERROR' );
	}

	/* ------------------------------------------------------------------ lead form sync */

	/**
	 * Lead-form Zoho field mapping, rebuilt from the stored row (so background retries send the same
	 * data). Step 2 / Step 3 API names are the 2026-11-12 implementation's, checked then against the
	 * Zoho Leads field metadata. Lead_Source is only sent on CREATE.
	 *
	 * - Allergies Yes/No goes to its own picklist (the old mapping wrote it into
	 *   Explain_Other_Medications_or_Supplements).
	 * - Explanations go to Rich-Text fields as escaped HTML with <br> line breaks, and are left out
	 *   when empty: whether Zoho clears a Rich-Text field on null/"" is unverified, so an existing
	 *   Zoho explanation is never wiped.
	 * - Consent_To_Contact is a JSON boolean from the stored explicit choice; omitted for rows saved
	 *   before consent was a real choice (NULL), so old leads are never reported as consenting.
	 * - Step 2 picklists are omitted when empty (rows saved before they existed).
	 *
	 * @param object $row Lead row.
	 * @return array
	 */
	public static function lead_form_fields( $row ) {
		$fields = array(
			'First_Name'                                     => $row->first_name,
			'Last_Name'                                      => $row->last_name,
			'Email'                                          => $row->email,
			'Phone'                                          => $row->phone,
			'Date_Of_Birth'                                  => date( 'Y-m-d', strtotime( $row->dob ) ),
			'Gender'                                         => $row->gender,
			'How_long_have_you_been_experiencing_hair_loss'  => $row->hair_loss_duration,
			'What_results_are_you_hoping_to_achieve'         => $row->expacted_results,
			'Any_known_Allergies_to_medications_supplements' => $row->allergies_or_sensitivities,
			'Are_you_currently_taking_any_other_medications' => $row->taking_any_medications,
			'Do_you_have_a_history_with_high_blood_pressure' => $row->high_blood_pressure,
			'Lead_Source'                                    => self::LEAD_SOURCE,
			'Website'                                        => get_site_url() . '/lets-get-started',
		);
		$optional = array(
			'What_is_your_age_group'                              => isset( $row->age_group ) ? (string) $row->age_group : '',
			'Which_of_the_following_best_describes_your_current' => isset( $row->current_hair_loss ) ? (string) $row->current_hair_loss : '',
			'Explain_Allergies_or_Sensitivities'                  => isset( $row->explain_allergies ) ? (string) $row->explain_allergies : '',
			'Explain_Other_Medications_or_Supplements'            => isset( $row->explain_medications ) ? (string) $row->explain_medications : '',
		);
		foreach ( $optional as $api => $value ) {
			if ( '' === trim( $value ) ) {
				continue;
			}
			$fields[ $api ] = 0 === strpos( $api, 'Explain_' ) ? nl2br( esc_html( $value ), false ) : $value;
		}
		if ( isset( $row->consent_to_contact ) && null !== $row->consent_to_contact ) {
			$fields['Consent_To_Contact'] = (bool) (int) $row->consent_to_contact;
		}
		return $fields;
	}

	/**
	 * Syncs one lead-form submission to Zoho. Safe to call repeatedly (idempotent per row).
	 *
	 * @param int   $lead_id Lead row ID.
	 * @param array $ctx {
	 *   prev_lead_id: int (guest's own previous lead, proven by the guest-access token at submit time),
	 *   lock_wait: int, manual: bool, assign_id: string, allow_create: bool
	 * }
	 * @return string Resulting status (synced, retry, create_uncertain, needs_review, failed, skipped, ...).
	 */
	public static function sync_lead( $lead_id, array $ctx = array() ) {
		global $wpdb;
		$ctx     = array_merge( array( 'prev_lead_id' => 0, 'lock_wait' => 0, 'manual' => false, 'assign_id' => '', 'allow_create' => false ), $ctx );
		$lead_id = absint( $lead_id );
		$table   = HH_Registration_Lead::table();

		if ( ! $lead_id || ! self::schema_ready() ) {
			return 'schema_not_ready';
		}
		$row = self::row( $lead_id );
		if ( ! $row ) {
			return 'missing';
		}
		if ( ! $ctx['manual'] && ( ( self::S_SYNCED === $row->zoho_sync_status && self::valid_id( $row->zoho_id ) ) || in_array( $row->zoho_sync_status, array( self::S_REVIEW, self::S_FAILED ), true ) ) ) {
			return (string) $row->zoho_sync_status;
		}
		if ( ! HH_Registration_Zoho::connector_available() ) {
			self::set_state( $lead_id, self::S_SKIPPED, 'CONNECTOR_INACTIVE' );
			return self::S_SKIPPED;
		}
		$email = HH_Registration_Lead::normalize_email( $row->email );
		if ( '' === $email ) {
			self::set_state( $lead_id, self::S_FAILED, 'INVALID_EMAIL' );
			return self::S_FAILED;
		}

		if ( ! self::lock( $email, (int) $ctx['lock_wait'] ) ) {
			// Another sync for this email (e.g. the registration job) is running: try again shortly.
			self::set_state( $lead_id, self::S_RETRY, 'LOCKED' );
			self::queue( $lead_id, (int) $ctx['prev_lead_id'], MINUTE_IN_SECONDS );
			return self::S_RETRY;
		}

		try {
			$row = self::row( $lead_id ); // Re-read under the lock.
			if ( ! $ctx['manual'] && self::S_SYNCED === $row->zoho_sync_status && self::valid_id( $row->zoho_id ) ) {
				return self::S_SYNCED;
			}
			$attempt = (int) $row->zoho_sync_attempts + 1;
			$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET zoho_sync_attempts = %d, zoho_sync_at = %s WHERE ID = %d", $attempt, current_time( 'mysql', true ), $lead_id ) );

			$user_id  = self::real_user_id( $row->user_id );
			$user     = $user_id ? get_userdata( $user_id ) : false;
			$verified = $user && HH_Registration_Lead::normalize_email( $user->user_email ) === $email && HH_Registration_Lead::is_email_verified( $user_id );

			$decision = self::decide( array(
				'email'           => $email,
				'user_id'         => $user_id,
				'verified'        => $verified,
				'trusted'         => self::trusted_ids( $user_id, $email, (int) $ctx['prev_lead_id'], $row ),
				'exclude_lead_id' => $lead_id,
				'uncertain'       => self::S_UNCERTAIN === $row->zoho_sync_status,
				'assign_id'       => (string) $ctx['assign_id'],
				'allow_create'    => (bool) $ctx['allow_create'],
			) );

			return self::apply( $row, $decision, $attempt, (int) $ctx['prev_lead_id'] );
		} finally {
			self::unlock( $email );
		}
	}

	/**
	 * Executes a decision for a lead row. Caller holds the email lock.
	 *
	 * @param object $row      Lead row.
	 * @param array  $decision From decide().
	 * @param int    $attempt  Attempt number.
	 * @param int    $prev     prev_lead_id (kept for retries).
	 * @return string Status.
	 */
	protected static function apply( $row, array $decision, $attempt, $prev ) {
		$lead_id = (int) $row->ID;
		$fields  = self::lead_form_fields( $row );

		switch ( $decision['action'] ) {
			case 'adopt':
				self::set_state( $lead_id, self::S_SYNCED, null, $decision['id'] );
				self::log( $lead_id, 'adopted Zoho Lead ' . $decision['id'] . ' after an uncertain create' );
				return self::S_SYNCED;

			case 'review':
				self::set_state( $lead_id, self::S_REVIEW, $decision['reason'] );
				self::log( $lead_id, 'needs review (' . $decision['reason'] . '); no Zoho write' );
				return self::S_REVIEW;

			case 'retry':
				return self::retry_or_fail( $lead_id, $prev, $attempt, $decision['reason'] );

			case 'update':
				unset( $fields['Lead_Source'] ); // Keep the Lead's original source.
				return self::call_zoho( $lead_id, $prev, $attempt, 'PUT', $decision['id'], $fields );

			case 'create':
				// Recorded before the call: if the request dies mid-flight the row shows the uncertainty.
				self::set_state( $lead_id, self::S_UNCERTAIN, 'CREATE_IN_PROGRESS' );
				return self::call_zoho( $lead_id, $prev, $attempt, 'POST', '', $fields );
		}
		return self::retry_or_fail( $lead_id, $prev, $attempt, 'UNKNOWN_DECISION' );
	}

	/**
	 * @return string Status after the call.
	 */
	protected static function call_zoho( $lead_id, $prev, $attempt, $method, $target, array $fields ) {
		try {
			$zoho     = new Connect_To_Zoho();
			$returned = $zoho->updateInsertCRMrecord( 'Leads', $target, $fields );
		} catch ( \Throwable $e ) {
			$c = self::classify( $e, $method );
			switch ( $c['kind'] ) {
				case 'uncertain':
					self::set_state( $lead_id, self::S_UNCERTAIN, $c['code'] );
					self::log( $lead_id, 'create outcome unknown (' . $c['code'] . '); retry will search before creating' );
					return self::retry_or_fail( $lead_id, $prev, $attempt, $c['code'], self::S_UNCERTAIN );
				case 'target_unavailable':
					self::set_state( $lead_id, self::S_REVIEW, $c['code'] );
					self::log( $lead_id, 'Zoho Lead ' . $target . ' unavailable (converted/deleted?); needs review, no replacement created' );
					return self::S_REVIEW;
				case 'permanent':
					self::set_state( $lead_id, self::S_FAILED, $c['code'] );
					self::log( $lead_id, 'permanent Zoho error ' . $c['code'] . '; not retried' );
					return self::S_FAILED;
				default:
					return self::retry_or_fail( $lead_id, $prev, $attempt, $c['code'] );
			}
		}

		if ( ! self::valid_id( $returned ) ) {
			return 'POST' === $method
				? self::retry_or_fail( $lead_id, $prev, $attempt, 'INVALID_LEAD_ID', self::S_UNCERTAIN )
				: self::retry_or_fail( $lead_id, $prev, $attempt, 'INVALID_LEAD_ID' );
		}
		$returned = trim( (string) $returned );
		self::set_state( $lead_id, self::S_SYNCED, null, $returned );
		self::log( $lead_id, sprintf( 'Zoho Lead %s %s', $returned, 'POST' === $method ? 'created' : 'updated' ) );
		return self::S_SYNCED;
	}

	/**
	 * Queues the next attempt with backoff, or marks the row failed after MAX_ATTEMPTS.
	 *
	 * @return string Status.
	 */
	protected static function retry_or_fail( $lead_id, $prev, $attempt, $code, $status = self::S_RETRY ) {
		if ( $attempt >= self::MAX_ATTEMPTS ) {
			self::set_state( $lead_id, self::S_FAILED, $code );
			self::log( $lead_id, sprintf( 'Zoho sync failed (%s), attempt %d of %d; giving up', $code, $attempt, self::MAX_ATTEMPTS ) );
			return self::S_FAILED;
		}
		$delays = self::delays();
		self::set_state( $lead_id, $status, $code );
		self::queue( $lead_id, $prev, isset( $delays[ $attempt ] ) ? $delays[ $attempt ] : 12 * HOUR_IN_SECONDS );
		self::log( $lead_id, sprintf( 'Zoho sync failed (%s), attempt %d of %d; retry queued', $code, $attempt, self::MAX_ATTEMPTS ) );
		return $status;
	}

	/**
	 * One pending job per lead (args are fixed per lead, so duplicates are detected).
	 *
	 * @return bool
	 */
	public static function queue( $lead_id, $prev, $delay ) {
		$args = array( (int) $lead_id, (int) $prev );
		if ( function_exists( 'as_schedule_single_action' ) ) {
			if ( function_exists( 'as_has_scheduled_action' ) && as_has_scheduled_action( self::JOB_HOOK, $args, self::JOB_GROUP ) ) {
				return true;
			}
			return (bool) as_schedule_single_action( time() + (int) $delay, self::JOB_HOOK, $args, self::JOB_GROUP );
		}
		if ( wp_next_scheduled( self::JOB_HOOK, $args ) ) {
			return true;
		}
		return true === wp_schedule_single_event( time() + (int) $delay, self::JOB_HOOK, $args, true );
	}

	/**
	 * Background job callback.
	 *
	 * @param int $lead_id Lead row ID.
	 * @param int $prev    prev_lead_id.
	 */
	public static function run_job( $lead_id, $prev = 0 ) {
		self::sync_lead( $lead_id, array( 'prev_lead_id' => (int) $prev ) );
	}

	/**
	 * After the account email is verified (and its guest rows linked by P2), re-evaluate this
	 * user's submissions that were held back only because ownership could not be proven.
	 *
	 * @param int $user_id User ID.
	 * @return int Number of rows queued.
	 */
	public static function requeue_after_verification( $user_id ) {
		global $wpdb;
		if ( ! self::schema_ready() ) {
			return 0;
		}
		$table = HH_Registration_Lead::table();
		$ids   = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM `{$table}` WHERE user_id = %d AND zoho_sync_status = %s AND zoho_sync_error = %s",
			absint( $user_id ),
			self::S_REVIEW,
			'POSSIBLE_DUPLICATE'
		) );
		foreach ( $ids as $id ) {
			self::set_state( (int) $id, self::S_RETRY, 'VERIFIED_REQUEUE' );
			self::queue( (int) $id, 0, 0 );
		}
		return count( $ids );
	}

	/* ------------------------------------------------------------------ state */

	/**
	 * @return object|null
	 */
	protected static function row( $lead_id ) {
		global $wpdb;
		return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM `' . HH_Registration_Lead::table() . '` WHERE ID = %d', $lead_id ) );
	}

	/**
	 * Updates sync state. zoho_id is only ever filled when empty (never overwritten).
	 */
	public static function set_state( $lead_id, $status, $error = null, $zoho_id = null ) {
		global $wpdb;
		$table = HH_Registration_Lead::table();
		if ( null !== $zoho_id ) {
			$wpdb->query( $wpdb->prepare(
				"UPDATE `{$table}` SET zoho_sync_status = %s, zoho_sync_error = NULL, zoho_id = IF(zoho_id IS NULL OR zoho_id = '', %s, zoho_id) WHERE ID = %d",
				$status,
				$zoho_id,
				$lead_id
			) );
			return;
		}
		$wpdb->query( $wpdb->prepare(
			"UPDATE `{$table}` SET zoho_sync_status = %s, zoho_sync_error = " . ( null === $error ? 'NULL' : '%s' ) . ' WHERE ID = %d',
			...( null === $error ? array( $status, $lead_id ) : array( $status, substr( (string) $error, 0, 64 ), $lead_id ) )
		) );
	}

	/**
	 * @param int    $lead_id Row ID.
	 * @param string $message IDs/codes only.
	 */
	protected static function log( $lead_id, $message ) {
		HH_Registration_Zoho::log( sprintf( 'HairyHair lead #%d: %s.', $lead_id, $message ) );
	}
}
