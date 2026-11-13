<?php
/**
 * Customer processing renewal order email
 *
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 7.3.0
 *
 * Updated for WC core email improvements.
 */
defined( 'ABSPATH' ) || exit;

$email_improvements_enabled = wcs_is_wc_feature_enabled( 'email_improvements' );

do_action( 'woocommerce_email_header', $email_heading, $email );

echo $email_improvements_enabled ? '<div class="email-introduction">' : '';

/* translators: %s: Customer first name */ ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce-subscriptions' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php /* translators: %s: Order number */ ?>
<p>
<?php
printf(
    esc_html__(
        'Your subscription payment of %s has been successfully processed from your account.',
        'woocommerce-subscriptions'
    ),
    wp_kses_post(
        wc_price(
            $order->get_total(),
            array(
                'currency' => $order->get_currency(),
            )
        )
    )
);
?>
</p>

<p>
    Your subscription is continuing as normal, and your next payment
    will be processed according to your existing subscription schedule.
</p>

<p>
    If you have any questions or need assistance, please feel free
    to reach out.
</p><?php

echo $email_improvements_enabled ? '</div>' : '';

do_action( 'woocommerce_subscriptions_email_order_details', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? '<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content">' : '';
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

do_action( 'woocommerce_email_footer', $email );
