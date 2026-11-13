    <?php
/**
 * Custom WooCommerce Reset Password Form
 * Override: your-theme/woocommerce/myaccount/form-reset-password.php
 */
defined( 'ABSPATH' ) || exit;

// Don’t show to logged-in users
if ( is_user_logged_in() ) {
    return;
}
?>

<form method="post" class="woocommerce-form woocommerce-form-login login custom-login-form woocommerce-form-reset-password">

    <?php do_action( 'woocommerce_resetpassword_form_start' ); ?>

    <div class="global-title">
        <h2><?php esc_html_e( 'Reset Password', 'woocommerce' ); ?></h2>
        <p><?php esc_html_e( 'Enter your new password below', 'woocommerce' ); ?></p>
    </div>

    <div class="contact-form">
        <!-- Keep your HTML structure exactly as you like -->
        
        <!-- Hidden fields for key & login -->
        <input type="hidden" name="reset_key"   value="<?php echo esc_attr( $_GET['key'] ); ?>" />
        <input type="hidden" name="reset_login" value="<?php echo esc_attr( $_GET['login'] ); ?>" />

        <!-- New Password Field -->
        <div class="mb-3">
            <label for="password_1"><?php esc_html_e( 'New Password', 'woocommerce' ); ?></label>
            <div class="password-wrapper">
                <input
                    type="password"
                    name="password_1"
                    id="password_1"
                    class="input-text"
                    required
                    autocomplete="new-password"
                />
                <span class="toggle-password">&#128065;</span>
            </div>
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-3">
            <label for="password_2"><?php esc_html_e( 'Confirm New Password', 'woocommerce' ); ?></label>
            <div class="password-wrapper">
                <input
                    type="password"
                    name="password_2"
                    id="password_2"
                    class="input-text"
                    required
                    autocomplete="new-password"
                />
                <span class="toggle-password">&#128065;</span>
            </div>
        </div>

        <?php do_action( 'woocommerce_resetpassword_form' ); ?>

        <!-- Submit Button -->
        <button
            type="submit"
            class="woocommerce-button button woocommerce-form-login__submit sign-in-btn dark-btn"
            name="reset_password"
            value="true"
        >
            <?php esc_html_e( 'Save', 'woocommerce' ); ?>
        </button>
    </div>

    <?php do_action( 'woocommerce_resetpassword_form_end' ); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
