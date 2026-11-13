<?php
/**
 * Guest access tokens.
 *
 * Replaces the unsigned `lead_user_email` / `quiz_user_email` cookies, which let anyone who set
 * the cookie to another person's email read that person's lead/quiz details.
 *
 * - The browser gets a random 256-bit token in an HttpOnly cookie.
 * - The server stores only HMAC-SHA256(token, auth salt) as the key of a transient holding:
 *   email, the exact lead/quiz row IDs this guest created ("grants" = purpose => row ID), expiry.
 * - A row is only returned when its ID, email and guest status (user_id NULL/0) all still match.
 *   Rows linked to an account are never readable through a guest token.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HH_GUEST_ACCESS_COOKIE = 'hh_guest_access';
const HH_GUEST_ACCESS_TTL    = 30 * DAY_IN_SECONDS;

/**
 * @param string $token Raw token.
 * @return string Transient name derived from the token hash.
 */
function hh_guest_access_key( $token ) {
	return 'hh_ga_' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
}

/**
 * @return string Raw token from the cookie if well-formed, else ''.
 */
function hh_guest_access_token() {
	$token = isset( $_COOKIE[ HH_GUEST_ACCESS_COOKIE ] ) ? (string) $_COOKIE[ HH_GUEST_ACCESS_COOKIE ] : '';
	return preg_match( '/^[a-f0-9]{64}$/', $token ) ? $token : '';
}

/**
 * The current guest's access record, or null if the token is missing, unknown or expired.
 *
 * @return array|null { email: string, grants: array<string,int>, expires: int }
 */
function hh_guest_access_current() {
	$token = hh_guest_access_token();
	if ( '' === $token ) {
		return null;
	}
	$record = get_transient( hh_guest_access_key( $token ) );
	if ( ! is_array( $record ) || empty( $record['email'] ) || empty( $record['expires'] ) || (int) $record['expires'] < time() || empty( $record['grants'] ) || ! is_array( $record['grants'] ) ) {
		return null;
	}
	return $record;
}

/**
 * Lets the current guest read back a row they just created/updated.
 *
 * @param string $email   Email of the row (normalised).
 * @param string $purpose 'lead' or 'quiz'.
 * @param int    $row_id  Row primary key.
 */
function hh_guest_access_grant( $email, $purpose, $row_id ) {
	$email  = strtolower( trim( (string) $email ) );
	$row_id = absint( $row_id );
	if ( is_user_logged_in() || ! in_array( $purpose, array( 'lead', 'quiz' ), true ) || ! $row_id || ! is_email( $email ) ) {
		return;
	}

	$record = hh_guest_access_current();
	$token  = hh_guest_access_token();

	if ( ! $record || $record['email'] !== $email ) {
		$record = array( 'email' => $email, 'grants' => array() ); // Different person/email: start over.
	}
	// Rotate on every grant: the previous token stops working immediately.
	if ( '' !== $token ) {
		delete_transient( hh_guest_access_key( $token ) );
	}
	$token = bin2hex( random_bytes( 32 ) );

	$record['grants'][ $purpose ] = $row_id;
	$record['expires']            = time() + HH_GUEST_ACCESS_TTL;
	set_transient( hh_guest_access_key( $token ), $record, HH_GUEST_ACCESS_TTL );

	hh_guest_access_set_cookie( $token, $record['expires'] );
}

/**
 * Removes the guest token (server record and cookie).
 */
function hh_guest_access_clear() {
	$token = hh_guest_access_token();
	if ( '' !== $token ) {
		delete_transient( hh_guest_access_key( $token ) );
	}
	if ( isset( $_COOKIE[ HH_GUEST_ACCESS_COOKIE ] ) ) {
		hh_guest_access_set_cookie( '', time() - HOUR_IN_SECONDS );
	}
}

/**
 * @param string $value   Cookie value.
 * @param int    $expires Unix expiry.
 */
function hh_guest_access_set_cookie( $value, $expires ) {
	if ( ! headers_sent() ) {
		setcookie(
			HH_GUEST_ACCESS_COOKIE,
			$value,
			array(
				'expires'  => $expires,
				'path'     => COOKIEPATH ? COOKIEPATH : '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);
	}
	if ( '' === $value ) {
		unset( $_COOKIE[ HH_GUEST_ACCESS_COOKIE ] );
	} else {
		$_COOKIE[ HH_GUEST_ACCESS_COOKIE ] = $value;
	}
}

/**
 * The guest's own row for a purpose, re-checked against the database every time.
 *
 * @param string $purpose 'lead' or 'quiz'.
 * @return array|null Row (ARRAY_A) or null.
 */
function hh_guest_access_row( $purpose ) {
	global $wpdb;

	$record = hh_guest_access_current();
	if ( ! $record || empty( $record['grants'][ $purpose ] ) ) {
		return null;
	}

	$tables = array( 'lead' => 'hh_lead_results', 'quiz' => 'hh_quiz_results' );
	if ( ! isset( $tables[ $purpose ] ) ) {
		return null;
	}

	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}{$tables[ $purpose ]} WHERE ID = %d AND email = %s AND ( user_id IS NULL OR user_id = 0 )",
			(int) $record['grants'][ $purpose ],
			$record['email']
		),
		ARRAY_A
	);

	return $row ? $row : null;
}
