<?php
// extend/campaign/campaign.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Define constants for paths and URLs
define( 'HH_CAMPAIGN_CMS_DIR', get_template_directory() . '/extend/campaign/' );
define( 'HH_CAMPAIGN_CMS_URL', get_template_directory_uri() . '/extend/campaign/' );
define( 'HH_CAMPAIGN_CMS_VERSION', '1.0.0' );

// Include required database setup
require_once HH_CAMPAIGN_CMS_DIR . 'db-setup.php';


// Include core classes
require_once HH_CAMPAIGN_CMS_DIR . 'includes/class-campaign-manager.php';
require_once HH_CAMPAIGN_CMS_DIR . 'includes/class-asset-uploader.php';
require_once HH_CAMPAIGN_CMS_DIR . 'includes/class-shortcode-generator.php';
require_once HH_CAMPAIGN_CMS_DIR . 'includes/class-admin-dashboard.php';

/**
 * Initialize all Campaign CMS components.
 * This runs after WordPress has loaded its core functionalities.
 */
function hh_campaign_cms_init() {
    HH_Campaign_Manager::get_instance();
    HH_Asset_Uploader::get_instance();
    HH_Shortcode_Generator::get_instance();
    HH_Admin_Dashboard::get_instance();
}
add_action( 'after_setup_theme', 'hh_campaign_cms_init' ); // Use plugins_loaded for theme components as well

// Optionally, handle database creation if the theme is already active and tables don't exist
// This is a fallback in case the 'after_switch_theme' hook was missed (e.g., during development)
function hh_campaign_cms_check_tables_on_load() {
    global $wpdb;
    $table_name_campaigns = $wpdb->prefix . 'campaign_cms_campaigns';
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name_campaigns'" ) != $table_name_campaigns ) {
        hh_campaign_cms_create_tables(); // Call the table creation function
    }
}
add_action( 'admin_init', 'hh_campaign_cms_check_tables_on_load' );

 

add_action( 'admin_enqueue_scripts', 'load_datetimepicker_assets' );
function load_datetimepicker_assets( $hook ) { 
    // if ( $hook !== 'your_plugin_page' ) return;
 
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'jquery-ui-slider' );

    // Timepicker addon (from external or enqueue your own)
    wp_enqueue_script(
        'jquery-ui-timepicker-addon',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js',
        array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
        '1.6.3',
        true
    );

    wp_enqueue_style(
        'jquery-ui',
        'https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css'
    );

    wp_enqueue_style(
        'jquery-ui-timepicker-addon',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css'
    );

    // Init script
    wp_add_inline_script( 'jquery-ui-timepicker-addon', "
        jQuery(document).ready(function($) {
            var now = new Date();

            $('#start_date').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss',
                minDate: now,
                onClose: function(selectedDateTime) {
                    // Set minDate for end_date when start_date is selected
                    var selectedDate = $(this).datetimepicker('getDate');
                    if (selectedDate) {
                        $('#end_date').datetimepicker('option', 'minDate', selectedDate);
                    }
                }
            });

            $('#end_date').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss',
                minDate: now
            });
        });
    " );

} 



add_action( 'admin_enqueue_scripts', 'enqueue_fullcalendar_assets' );
function enqueue_fullcalendar_assets( $hook ) {
    // if ( $hook !== 'toplevel_page_your_menu_slug' ) return;

    wp_enqueue_style( 'fullcalendar-css', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' );
    wp_enqueue_script( 'fullcalendar-js', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js', array('jquery'), null, true );
    wp_enqueue_script( 'your-calendar-init', plugin_dir_url(__FILE__) . 'js/calendar-init.js', array('fullcalendar-js', 'jquery'), null, true );

    wp_localize_script( 'your-calendar-init', 'campaignCalendarData', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('load_campaigns_nonce'),
    ));
}
