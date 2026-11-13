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
require_once HAIRY_PATH . 'inc/class-hairy-quiz-admin.php'; 
require_once HAIRY_PATH . 'inc/class-hairy-leads-admin.php'; 
require_once HAIRY_PATH . 'inc/class-hairy-quiz-frontend.php';


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


add_action( 'user_register', 'my_custom_user_registration_handler', 10, 1 );
add_action( 'wp_login', 'my_custom_user_registration_handler', 10, 2 );
function my_custom_user_registration_handler( $user_id ) {
    global $wpdb;
    $user_info = get_userdata( $user_id );
    $email = $user_info->user_email;

    $quiz_table = $wpdb->prefix . 'hh_quiz_results';
    $lead_table = $wpdb->prefix . 'hh_lead_results';

    $quiz = $wpdb->get_row("SELECT * FROM $quiz_table WHERE email = '$email' ");

    if($quiz) {
        $wpdb->query("UPDATE $quiz_table SET user_id = $user_id WHERE email = '$quiz->email'");

        update_user_meta($user_id, 'email', $email);
        update_user_meta($user_id, 'first_name', $quiz->first_name);
        update_user_meta($user_id, 'last_name', $quiz->last_name);
        update_user_meta($user_id, 'phone', $quiz->phone);
        update_user_meta($user_id, 'dob', $quiz->dob);
        update_user_meta($user_id, 'gender', $quiz->gender);
    } 

    $lead = $wpdb->get_row("SELECT * FROM $lead_table WHERE email = '$email' ");

    if($lead) {
        $wpdb->query("UPDATE $lead_table SET user_id = $user_id WHERE email = '$email'");

        update_user_meta($user_id, 'first_name', $lead->first_name);
        update_user_meta($user_id, 'email', $email);
        update_user_meta($user_id, 'last_name', $lead->last_name);
        update_user_meta($user_id, 'phone', $lead->phone);
        update_user_meta($user_id, 'dob', $lead->dob);
        update_user_meta($user_id, 'gender', $lead->gender);
        update_user_meta($user_id, 'postcode', $lead->postcode);
        update_user_meta($user_id, 'email', $lead->email);
        update_user_meta($user_id, 'state', $lead->state);
        update_user_meta($user_id, 'city', $lead->city);
        update_user_meta($user_id, 'address_line_1', $lead->address_line_1);
        update_user_meta($user_id, 'address_line_2', $lead->address_line_2);
    }

     

    if (isset($_COOKIE['quiz_user_email'])) {
        unset($_COOKIE['quiz_user_email']);
        setcookie('quiz_user_email', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
    }

    if (isset($_COOKIE['lead_user_email'])) {
        unset($_COOKIE['lead_user_email']);
        setcookie('lead_user_email', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
    }

}
 

function gm_get_user_meta($user_id, $key = '', $single = false) {
    global $wpdb; 
    
    if (isset($user_id) && $user_id > 0) { 
        return get_user_meta($user_id, $key, $single);
    } 
     
    if (isset($_COOKIE['lead_user_email'])) { 
        $email = $_COOKIE['lead_user_email']; 
        $table = $wpdb->prefix . 'hh_lead_results';
 
        return $wpdb->get_var("SELECT $key FROM {$table} WHERE email = '$email'");

    } elseif (isset($_COOKIE['quiz_user_email'])) {
        $n_Arr = [
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'postcode',
        ];

        if(!in_array($key, $n_Arr)) {
            $email = $_COOKIE['quiz_user_email']; 
            $table = $wpdb->prefix . 'hh_quiz_results';
            return $wpdb->get_var("SELECT $key FROM {$table} WHERE email = '$email'");
        }

    }   

}


add_action('wp_ajax_upload_custom_file', 'upload_custom_file_handler');
add_action('wp_ajax_nopriv_upload_custom_file', 'upload_custom_file_handler');

function upload_custom_file_handler() { 
    if (empty($_FILES['file'])) {
        wp_send_json_error('No file uploaded');
    }
 
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');
 
    $uploaded = media_handle_upload('file', 0); 

    if (is_wp_error($uploaded)) {
        wp_send_json_error($uploaded->get_error_message());
    }
 
    $file_url = wp_get_attachment_url($uploaded);

    wp_send_json_success(['attachment_id' => $uploaded, 'url' => $file_url]);
}


function enqueue_custom_file_upload_script() {
    // Enqueue jQuery if not already included
    wp_enqueue_script('jquery');

    // Enqueue a dummy custom script handle
    wp_register_script('custom-upload-script', false);
    wp_enqueue_script('custom-upload-script');

    // Localize the admin-ajax URL
    wp_localize_script('custom-upload-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
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

   