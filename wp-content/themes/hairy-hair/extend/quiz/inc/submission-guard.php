<?php
/**
 * Server-side protection against accidental duplicate quiz / lead-form submissions.
 *
 * - The form fetches a submission token from admin-ajax (`hh_submission_token`), so it is fresh even
 *   when the page itself is served from a cache. Token = random 128 bits + issue time + HMAC (site
 *   salt); forged or expired (> 2 h) tokens are ignored.
 * - A submission is a duplicate if the same token was already used for the same payload (retry after
 *   a timeout, double click), or if the identical payload for the same email was saved in the last
 *   2 minutes (protects clients without a token). The stored result is returned; nothing is saved
 *   again and Zoho is not called again.
 * - A different payload, or the same answers on another occasion (new token, > 2 min), is a new
 *   submission and gets its own history row.
 * - Check + insert + record run under a per-email MySQL lock, so concurrent identical requests
 *   cannot both insert. The lead lock name is the one HH_Registration_Lead::associate() uses.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hairy_Submission_Guard {

	const TOKEN_FIELD = 'hh_submission_token';
	const TOKEN_TTL   = 2 * HOUR_IN_SECONDS;
	const WINDOW      = 2 * MINUTE_IN_SECONDS;
	const LOCK_WAIT   = 10;

	public static function init() {
		add_action( 'wp_ajax_hh_submission_token', array( __CLASS__, 'ajax_token' ) );
		add_action( 'wp_ajax_nopriv_hh_submission_token', array( __CLASS__, 'ajax_token' ) );
	}

	public static function ajax_token() {
		nocache_headers();
		wp_send_json_success( array( 'token' => self::new_token() ) );
	}

	/**
	 * @return string
	 */
	public static function new_token() {
		$rand = bin2hex( random_bytes( 16 ) );
		$time = (string) time();
		return $rand . '.' . $time . '.' . self::sign( $rand . '.' . $time );
	}

	/**
	 * @param string $token Posted token.
	 * @return bool
	 */
	public static function valid_token( $token ) {
		if ( ! is_string( $token ) || ! preg_match( '/^([a-f0-9]{32})\.(\d{10})\.([a-f0-9]{32})$/', $token, $m ) ) {
			return false;
		}
		$age = time() - (int) $m[2];
		return $age >= -60 && $age <= self::TOKEN_TTL && hash_equals( self::sign( $m[1] . '.' . $m[2] ), $m[3] );
	}

	protected static function sign( $data ) {
		return substr( hash_hmac( 'sha256', 'hh-submission|' . $data, wp_salt( 'nonce' ) ), 0, 32 );
	}

	/**
	 * Starts a guarded submission. On success the caller MUST call complete() or release().
	 *
	 * @param string $form   'lead' or 'quiz'.
	 * @param string $email  Submission email.
	 * @param array  $fields Fields that define "the same payload" (no nonce/token).
	 * @param bool   $payload_window Also treat an identical payload within 2 minutes as a repeat, even
	 *                               without the same token. Off for responses that depend on who is asking.
	 * @return array|WP_Error ['duplicate' => record] | ['guard' => state] | WP_Error('busy')
	 */
	public static function begin( $form, $email, array $fields, $payload_window = true ) {
		global $wpdb;
		$email = strtolower( trim( (string) $email ) );
		$lock  = ( 'lead' === $form ? 'hh_lead_' : 'hh_quiz_' ) . md5( $email );
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock, self::LOCK_WAIT ) ) ) {
			return new WP_Error( 'busy' );
		}

		ksort( $fields );
		$fp    = hash_hmac( 'sha256', $form . '|' . $email . '|' . wp_json_encode( $fields ), wp_salt( 'nonce' ) );
		$token = isset( $_POST[ self::TOKEN_FIELD ] ) ? wp_unslash( $_POST[ self::TOKEN_FIELD ] ) : '';
		$token = self::valid_token( $token ) ? $token : '';
		$state = array( 'lock' => $lock, 'fp' => $fp, 'token_key' => '' === $token ? '' : self::key( 'tok', $token ), 'fp_key' => $payload_window ? self::key( 'fp', $fp ) : '' );

		$by_token = $state['token_key'] ? get_transient( $state['token_key'] ) : false;
		$record   = ( is_array( $by_token ) && isset( $by_token['fp'] ) && hash_equals( $by_token['fp'], $fp ) ) ? $by_token : ( $state['fp_key'] ? get_transient( $state['fp_key'] ) : false );
		if ( is_array( $record ) && ! empty( $record['row_id'] ) ) {
			self::release( $state );
			return array( 'duplicate' => $record );
		}
		return array( 'guard' => $state );
	}

	/**
	 * Records the saved row as the result for this token/payload and releases the lock.
	 *
	 * @param array  $state   From begin().
	 * @param int    $row_id  Saved row.
	 * @param string $message Response message for repeats.
	 * @param array  $data    Extra response data replayed for repeats (e.g. the Step 1 state).
	 */
	public static function complete( array $state, $row_id, $message, array $data = array() ) {
		$record = array( 'row_id' => (int) $row_id, 'fp' => $state['fp'], 'message' => (string) $message, 'data' => $data );
		if ( $state['token_key'] ) {
			set_transient( $state['token_key'], $record, self::TOKEN_TTL );
		}
		if ( $state['fp_key'] ) {
			set_transient( $state['fp_key'], $record, self::WINDOW );
		}
		self::release( $state );
	}

	/**
	 * @param array $state From begin().
	 */
	public static function release( array $state ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $state['lock'] ) );
	}

	protected static function key( $type, $value ) {
		return 'hh_sub_' . $type . '_' . substr( hash_hmac( 'sha256', $value, wp_salt( 'nonce' ) ), 0, 40 );
	}
}

Hairy_Submission_Guard::init();
