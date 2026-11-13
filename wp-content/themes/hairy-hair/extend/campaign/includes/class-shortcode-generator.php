<?php
// extend/campaign/includes/class-shortcode-generator.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HH_Shortcode_Generator {
    private static $instance = null;
    private $campaign_manager;

    private function __construct() {
        $this->campaign_manager = HH_Campaign_Manager::get_instance();
        add_shortcode( 'hh_campaign_widget', array( $this, 'display_campaign_widget' ) );
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Shortcode callback to display the campaign widget.
     *
     * Usage: [hh_campaign_widget slot="Homepage Hero Banner"]
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output for the widget.
     */
    public function display_campaign_widget( $atts ) {
        $atts = shortcode_atts(
            array(
                'slot' => '',
            ),
            $atts,
            'hh_campaign_widget'
        );

        $slot_name = sanitize_title( $atts['slot'] );
        if ( empty( $slot_name ) ) {
            return '';
        }
 

        global $wpdb;
        $table_campaigns = $wpdb->prefix . 'campaign_cms_campaigns';
        $table_assets = $wpdb->prefix . 'campaign_cms_campaign_assets';
        $table_slots = $wpdb->prefix . 'campaign_cms_widget_slots';
        $current_time = current_time( 'mysql' );
        $device_type = wp_is_mobile() ? 'mobile' : 'desktop'; 

        $active_asset = $wpdb->get_row( $wpdb->prepare(
            "SELECT ca.*, ws.fallback_behavior
             FROM {$table_assets} ca
             JOIN {$table_campaigns} c ON ca.campaign_id = c.id
             JOIN {$table_slots} ws ON ca.slot_id = ws.id
             WHERE ws.slot_name = %s
             AND c.status = 'live'
             AND c.start_date <= %s
             AND c.end_date >= %s
             AND (ca.device_type = %s OR ca.device_type = 'desktop') 
             ORDER BY ca.device_type = %s DESC LIMIT 1",
            $atts['slot'], 
            $current_time,
            $current_time,
            $device_type,
            $device_type
        ) ); 

        if ( $active_asset && ! empty( $active_asset->image_url ) ) { 
            $image_url = $active_asset->image_url;
            $webp_url = $active_asset->image_url_webp;
            $destination_url = $active_asset->destination_url;
            $alt_text = $active_asset->alt_text;

            ob_start();
            include HH_CAMPAIGN_CMS_DIR . 'templates/widget-template.php';
            return ob_get_clean();

        } else {
            $slot_details = $wpdb->get_row( $wpdb->prepare(
                "SELECT fallback_behavior, default_image, default_link, default_alt FROM {$table_slots} WHERE slot_name = %s",
                $atts['slot']
            ) );  

            if ( $slot_details ) {
                if ( $slot_details->fallback_behavior === 'default' && ! empty( $slot_details->default_image ) ) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" alt="%s" /></a>',
                        esc_url( $slot_details->default_link ),
                        esc_url( $slot_details->default_image ),
                        esc_attr( $slot_details->default_alt )
                    );
                } elseif ( $slot_details->fallback_behavior === 'omit' ) {
                    return '';
                }
            }
        }
        return '';
    }
}