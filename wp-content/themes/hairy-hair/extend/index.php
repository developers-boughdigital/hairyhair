<?php 

add_action('init', function () {
    if (!session_id()) {
        session_start();
    }
});

function check_quiz_result_access() {
    global $wpdb;

    if (!session_id()) {
        session_start();
    }

    $user_id = get_current_user_id();

    if ( $user_id === 0 ) {
        // Guests: only a quiz they submitted themselves, proven by the server-issued guest-access
        // token. The old quiz_user_email / lead_user_email cookies are not trusted.
        $quiz_result = function_exists( 'hh_guest_access_row' ) ? hh_guest_access_row( 'quiz' ) : null;
    } else {
        $user_info = get_userdata( $user_id );
        $email = $user_info ? $user_info->user_email : '';

        $quiz_table = $wpdb->prefix . 'hh_quiz_results';

        $quiz_result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ID FROM {$quiz_table} WHERE email = %s LIMIT 1",
                $email
            )
        );
    }

    $acess_to_add_prescriptive_products = ( $quiz_result === null ) ? false : true;
    $_SESSION['acess_to_add_prescriptive_products'] = ( $quiz_result === null ) ? false : true;
 
}

add_action('init', 'check_quiz_result_access');




require_once get_template_directory() . '/extend/quiz/quiz.php';
require_once get_template_directory() . '/extend/campaign/campaign.php'; 
