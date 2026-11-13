<?php

add_action( 'template_redirect', function () {

    if ( ! isset( $_GET['create-failed-renewal'] ) ) {
        return;
    }

    // Admin only.
    if ( ! current_user_can( 'manage_woocommerce' ) ) {
        wp_die( 'Not allowed.' );
    }

    // Hardcoded safety restriction.
    $subscription_id = 15789;

    if ( ! function_exists( 'wcs_get_subscription' ) ) {
        wp_die( 'WooCommerce Subscriptions is not available.' );
    }

    $subscription = wcs_get_subscription( $subscription_id );

    if ( ! $subscription ) {
        wp_die( 'Subscription #15789 not found.' );
    }

    /*
     * IMPORTANT:
     * This creates a renewal order only.
     * It does NOT execute the Square payment gateway.
     */
    $renewal_order = wcs_create_renewal_order( $subscription );

    if ( is_wp_error( $renewal_order ) ) {
        wp_die(
            'Could not create renewal order: ' .
            esc_html( $renewal_order->get_error_message() )
        );
    }

    // Make sure it uses the same gateway as the subscription.
    $renewal_order->set_payment_method( 'square_credit_card' );
    $renewal_order->set_payment_method_title( 'Credit Card' );
    $renewal_order->save();

    /*
     * Simulate the error we're investigating.
     *
     * Square is NOT contacted.
     */
    $renewal_order->add_order_note(
        'TEST ONLY - Square Payment Failed: Payment nonce is missing'
    );

    $renewal_order->update_status(
        'failed',
        'TEST ONLY - Simulated automatic renewal payment failure: Payment nonce is missing'
    );

    $renewal_order->save();

    wp_die(
        '<h2>Test failed renewal created</h2>' .
        '<p>Subscription: <strong>#15789</strong></p>' .
        '<p>Renewal Order: <strong>#' .
        esc_html( $renewal_order->get_id() ) .
        '</strong></p>' .
        '<p>Status: <strong>' .
        esc_html( $renewal_order->get_status() ) .
        '</strong></p>' .
        '<p>Gateway: <strong>' .
        esc_html( $renewal_order->get_payment_method() ) .
        '</strong></p>' .
        '<p><strong>Square was NOT contacted.</strong></p>'
    );
} );