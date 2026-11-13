<?php
/**
 * Plugin Name: HairyHair Registration Sync
 * Description: Links verified WordPress users to their existing HairyHair leads and syncs the registration to Zoho CRM via the Connect with Zoho plugin.
 * Version:     1.1.0
 * Author:      hairyhair
 *
 * Flow:
 * 1. user_register (priority 20, after WordPress/WooCommerce/theme handlers at 10):
 *    - link guest leads/quiz results with the same email, only if the email is verified
 *      (normally it is not yet: WooCommerce logs new customers in before they prove the email);
 *    - queue one background Zoho sync job (front-end registrations only).
 * 2. after_password_reset: the user completed an emailed password set/reset link, which proves
 *    the email -> mark verified and link.
 * 3. wp_login: link any new guest leads for a verified account. No Zoho sync.
 * 4. hh_registration_zoho_sync job: create or update the Zoho Lead, with retries. If a Zoho
 *    Lead may already exist for the email and the email is not verified yet, the job waits and is
 *    re-queued by step 2 (no update of a Lead found only by email, no replacement Lead).
 * 5. hh_lead_zoho_sync job: background retries for lead-form submissions (HH_Zoho_Lead_Sync).
 *
 * Linking only sets user_id on rows that have none (0/NULL); rows owned by any user are never
 * changed, no rows are created, and linking never calls Zoho.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/hairyhair-registration/class-registration-lead.php';
require_once __DIR__ . '/hairyhair-registration/class-registration-zoho.php';
require_once __DIR__ . '/hairyhair-registration/class-zoho-lead-sync.php';
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once __DIR__ . '/hairyhair-registration/class-cli.php';
}

add_action( 'user_register', 'hh_registration_handle_user_register', 20, 1 );
add_action( 'wp_login', 'hh_registration_handle_login', 10, 2 );
add_action( 'after_password_reset', 'hh_registration_handle_password_reset', 10, 1 );
add_action( HH_Registration_Zoho::JOB_HOOK, array( 'HH_Registration_Zoho', 'run' ), 10, 2 );
add_action( HH_Zoho_Lead_Sync::JOB_HOOK, array( 'HH_Zoho_Lead_Sync', 'run_job' ), 10, 2 );

/**
 * Handles a newly created WordPress user.
 *
 * Runs once per user creation; safe to call again for the same user.
 *
 * @param int $user_id New WordPress user ID.
 */
function hh_registration_handle_user_register( $user_id ) {
	$user_id = absint( $user_id );
	$user    = $user_id ? get_userdata( $user_id ) : false;
	if ( ! $user ) {
		return;
	}

	$email = HH_Registration_Lead::normalize_email( $user->user_email );
	if ( '' === $email ) {
		HH_Registration_Zoho::log( sprintf( 'HairyHair registration user #%d: skipped (INVALID_EMAIL).', $user_id ) );
		return;
	}

	HH_Registration_Lead::link_user( $user_id ); // No-op until the email is verified.

	/**
	 * Whether this user creation should be synced to Zoho.
	 *
	 * Default: only front-end registrations (My Account form, checkout account creation).
	 * Users created in wp-admin, by WP-CLI, imports or cron are not sent to Zoho.
	 *
	 * @param bool $should  Default decision.
	 * @param int  $user_id New user ID.
	 */
	$is_backend = ( is_admin() && ! wp_doing_ajax() ) || wp_doing_cron() || ( defined( 'WP_CLI' ) && WP_CLI );
	if ( apply_filters( 'hh_registration_should_sync_zoho', ! $is_backend, $user_id ) ) {
		HH_Registration_Zoho::schedule( $user_id );
	}
}

/**
 * wp_login passes ( $user_login, WP_User $user ): use the authenticated user object, not the login name.
 *
 * @param string       $user_login Login name (unused).
 * @param WP_User|null $user       Authenticated user.
 */
function hh_registration_handle_login( $user_login, $user = null ) {
	if ( $user instanceof WP_User && $user->ID ) {
		HH_Registration_Lead::link_user( $user->ID );
	}
}

/**
 * A password set/reset link sent to the account's email was used: the email is proven.
 * Fired by WordPress core reset_password() and WooCommerce's My Account reset/set-password form.
 *
 * @param WP_User $user User whose password was reset.
 */
function hh_registration_handle_password_reset( $user ) {
	if ( $user instanceof WP_User && $user->ID ) {
		HH_Registration_Lead::mark_email_verified( $user->ID );
		HH_Registration_Lead::link_user( $user->ID );
		// Ownership is now proven: resume Zoho syncs that were held back for it. Idempotent.
		HH_Registration_Zoho::resume_after_verification( $user->ID );
		HH_Zoho_Lead_Sync::requeue_after_verification( $user->ID );
	}
}
