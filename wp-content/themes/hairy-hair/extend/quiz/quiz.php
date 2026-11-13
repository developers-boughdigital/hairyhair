<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

if ( ! defined( 'HAIRY_PATH' ) ) {
    define( 'HAIRY_PATH', get_template_directory() . '/extend/quiz/' );
}
if ( ! defined( 'HAIRY_URL' ) ) {
    define( 'HAIRY_URL', get_template_directory_uri() . '/extend/quiz/' );
}

require_once HAIRY_PATH . 'inc/class-hairy-quiz-manager-core.php';
require_once HAIRY_PATH . 'inc/class-hairy-quiz-db-migrations.php';
require_once HAIRY_PATH . 'inc/class-hairy-quiz-admin.php'; 
require_once HAIRY_PATH . 'inc/class-hairy-leads-admin.php'; 
require_once HAIRY_PATH . 'inc/class-hairy-quiz-frontend.php';
require_once HAIRY_PATH . 'inc/guest-access.php';      // Server-issued guest tokens (replace email cookies).
require_once HAIRY_PATH . 'inc/private-uploads.php';   // Private scalp-photo uploads (replaces the public Media Library handler).
require_once HAIRY_PATH . 'inc/submission-guard.php';  // Duplicate-submission protection.


function hairy_load_quiz_manager() {
    $core_manager = Hairy_Quiz_Manager_Core::get_instance();
    new Hairy_Quiz_Admin( $core_manager ); 
    new Hairy_Leads_Admin( $core_manager ); 
    new Hairy_Quiz_Frontend( $core_manager );
}
add_action( 'after_setup_theme', 'hairy_load_quiz_manager' );


function hairy_quiz_manager_setup() {
    if ( get_option( 'hairy_quiz_manager_tables_created' ) !== 'yes' ) {
        Hairy_Quiz_Manager_Core::activate();
        update_option( 'hairy_quiz_manager_tables_created', 'yes' );
    }
}
add_action( 'after_setup_theme', 'hairy_quiz_manager_setup', 0 );

// Lead table schema migrations: run on every request once new theme files are uploaded (no reactivation needed),
// before any AJAX/form handler writes to the table. No-op once the stored version is current.
add_action( 'after_setup_theme', array( 'Hairy_Quiz_DB_Migrations', 'maybe_migrate' ), 1 );
 

 

function add_woocommerce_account_body_class( $classes ) {
    if ( is_page( 'quiz' ) ) {
        $classes[] = 'woocommerce-account';
    }
    return $classes;
}
add_filter( 'body_class', 'add_woocommerce_account_body_class' );

function quiz_my_account_menu_items( $items ) {
    $new_items = [];

    foreach ( $items as $key => $item ) {
        $new_items[$key] = $item;
        if ( $key === 'edit-address' ) {
            $new_items['quiz'] = 'Quiz';
        }
    }

    return $new_items;
}
add_filter( 'woocommerce_account_menu_items', 'quiz_my_account_menu_items' );


// Lead / quiz-result linking lives in the hairyhair-registration mu-plugin (HH_Registration_Lead::link_user):
// it runs on registration, login and password reset, only for accounts whose email is verified, and only
// claims rows not owned by anyone. The theme fills empty profile fields once rows have been linked.
add_action( 'user_register', 'my_custom_user_registration_handler', 10, 1 );
add_action( 'wp_login', 'my_custom_user_registration_handler', 10, 0 );
add_action( 'hh_leads_linked', 'hairy_quiz_fill_profile_from_linked_leads', 10, 2 );

/**
 * Registration / login: the browser is now an account, so revoke guest access (server-side token
 * and cookie) and drop the legacy email cookies (no longer trusted for anything).
 */
function my_custom_user_registration_handler() {
    if ( function_exists( 'hh_guest_access_clear' ) ) {
        hh_guest_access_clear();
    }
    foreach ( array( 'quiz_user_email', 'lead_user_email' ) as $cookie ) {
        if ( isset( $_COOKIE[ $cookie ] ) ) {
            unset( $_COOKIE[ $cookie ] );
            if ( ! headers_sent() ) {
                setcookie( $cookie, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
            }
        }
    }
}

/**
 * Copies details from the user's own newest linked lead (then quiz result) into profile fields
 * that are still empty. Never overwrites an existing value and never writes an empty value.
 *
 * @param int    $user_id User ID.
 * @param string $email   Account email (normalised).
 */
function hairy_quiz_fill_profile_from_linked_leads( $user_id, $email ) {
    global $wpdb;
    $user_id = absint( $user_id );
    if ( ! $user_id || '' === (string) $email ) {
        return;
    }

    $lead = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hh_lead_results WHERE user_id = %d AND email = %s ORDER BY ID DESC LIMIT 1",
        $user_id, $email
    ), ARRAY_A );
    $quiz = $wpdb->get_row( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}hh_quiz_results WHERE user_id = %d AND email = %s ORDER BY ID DESC LIMIT 1",
        $user_id, $email
    ), ARRAY_A );

    $fields = array( 'first_name', 'last_name', 'phone', 'dob', 'gender', 'postcode', 'state', 'city', 'address_line_1', 'address_line_2' );
    $values = array( 'email' => $email );
    foreach ( $fields as $field ) {
        foreach ( array( $lead, $quiz ) as $row ) {
            if ( is_array( $row ) && isset( $row[ $field ] ) && '' !== trim( (string) $row[ $field ] ) ) {
                $values[ $field ] = $row[ $field ];
                break;
            }
        }
    }

    foreach ( $values as $key => $value ) {
        if ( '' === trim( (string) get_user_meta( $user_id, $key, true ) ) ) {
            update_user_meta( $user_id, $key, $value );
        }
    }
}
 

/**
 * Columns gm_get_user_meta() may read from the lead table (column names cannot be prepared).
 *
 * @return string[]
 */
function gm_get_user_meta_allowed_keys() {
    return array( 'first_name', 'last_name', 'email', 'phone', 'dob', 'gender', 'address_line_1', 'address_line_2', 'city', 'state', 'postcode' );
}

function gm_get_user_meta($user_id, $key = '', $single = false) {
    if (isset($user_id) && $user_id > 0) { 
        return get_user_meta($user_id, $key, $single);
    } 

    // Guests: only their own lead / quiz row, authorised by the server-issued guest-access token
    // (never by an email address in a cookie). Lead row first, then quiz row, as before.
    if ( ! function_exists( 'hh_guest_access_row' ) || ! in_array( $key, gm_get_user_meta_allowed_keys(), true ) ) {
        return '';
    }
    $address_keys = array( 'address_line_1', 'address_line_2', 'city', 'state', 'postcode' );
    foreach ( array( 'lead', 'quiz' ) as $purpose ) {
        if ( 'quiz' === $purpose && in_array( $key, $address_keys, true ) ) {
            continue;
        }
        $row = hh_guest_access_row( $purpose );
        if ( $row && isset( $row[ $key ] ) ) {
            return $row[ $key ];
        }
    }
    return '';
}


function enqueue_custom_file_upload_script() {
    // Enqueue jQuery if not already included
    wp_enqueue_script('jquery');

    // Enqueue a dummy custom script handle
    wp_register_script('custom-upload-script', false);
    wp_enqueue_script('custom-upload-script');

    // Localize the admin-ajax URL
    wp_localize_script('custom-upload-script', 'ajax_object', array(
        'ajax_url'     => admin_url('admin-ajax.php'),
        'upload_nonce' => is_user_logged_in() ? wp_create_nonce( 'hh_upload' ) : '', // private-uploads.php requires it for logged-in users
    ));

    // Add inline JS code
    wp_add_inline_script('custom-upload-script', "
        jQuery(document).ready(function($) {
            $('#file-input').on('change', function() {
                $('#upload-status').text('Uploading...');
                var file = this.files[0];
                var formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'upload_custom_file');
                formData.append('nonce', ajax_object.upload_nonce);

                $.ajax({
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        console.log(response);
                        if (response.success) {
                            $('#upload-status').text('File uploaded!');
                            $('#file-url').val(response.data.url);
                            $('#file-input').hide();
                        } else {
                            $('#upload-status').text('Upload failed: ' + response.data);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        $('#upload-status').text('Upload failed!');
                    }
                });
            });
        });
    ");
}
add_action('wp_enqueue_scripts', 'enqueue_custom_file_upload_script');

   