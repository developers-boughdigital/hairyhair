<?php
/**
 * Template Name: Forgot Password Page
 *
 * Displays only the WooCommerce lost-password form.
 */
defined( 'ABSPATH' ) || exit;

// Redirect logged-in users to My Account
// if ( is_user_logged_in() ) {
//     wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
//     exit;
// }

get_header();
?>

<div class="main-page login-page">

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="login-form">
                    <div class="container">
                        <?php
                            // Show any error messages
                            wc_print_notices();

                            // Before‐form hook
                            do_action( 'woocommerce_before_lost_password_form' );

                            // Load WooCommerce’s form-lost-password.php template
                            wc_get_template( 'myaccount/form-lost-password.php' );

                            // After‐form hook
                            do_action( 'woocommerce_after_lost_password_form' );
                        ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
<?php get_footer(); ?>
