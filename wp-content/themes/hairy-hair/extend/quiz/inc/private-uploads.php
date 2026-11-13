<?php
/**
 * Private scalp-photo uploads for the hair quiz.
 *
 * Replaces the old upload handler, which put any file from anyone into the public Media Library.
 *
 * - Only JPEG/PNG/WebP images, max 5 MB and 40 megapixels; the image is re-encoded, which drops
 *   EXIF data (including GPS) and anything that is not image data.
 * - Files are not WordPress attachments. They are stored under an HMAC-derived file name in a
 *   private folder (HH_PRIVATE_UPLOADS_DIR, ideally outside the web root; default
 *   uploads/hh-private with deny rules for Apache). The public upload ID cannot be turned into
 *   the file name without the site's auth salt.
 * - Files are only served through admin-ajax `hh_view_upload`, to the uploader (WordPress user ID,
 *   or a random HttpOnly owner cookie for guests) and to shop staff (manage_woocommerce).
 * - Rate limits per client IP, per owner and per site per day.
 * - Logged-in uploads also need a nonce (CSRF). Guests are authorised by the owner cookie, not by
 *   a nonce (a logged-out nonce is the same for every visitor and breaks on cached pages).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const HH_UPLOAD_MAX_BYTES      = 5 * MB_IN_BYTES;
const HH_UPLOAD_MAX_PIXELS     = 40000000;
const HH_UPLOAD_OWNER_COOKIE   = 'hh_upload_owner';
const HH_UPLOAD_LIMIT_IP       = 20;  // per hour
const HH_UPLOAD_LIMIT_OWNER    = 5;   // per 10 minutes
const HH_UPLOAD_LIMIT_SITE_DAY = 500; // per day, all visitors

/**
 * @return string[] Allowed MIME type => file extension.
 */
function hh_upload_allowed_types() {
	return array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/webp' => 'webp',
	);
}

/**
 * Private storage folder; created on first use with deny rules.
 *
 * @return string Absolute path without trailing slash.
 */
function hh_upload_dir() {
	if ( defined( 'HH_PRIVATE_UPLOADS_DIR' ) && HH_PRIVATE_UPLOADS_DIR ) {
		$dir = untrailingslashit( HH_PRIVATE_UPLOADS_DIR );
	} else {
		$uploads = wp_upload_dir( null, false );
		$dir     = untrailingslashit( $uploads['basedir'] ) . '/hh-private';
	}

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	if ( ! file_exists( $dir . '/index.php' ) ) {
		@file_put_contents( $dir . '/index.php', "<?php\n// Silence is golden.\n" );
	}
	if ( ! file_exists( $dir . '/.htaccess' ) ) {
		@file_put_contents( $dir . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" );
	}
	return $dir;
}

/**
 * @param string $id Public upload ID (32 hex).
 * @return string File path stem (no extension) derived from the ID with the auth salt.
 */
function hh_upload_path_stem( $id ) {
	return hh_upload_dir() . '/' . hash_hmac( 'sha256', 'hh-upload|' . $id, wp_salt( 'auth' ) );
}

/**
 * @param mixed $id Candidate ID.
 * @return bool
 */
function hh_upload_is_id( $id ) {
	return is_string( $id ) && (bool) preg_match( '/^[a-f0-9]{32}$/', $id );
}

/**
 * Identity of the current uploader: "u:<user id>" or "g:<hmac of guest owner token>".
 *
 * @param bool $create Create a guest owner token (and cookie) if there is none.
 * @return string '' when a guest has no token and $create is false.
 */
function hh_upload_owner( $create = false ) {
	$user_id = get_current_user_id();
	if ( $user_id > 0 ) {
		return 'u:' . $user_id;
	}

	$token = isset( $_COOKIE[ HH_UPLOAD_OWNER_COOKIE ] ) ? (string) $_COOKIE[ HH_UPLOAD_OWNER_COOKIE ] : '';
	if ( ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
		if ( ! $create ) {
			return '';
		}
		$token = bin2hex( random_bytes( 32 ) );
		if ( ! headers_sent() ) {
			setcookie(
				HH_UPLOAD_OWNER_COOKIE,
				$token,
				array(
					'expires'  => time() + 30 * DAY_IN_SECONDS,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
		$_COOKIE[ HH_UPLOAD_OWNER_COOKIE ] = $token;
	}

	return 'g:' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
}

/**
 * @return string Client IP used for rate limiting (filterable for proxies/CDNs).
 */
function hh_upload_client_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	return (string) apply_filters( 'hh_upload_client_ip', $ip );
}

/**
 * Increments a counter; returns false once the limit is exceeded.
 *
 * @param string $key    Counter name.
 * @param int    $limit  Max count per window.
 * @param int    $window Window in seconds.
 * @return bool
 */
function hh_upload_rate_ok( $key, $limit, $window ) {
	$name  = 'hh_upl_' . substr( hash_hmac( 'sha256', $key, wp_salt( 'auth' ) ), 0, 40 );
	$entry = get_transient( $name );
	if ( ! is_array( $entry ) || empty( $entry['until'] ) || $entry['until'] < time() ) {
		$entry = array( 'count' => 0, 'until' => time() + $window );
	}
	if ( $entry['count'] >= $limit ) {
		return false;
	}
	$entry['count']++;
	set_transient( $name, $entry, max( 1, $entry['until'] - time() ) );
	return true;
}

/**
 * @param string $id Upload ID.
 * @return array|null Stored metadata.
 */
function hh_upload_meta( $id ) {
	if ( ! hh_upload_is_id( $id ) ) {
		return null;
	}
	$file = hh_upload_path_stem( $id ) . '.json';
	if ( ! is_readable( $file ) ) {
		return null;
	}
	$meta = json_decode( (string) file_get_contents( $file ), true );
	return is_array( $meta ) && ! empty( $meta['owner'] ) && ! empty( $meta['ext'] ) ? $meta : null;
}

/**
 * @param string $id Upload ID.
 * @return string URL that serves the photo to authorised viewers only.
 */
function hh_upload_view_url( $id ) {
	return add_query_arg( array( 'action' => 'hh_view_upload', 'id' => $id ), admin_url( 'admin-ajax.php' ) );
}

/**
 * @param array $meta Upload metadata.
 * @return bool Whether the current visitor may view the upload.
 */
function hh_upload_can_view( array $meta ) {
	if ( current_user_can( 'manage_woocommerce' ) ) {
		return true;
	}
	$owner = hh_upload_owner( false );
	return '' !== $owner && hash_equals( (string) $meta['owner'], $owner );
}

/**
 * Turns the value posted with the quiz (upload ID or its view URL) into a stored reference,
 * only if the current visitor uploaded that file.
 *
 * @param string $value Posted value.
 * @return string View URL, or '' if the value is not an upload owned by this visitor.
 */
function hh_upload_claim( $value ) {
	$value = trim( (string) $value );
	$id    = $value;
	if ( ! hh_upload_is_id( $id ) ) {
		$query = wp_parse_url( $value, PHP_URL_QUERY );
		parse_str( (string) $query, $args );
		$id = isset( $args['id'] ) ? (string) $args['id'] : '';
	}

	$meta  = hh_upload_meta( $id );
	$owner = hh_upload_owner( false );
	if ( ! $meta || '' === $owner || ! hash_equals( (string) $meta['owner'], $owner ) ) {
		return '';
	}
	return hh_upload_view_url( $id );
}

/**
 * AJAX: upload a scalp photo. Keeps the existing action name and response shape (data.url).
 */
function upload_custom_file_handler() {
	if ( is_user_logged_in() && ! check_ajax_referer( 'hh_upload', 'nonce', false ) ) {
		wp_send_json_error( 'Your session has expired. Please refresh the page and try again.', 403 );
	}

	// Count every attempt (including invalid files) before doing any work.
	$owner = hh_upload_owner( true );
	if ( ! hh_upload_rate_ok( 'site|' . gmdate( 'Ymd' ), HH_UPLOAD_LIMIT_SITE_DAY, DAY_IN_SECONDS )
		|| ! hh_upload_rate_ok( 'ip|' . hh_upload_client_ip(), HH_UPLOAD_LIMIT_IP, HOUR_IN_SECONDS )
		|| ! hh_upload_rate_ok( 'owner|' . $owner, HH_UPLOAD_LIMIT_OWNER, 10 * MINUTE_IN_SECONDS ) ) {
		wp_send_json_error( 'Too many uploads. Please wait a few minutes and try again.', 429 );
	}

	if ( empty( $_FILES['file'] ) || ! is_array( $_FILES['file'] ) ) {
		wp_send_json_error( 'No file uploaded', 400 );
	}
	$file = $_FILES['file'];

	if ( ! isset( $file['error'] ) || UPLOAD_ERR_OK !== (int) $file['error'] || empty( $file['tmp_name'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
		$too_big = isset( $file['error'] ) && in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true );
		wp_send_json_error( $too_big ? 'The photo is too large. Maximum size is 5 MB.' : 'The upload failed. Please try again.', 400 );
	}

	$result = hh_upload_process( $file['tmp_name'], $owner );
	if ( is_wp_error( $result ) ) {
		$status = $result->get_error_data();
		wp_send_json_error( $result->get_error_message(), is_int( $status ) ? $status : 400 );
	}

	wp_send_json_success( $result );
}

/**
 * Validates, re-encodes and stores one image privately.
 *
 * @param string $tmp_path Path of the received file.
 * @param string $owner    Owner identity from hh_upload_owner().
 * @return array|WP_Error { id, url } or an error with an HTTP status as data.
 */
function hh_upload_process( $tmp_path, $owner ) {
	$size = is_file( $tmp_path ) ? (int) filesize( $tmp_path ) : 0;
	if ( $size <= 0 || $size > HH_UPLOAD_MAX_BYTES ) {
		return new WP_Error( 'too_large', 'The photo is too large. Maximum size is 5 MB.', 413 );
	}
	if ( '' === (string) $owner ) {
		return new WP_Error( 'no_owner', 'The upload failed. Please try again.', 400 );
	}

	// Detect the type from the file contents, never from the name or browser-supplied type.
	$types = hh_upload_allowed_types();
	$mime  = function_exists( 'wp_get_image_mime' ) ? wp_get_image_mime( $tmp_path ) : false;
	$dims  = @getimagesize( $tmp_path );
	if ( ! $mime || ! isset( $types[ $mime ] ) || ! $dims || empty( $dims[0] ) || empty( $dims[1] ) ) {
		return new WP_Error( 'bad_type', 'Please upload a JPEG, PNG or WebP photo.', 415 );
	}
	if ( (int) $dims[0] * (int) $dims[1] > HH_UPLOAD_MAX_PIXELS ) {
		return new WP_Error( 'too_many_pixels', 'The photo dimensions are too large.', 413 );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$id   = bin2hex( random_bytes( 16 ) );
	$ext  = $types[ $mime ];
	$stem = hh_upload_path_stem( $id );
	$dest = $stem . '.' . $ext;

	// Re-encode: strips EXIF/GPS metadata and anything appended to the image.
	$editor = wp_get_image_editor( $tmp_path );
	if ( is_wp_error( $editor ) ) {
		return new WP_Error( 'unprocessable', 'The photo could not be processed. Please try a different photo.', 422 );
	}
	$saved = $editor->save( $dest, $mime );
	if ( is_wp_error( $saved ) || empty( $saved['path'] ) || ! file_exists( $saved['path'] ) ) {
		return new WP_Error( 'save_failed', 'The photo could not be saved. Please try again.', 500 );
	}
	if ( $saved['path'] !== $dest ) {
		@rename( $saved['path'], $dest );
	}
	@chmod( $dest, 0640 );

	$meta = array(
		'owner'   => (string) $owner,
		'mime'    => $mime,
		'ext'     => $ext,
		'bytes'   => (int) filesize( $dest ),
		'created' => time(),
	);
	file_put_contents( $stem . '.json', wp_json_encode( $meta ) );
	@chmod( $stem . '.json', 0640 );

	return array( 'id' => $id, 'url' => hh_upload_view_url( $id ) );
}

/**
 * File the current visitor may view for an upload ID, or '' (404).
 *
 * @param string $id Upload ID.
 * @return array{path: string, meta: array}|null
 */
function hh_upload_viewable( $id ) {
	$meta = hh_upload_meta( $id );
	if ( ! $meta || ! hh_upload_can_view( $meta ) ) {
		return null;
	}
	$path = hh_upload_path_stem( $id ) . '.' . $meta['ext'];
	return is_readable( $path ) ? array( 'path' => $path, 'meta' => $meta ) : null;
}
add_action( 'wp_ajax_upload_custom_file', 'upload_custom_file_handler' );
add_action( 'wp_ajax_nopriv_upload_custom_file', 'upload_custom_file_handler' );

/**
 * AJAX: serve an uploaded photo to its owner or to shop staff. Everyone else gets a 404.
 */
function hh_view_upload_handler() {
	$id   = isset( $_GET['id'] ) ? sanitize_key( wp_unslash( $_GET['id'] ) ) : '';
	$file = hh_upload_viewable( $id );

	if ( ! $file ) {
		status_header( 404 );
		nocache_headers();
		exit;
	}
	$path = $file['path'];
	$meta = $file['meta'];

	nocache_headers();
	header( 'Content-Type: ' . $meta['mime'] );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'Content-Disposition: inline; filename="scalp-photo.' . $meta['ext'] . '"' );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Robots-Tag: noindex, nofollow' );
	header( 'Cache-Control: private, no-store, max-age=0' );
	readfile( $path );
	exit;
}
add_action( 'wp_ajax_hh_view_upload', 'hh_view_upload_handler' );
add_action( 'wp_ajax_nopriv_hh_view_upload', 'hh_view_upload_handler' );
