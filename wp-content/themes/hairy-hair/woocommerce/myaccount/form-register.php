<?php
defined( 'ABSPATH' ) || exit;

// Remove the privacy‐policy text from the register form
remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );

// Don’t show to logged‐in users
if ( is_user_logged_in() ) {
    return;
}

// Display any errors & notices
wc_print_notices();
do_action( 'woocommerce_before_customer_login_form' );
?>

<form method="post" class="woocommerce-form woocommerce-form-register register custom-register-form">

    <?php do_action( 'woocommerce_register_form_start' ); ?>

    <div class="global-title">
        <h2><?php esc_html_e( 'Create Account', 'woocommerce' ); ?></h2>
        <p><?php esc_html_e( 'Enter your details to register', 'woocommerce' ); ?></p>
    </div>

    <div class="contact-form">

         <!-- REPLACED: split “Name” into first_name + last_name -->
        <div class="mb-3">
            <label for="reg_first_name"><?php esc_html_e( 'First name', 'woocommerce' ); ?></label>
            <input
                type="text"
                name="first_name"
                id="reg_first_name"
                placeholder="<?php esc_attr_e( 'First name', 'woocommerce' ); ?>"
                required
                autocomplete="given-name"
            />
        </div>

        <div class="mb-3">
            <label for="reg_last_name"><?php esc_html_e( 'Last name', 'woocommerce' ); ?></label>
            <input
                type="text"
                name="last_name"
                id="reg_last_name"
                placeholder="<?php esc_attr_e( 'Last name', 'woocommerce' ); ?>"
                required
                autocomplete="family-name"
            />
        </div>
        <!-- END REPLACED -->

        <div class="mb-3">
            <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?></label>
            <input
                type="email"
                name="email"
                id="reg_email"
                placeholder="<?php esc_attr_e( 'Example@gmail.com', 'woocommerce' ); ?>"
                required
                autocomplete="email"
            />
        </div>

        <div class="mb-3">
            <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?></label>
            <div class="password-wrapper">
                <input
                    type="password"
                    name="password"
                    id="reg_password"
                    placeholder="<?php esc_attr_e( 'Create a strong password', 'woocommerce' ); ?>"
                    required
                    autocomplete="new-password"
                />
                <span class="toggle-password">&#128065;</span>
            </div>
        </div>

        <div class="mb-3">
            <label for="reg_confirm_password"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?></label>
            <div class="password-wrapper">
                <input
                    type="password"
                    name="confirm_password"
                    id="reg_confirm_password"
                    placeholder="<?php esc_attr_e( 'Re-enter your password', 'woocommerce' ); ?>"
                    required
                    autocomplete="new-password"
                />
                <span class="toggle-password">&#128065;</span>
            </div>
        </div>
        <!-- NEW: Remember me + Forgot Password link -->
        <div class="mb-3 d-flex justify-content-between pw-setting">
            <label style="display: flex; align-items: center; font-size: 16px;">
                <input
                    name="rememberme"
                    type="checkbox"
                    id="rememberme"
                    value="forever"
                    style="margin-right: 8px; width: 16px; height: 16px;"
                />
                <?php esc_html_e( 'Remember me', 'woocommerce' ); ?>
            </label>
        </div>
        <!-- end new -->

        <?php
            // Hook for extra fields if needed
            do_action( 'woocommerce_register_form' );
            // Security nonce
            wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' );
            
        ?>
    
        <button
            type="submit"
            class="woocommerce-button button woocommerce-form-register__submit sign-in-btn dark-btn"
            name="register"
            value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"
        >
            <?php esc_html_e( 'Sign in', 'woocommerce' ); ?>
        </button>

        <div class="login-link">
            <?php esc_html_e( 'Already have an account?', 'woocommerce' ); ?>
            <a href="<?php echo home_url().'/login'; ?>">
                <?php esc_html_e( 'Sign in', 'woocommerce' ); ?>
            </a>
        </div>

    </div>

    <?php do_action( 'woocommerce_register_form_end' ); ?>

</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visibility on both password fields
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
do_action( 'woocommerce_after_customer_login_form' );
?>
