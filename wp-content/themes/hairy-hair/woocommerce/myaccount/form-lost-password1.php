<?php
/**
 * Custom WooCommerce Lost Password Form
 *
 * Override this template by copying to:
 * your-theme/woocommerce/myaccount/form-lost-password.php
 */
defined( 'ABSPATH' ) || exit;

// Don’t show to logged-in users
if ( is_user_logged_in() ) {
    return;
}

// Print notices and before-form hook
wc_print_notices();
do_action( 'woocommerce_before_lost_password_form' );
?>

<form method="post" class="woocommerce-form woocommerce-form-login login custom-login-form woocommerce-form-lost-password">

    <?php do_action( 'woocommerce_lostpassword_form_start' ); ?>

    <div class="global-title">
        <h2>Forgot Password?</h2>
        <p>Don’t worry, happens to all of us. Enter your email below to recover your password</p>
    </div>

    <div class="contact-form">
        <!-- Username / Email -->
        <div class="mb-3">
            <label for="user_login">Email</label>
            <input
                type="email"
                name="user_login"
                id="user_login"
                placeholder="Example@gmail.com"
                required
                autocomplete="username"
            />
        </div>

        <?php
            do_action( 'woocommerce_lostpassword_form' );
            wp_nonce_field( 'woocommerce-lost-password', 'woocommerce-lost-password-nonce' );
        ?>

        <!-- Submit -->
        <button
            type="submit"
            class="woocommerce-button button woocommerce-form-login__submit sign-in-btn dark-btn"
            name="wc_reset_password"
            value="true"
        >
            <?php esc_html_e( 'Send Verification Link', 'woocommerce' ); ?>
        </button>

        <!-- Back to login -->
        <div class="register-link">
            <?php esc_html_e( 'Remember your password?', 'woocommerce' ); ?>
            <a href="<?php echo home_url().'/login'; ?>">
                <?php esc_html_e( 'Sign In', 'woocommerce' ); ?>
            </a>
        </div>
    </div>

    <?php do_action( 'woocommerce_lostpassword_form_end' ); ?>

</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // If you ever add a password field here, you can reuse this toggle logic:
    document.querySelectorAll('.toggle-password').forEach(function(toggle){
        toggle.addEventListener('click', function(){
            var input = this.previousElementSibling;
            var type  = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.textContent = type === 'password' ? '\u{1F441}' : '\u{1F576}';
        });
    });
});
</script>

<?php
// After-form hook
do_action( 'woocommerce_after_lost_password_form' );
?>
