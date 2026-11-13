<?php
// extend/campaign/includes/class-campaign-manager.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HH_Campaign_Manager {
    private static $instance = null;
    private $table_campaigns;
    private $table_assets;
    private $table_slots;

    private function __construct() {
       
        global $wpdb;
        $this->table_campaigns = $wpdb->prefix . 'campaign_cms_campaigns';
        $this->table_assets    = $wpdb->prefix . 'campaign_cms_campaign_assets';
        $this->table_slots     = $wpdb->prefix . 'campaign_cms_widget_slots';

        // Schedule hourly event to update campaign statuses
        add_action( 'init', array( $this, 'schedule_campaign_status_check' ) );
        add_action( 'hh_campaign_cms_hourly_event', array( $this, 'update_campaign_statuses' ) );        
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Creates a new campaign record.
     *
     * @param array $data Campaign data.
     * @return int|bool Insert ID on success, false on failure.
     */
    public function create_campaign( $data ) {
        global $wpdb;

        $defaults = array(
            'campaign_name'      => '',
            'internal_reference' => '',
            'start_date'         => current_time( 'mysql' ),
            'end_date'           => current_time( 'mysql' ),
            'notes'              => '',
            'status'             => 'upcoming',
        );
        $data = wp_parse_args( $data, $defaults );

        // Check for overlapping campaigns
        $start_date = $data['start_date'];
        $end_date   = $data['end_date'];

        if ( strtotime( $end_date ) <= strtotime( $start_date ) ) {
            return 'invalid_date_range';
        }

        $overlap_query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_campaigns}
            WHERE (%s <= end_date AND %s >= start_date)",
            $start_date, $end_date
        );

        $overlap_count = $wpdb->get_var( $overlap_query );

        if ( $overlap_count > 0 ) {
            return 'date_conflict';
        }

        // Insert if no overlap
        $inserted = $wpdb->insert(
            $this->table_campaigns,
            array(
                'campaign_name'      => sanitize_text_field( $data['campaign_name'] ),
                'internal_reference' => sanitize_text_field( $data['internal_reference'] ),
                'start_date'         => $start_date,
                'end_date'           => $end_date,
                'notes'              => sanitize_textarea_field( $data['notes'] ),
                'status'             => $data['status'],
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s' )
        );

        return $inserted ? $wpdb->insert_id : false;
    }


    /**
     * Retrieves a single campaign by ID.
     *
     * @param int $campaign_id
     * @return object|null Campaign object or null if not found.
     */
    public function get_campaign( $campaign_id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_campaigns} WHERE id = %d", $campaign_id ) );
    }

    /**
     * Retrieves all campaigns.
     *
     * @return array Array of campaign objects.
     */
    public function get_all_campaigns() {
        global $wpdb; 
        return $wpdb->get_results( "SELECT * FROM {$this->table_campaigns} ORDER BY start_date DESC" );
    }

    /**
     * Updates an existing campaign.
     *
     * @param int $campaign_id
     * @param array $data Campaign data to update.
     * @return int|false Number of rows updated on success, false on failure.
     */
    public function update_campaign( $campaign_id, $data ) {
        global $wpdb;
        return $wpdb->update(
            $this->table_campaigns,
            array(
                'campaign_name'      => sanitize_text_field( $data['campaign_name'] ),
                'internal_reference' => sanitize_text_field( $data['internal_reference'] ),
                'start_date'         => $data['start_date'],
                'end_date'           => $data['end_date'],
                'notes'              => sanitize_textarea_field( $data['notes'] ),
                'status'             => sanitize_text_field( $data['status'] ),
                'updated_at'         => current_time( 'mysql' ),
            ),
            array( 'id' => $campaign_id ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
            array( '%d' )
        );
    }

    /**
     * Deletes a campaign.
     *
     * @param int $campaign_id
     * @return int|false Number of rows deleted on success, false on failure.
     */
    public function delete_campaign( $campaign_id ) {
        global $wpdb;
        return $wpdb->delete( $this->table_campaigns, array( 'id' => $campaign_id ), array( '%d' ) );
    }

    /**
     * Schedules the hourly event for campaign status updates.
     */
    public function schedule_campaign_status_check() {
        if ( ! wp_next_scheduled( 'hh_campaign_cms_hourly_event' ) ) {
            wp_schedule_event( time(), 'hourly', 'hh_campaign_cms_hourly_event' );
        }
    }

    /**
     * Updates campaign statuses based on current date/time.
     */
    public function update_campaign_statuses() {
        global $wpdb;
        $current_time = current_time( 'mysql' );

        // Update 'upcoming' to 'live'
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table_campaigns} SET status = 'live' WHERE status = 'upcoming' AND start_date <= %s",
                $current_time
            )
        );

        // Update 'live' to 'expired'
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$this->table_campaigns} SET status = 'expired' WHERE status = 'live' AND end_date < %s",
                $current_time
            )
        );
    }

    /**
     * Manually pauses a campaign.
     *
     * @param int $campaign_id
     * @return int|false Number of rows updated on success, false on failure.
     */
    public function pause_campaign( $campaign_id ) {
        global $wpdb;
        return $wpdb->update(
            $this->table_campaigns,
            array( 'status' => 'paused' ),
            array( 'id' => $campaign_id ),
            array( '%s' ),
            array( '%d' )
        );
    }

    /**
     * Manually activates/re-activates a campaign.
     * Sets status based on current date relative to start/end dates.
     *
     * @param int $campaign_id
     * @return int|false Number of rows updated on success, false on failure.
     */
    public function activate_campaign( $campaign_id ) {
        global $wpdb;
        $campaign = $this->get_campaign( $campaign_id );
        if ( $campaign ) {
            $current_time = current_time( 'mysql' );
            $new_status = 'expired'; // Default to expired if dates don't match
            if ( $campaign->start_date <= $current_time && $campaign->end_date >= $current_time ) {
                $new_status = 'live';
            } elseif ( $campaign->start_date > $current_time ) {
                $new_status = 'upcoming';
            } 

            return $wpdb->update(
                $this->table_campaigns,
                array( 'status' => $new_status ),
                array( 'id' => $campaign_id ),
                array( '%s' ),
                array( '%d' )
            );
        }
        return false;
    }

    /**
     * Assigns or updates a campaign asset for a specific slot and device type.
     *
     * @param int    $campaign_id
     * @param int    $slot_id
     * @param string $image_url Original image URL.
     * @param string $image_url_webp Optional WebP image URL.
     * @param string $destination_url Optional click destination URL.
     * @param string $alt_text Optional alt text for the image. 
     * @param string $device_type 'desktop' or 'mobile'.
     * @return int|false Insert ID on success, false on failure.
     */
    public function assign_campaign_asset( $campaign_id, $slot_id, $image_url, $image_url_webp = null, $destination_url = null, $alt_text = null, $device_type = 'desktop' ) {
        global $wpdb;

        // Check if an asset already exists for this campaign, slot, and device type
        $existing_asset_id = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$this->table_assets} WHERE campaign_id = %d AND slot_id = %d AND device_type = %s",
            $campaign_id, $slot_id, $device_type
        ) );

        $data = array(
            'campaign_id'     => $campaign_id,
            'slot_id'         => $slot_id,
            'image_url'       => esc_url_raw( $image_url ),
            'image_url_webp'  => !empty($image_url_webp) ? esc_url_raw( $image_url_webp ) : null,
            'destination_url' => !empty($destination_url) ? esc_url_raw( $destination_url ) : null,
            'alt_text'        => sanitize_text_field( $alt_text ),
            'device_type'     => sanitize_text_field( $device_type ),
        );

        $format = array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' );

        if ( $existing_asset_id ) {
            // Update existing asset
            return $wpdb->update( $this->table_assets, $data, array( 'id' => $existing_asset_id ), $format, array( '%d' ) );
        } else {
            // Insert new asset
            return $wpdb->insert( $this->table_assets, $data, $format );
        }
    }

    /**
     * Get all assets assigned to a specific campaign.
     *
     * @param int $campaign_id
     * @return array Array of asset objects.
     */
    public function get_campaign_assets( $campaign_id ) {
        global $wpdb;
        return $wpdb->get_results( $wpdb->prepare(
            "SELECT ca.*, ws.slot_name, ws.slot_location, ws.dimensions
             FROM {$this->table_assets} ca
             JOIN {$this->table_slots} ws ON ca.slot_id = ws.id
             WHERE ca.campaign_id = %d",
            $campaign_id
        ) );
    }

    /**
     * Retrieves details for a single widget slot.
     *
     * @param int $slot_id
     * @return object|null Slot object or null.
     */
    public function get_slot_details( $slot_id ) {
        global $wpdb;
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_slots} WHERE id = %d", $slot_id ) );
    }

    /**
     * Retrieves details for a single widget slot by name.
     *
     * @param int $name
     * @return object|null Slot object or null.
     */
    public function get_slot_id_by_name( $name ) {
        global $wpdb; 
        return $wpdb->get_var( "SELECT id FROM {$this->table_slots} WHERE slot_name = '$name'" );
    }

    /**
     * Retrieves all defined widget slots.
     *
     * @return array Array of slot objects.
     */
    public function get_all_slots() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM {$this->table_slots} ORDER BY id ASC" );
    }

    /**
     * Creates a new widget slot.
     *
     * @param string $slot_name
     * @param string $dimensions
     * @param string $fallback_behavior
     * @param string $default_content
     * @return int|bool Insert ID on success, false on failure.
     */ 
    public function create_slot( 
        $slot_name, 
        $slot_location, 
        $dimensions, 
        $fallback_behavior= 'default', 
        $default_image = '', 
        $default_image_webp = '', 
        $default_link = '#', 
        $default_alt = '' 
    ) { 
        global $wpdb;
        $inserted = $wpdb->insert(
            $this->table_slots,
            array(
                'slot_name'         => sanitize_text_field( $slot_name ),
                'slot_location'     => sanitize_text_field( $slot_location ),
                'dimensions'        => sanitize_text_field( $dimensions ),
                'fallback_behavior' => sanitize_text_field( $fallback_behavior ),
                'default_image'     => sanitize_text_field( $default_image ),
                'default_image_webp'=> sanitize_text_field( $default_image_webp ),
                'default_link'      => sanitize_text_field( $default_link ),
                'default_alt'       => sanitize_text_field( $default_alt ),
            ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
        );
        return $inserted ? $wpdb->insert_id : false;
    }


    /**
     * Updates an existing widget slot.
     *
     * @param int $slot_id
     * @param array $data Slot data to update.
     * @return int|false Number of rows updated on success, false on failure.
     */
    public function update_slot( $slot_id, $data ) { 
        global $wpdb; 
        return $wpdb->update(
            $this->table_slots,
            array(
                'slot_location'     => sanitize_text_field( $data['slot_location'] ),
                'dimensions'        => sanitize_text_field( $data['dimensions'] ),
                'fallback_behavior' => sanitize_text_field( $data['fallback_behavior'] ),
                'default_image'     => sanitize_text_field( $data['default_image'] ),
                'default_image_webp'=> sanitize_text_field( $data['default_image_webp'] ),
                'default_link'      => sanitize_text_field( $data['default_link'] ),
                'default_alt'       => sanitize_text_field( $data['default_alt'] ),
            ),
            array( 'id' => $slot_id ),
            array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
            array( '%d' )
        ); 
    }

    /**
     * Deletes a widget slot.
     *
     * @param int $slot_id
     * @return int|false Number of rows deleted on success, false on failure.
     */
    public function delete_slot( $slot_id ) {
        global $wpdb;
        return $wpdb->delete( $this->table_slots, array( 'id' => $slot_id ), array( '%d' ) );
    }
}