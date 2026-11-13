<?php
// extend/campaign/templates/widget-template.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Variables passed from HH_Shortcode_Generator::display_campaign_widget:
// $image_url, $webp_url, $destination_url, $alt_text

$image_tag = '';
$img_src = esc_url( $image_url );
$img_alt = esc_attr( $alt_text );

// Add lazy loading and picture tag for WebP
$image_tag = '<picture>';
if ( ! empty( $webp_url ) ) {
    $image_tag .= '<source srcset="' . esc_url( $webp_url ) . '" type="image/webp">';
}
$image_tag .= '<img src="' . $img_src . '" alt="' . $img_alt . '" loading="lazy" class="hh-campaign-widget-image">';
$image_tag .= '</picture>';

if ( ! empty( $destination_url ) ) {
    echo '<a href="' . esc_url( $destination_url ) . '" target="_blank" rel="noopener">' . $image_tag . '</a>';
} else {
    echo $image_tag;
}