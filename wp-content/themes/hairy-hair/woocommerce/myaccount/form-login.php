<?php
/**
 * Custom WooCommerce Login Form
 *
 * Override: your-theme/woocommerce/myaccount/form-login.php
 */

defined( 'ABSPATH' ) || exit;

// If already logged in, no need to show form
if ( is_user_logged_in() ) {
    return;
}

// Print any WooCommerce notices (errors, etc.)
wc_print_notices();

do_action( 'woocommerce_before_customer_login_form' );
?>

<form method="post" class="woocommerce-form woocommerce-form-login login custom-login-form">

    <?php do_action( 'woocommerce_login_form_start' ); ?>

    <div class="global-title">
        <h2>Welcome Back</h2>
        <p>Enter your email and password to access your account</p>
    </div>

    <div class="contact-form">
        <div class="mb-3">
            <label for="username">Username or Email</label>
            <input
                type="text"
                name="username"
                id="username"
                placeholder="Example@gmail.com or johndoe"
                required
                autocomplete="username"
            />
        </div>

        <div class="mb-3">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Ex. Johndoe#423"
                    required
                    autocomplete="current-password"
                />
                <span class="toggle-password">&#128065;</span>
            </div>
        </div>

        <div class="mb-3 d-flex justify-content-between pw-setting">
            <label style="display: flex; align-items: center; color: #4a5a73; font-size: 16px;">
                <input
                    name="rememberme"
                    type="checkbox"
                    id="rememberme"
                    value="forever"
                    style="margin-right: 8px; width: 16px; height: 16px;"
                />
                Remember me
            </label>
            <a href="<?php echo home_url().'/forgot-password'; ?>">Forgot Password?</a>
        </div>

        <?php
do_action( 'woocommerce_login_form' );
wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' );
?>

        <button
            type="submit"
            class="woocommerce-button button woocommerce-form-login__submit sign-in-btn dark-btn"
            name="login"
            value="<?php esc_attr_e( 'Sign in', 'woocommerce' ); ?>"
        >
            SIGN IN
        </button>

        <div class="register-link">
            Don’t have an account? <a href="<?php echo home_url().'/register'; ?>">Register</a>
        </div>
    </div>

    <?php do_action( 'woocommerce_login_form_end' ); ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.querySelector('.toggle-password');
    var pwd    = document.getElementById('password');
    toggle.addEventListener('click', function() {
        var type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
        pwd.setAttribute('type', type);
        this.textContent = type === 'password' ? '\u{1F441}' : '\u{1F576}';
    });
});
</script>

<?php
do_action( 'woocommerce_after_customer_login_form' );
?>
