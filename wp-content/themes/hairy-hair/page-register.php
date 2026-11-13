<?php
/**
 * Template Name: Register Page
 *
 * Shows only the WooCommerce registration form,
 * using your custom form-register.php override.
 */

defined( 'ABSPATH' ) || exit;

// Redirect logged-in users to My Account
if ( is_user_logged_in() ) {
    wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
    exit;
}

get_header();
?>

<div class="main-page login-page">

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="login-form">
                    <div class="container">
  <?php if ( ! is_user_logged_in() ) : ?>

    <?php
      // Show any errors/notices, run before‐form hooks
      wc_print_notices();
      do_action( 'woocommerce_before_customer_login_form' );

      // Load your custom registration form
      // (this will pick up your theme’s
      // woocommerce/myaccount/form-register.php)
      get_template_part( 'woocommerce/myaccount/form-register' );

      // Run after‐form hooks
      do_action( 'woocommerce_after_customer_login_form' );
    ?>

  <?php else : ?>

    <div class="already-logged-in">
      <p><?php esc_html_e( 'You are already registered and logged in.', 'woocommerce' ); ?></p>
    </div>

  <?php endif; ?>
    </div>
                </section>
            </div>
        </div>
    </div>

<?php
get_footer();
