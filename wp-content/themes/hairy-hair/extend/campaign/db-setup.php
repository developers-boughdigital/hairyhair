<?php
// extend/campaign/db-setup.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

error_reporting(E_ALL);

/**
 * Creates custom database tables for the Campaign CMS.
 * This function is called when the theme is activated.
 */
function hh_campaign_cms_create_tables() {
     
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); // For dbDelta()

    $charset_collate = $wpdb->get_charset_collate();

    $sql = [];

    // Campaigns table
    $sql[] = "CREATE TABLE {$wpdb->prefix}campaign_cms_campaigns (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        campaign_name VARCHAR(255) NOT NULL,
        internal_reference VARCHAR(255) DEFAULT NULL,
        start_date DATETIME NOT NULL,
        end_date DATETIME NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'upcoming', -- 'upcoming', 'live', 'expired', 'paused'
        notes TEXT DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY start_date (start_date),
        KEY end_date (end_date),
        KEY status (status)
    ) $charset_collate;";

    // Widget Slots table
    $sql[] = "CREATE TABLE {$wpdb->prefix}campaign_cms_widget_slots (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        slot_name VARCHAR(255) NOT NULL,
        slot_location VARCHAR(255) NOT NULL,
        dimensions VARCHAR(50) NOT NULL,
        fallback_behavior VARCHAR(20) NOT NULL DEFAULT 'default', -- 'default', 'omit'
        default_content LONGTEXT DEFAULT NULL, -- HTML for default content
        PRIMARY KEY (id),
        UNIQUE KEY slot_name (slot_name)
    ) $charset_collate;";

    // Campaign Assets table
    $sql[] = "CREATE TABLE {$wpdb->prefix}campaign_cms_campaign_assets (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        campaign_id BIGINT(20) UNSIGNED NOT NULL,
        slot_id BIGINT(20) UNSIGNED NOT NULL,
        image_url VARCHAR(255) NOT NULL,
        image_url_webp VARCHAR(255) DEFAULT NULL, -- Optimized WebP version
        destination_url VARCHAR(255) DEFAULT NULL,
        alt_text VARCHAR(255) DEFAULT NULL,
        device_type VARCHAR(20) NOT NULL DEFAULT 'desktop', -- 'desktop', 'mobile'
        PRIMARY KEY (id),
        UNIQUE KEY campaign_slot_device (campaign_id, slot_id, device_type),
        FOREIGN KEY (campaign_id) REFERENCES {$wpdb->prefix}campaign_cms_campaigns(id) ON DELETE CASCADE,
        FOREIGN KEY (slot_id) REFERENCES {$wpdb->prefix}campaign_cms_widget_slots(id) ON DELETE CASCADE
    ) $charset_collate;";

    // Execute all table creation queries
    foreach ($sql as $query) {
        dbDelta($query);
    }

}
// hh_campaign_cms_create_tables();
if (get_option('campaign_tables') === false) {
    hh_campaign_cms_create_tables();
    add_option('campaign_tables', 1);  
} 