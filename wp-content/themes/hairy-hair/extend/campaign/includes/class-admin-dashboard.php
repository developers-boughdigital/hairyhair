<?php
// extend/campaign/includes/class-admin-dashboard.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HH_Admin_Dashboard {
    private static $instance = null;
    private $campaign_manager;
    private $asset_uploader;

    private function __construct() {
        $this->campaign_manager = HH_Campaign_Manager::get_instance();
        $this->asset_uploader = HH_Asset_Uploader::get_instance();

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'wp_ajax_hh_campaign_cms_save_campaign', array( $this, 'ajax_save_campaign' ) );
        add_action( 'wp_ajax_hh_campaign_cms_delete_campaign', array( $this, 'ajax_delete_campaign' ) );
        add_action( 'wp_ajax_hh_campaign_cms_pause_campaign', array( $this, 'ajax_pause_campaign' ) );
        add_action( 'wp_ajax_hh_campaign_cms_activate_campaign', array( $this, 'ajax_activate_campaign' ) );
        add_action( 'wp_ajax_hh_campaign_cms_save_slot', array( $this, 'ajax_save_slot' ) );
        add_action( 'wp_ajax_hh_campaign_cms_delete_slot', array( $this, 'ajax_delete_slot' ) );
        add_action( 'wp_ajax_hh_campaign_cms_upload_asset', array( $this, 'ajax_upload_asset' ) );

         
        add_action( 'wp_ajax_hh_campaign_cms_get_calendar_events', array( $this, 'ajax_get_calendar_events' ) ); 
        add_action( 'wp_ajax_nopriv_hh_campaign_cms_get_calendar_events', array( $this, 'ajax_get_calendar_events' ) );
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Adds the admin menu items for Campaign CMS.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Campaign CMS', 'hh-campaign-cms' ),
            __( 'Campaign CMS', 'hh-campaign-cms' ),
            'manage_options', // Only admins for now, can change to 'edit_others_posts' for editors
            'hh-campaign-cms',
            array( $this, 'render_dashboard_page' ),
            'dashicons-megaphone', // Icon for marketing/campaigns
            20
        );

        add_submenu_page(
            'hh-campaign-cms',
            __( 'Add New Campaign', 'hh-campaign-cms' ),
            __( 'Add New Campaign', 'hh-campaign-cms' ),
            'manage_options',
            'hh-campaign-cms-edit',
            array( $this, 'render_campaign_edit_page' )
        );

        add_submenu_page(
            'hh-campaign-cms',
            __( 'Manage Slots', 'hh-campaign-cms' ),
            __( 'Manage Slots', 'hh-campaign-cms' ),
            'manage_options',
            'hh-campaign-cms-slots',
            array( $this, 'render_slot_management_page' )
        );
        
        add_submenu_page(
            'hh-campaign-cms',
            __( 'Calendar View', 'hh-campaign-cms' ),
            __( 'Calendar View', 'hh-campaign-cms' ),
            'manage_options',
            'hh-campaign-cms-calendar', 
            array( $this, 'render_calendar_page' )
        );
        
    }

    /**
     * Enqueues admin scripts and styles.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_scripts( $hook ) {
        // Only load on our specific admin pages
        if ( strpos( $hook, 'hh-campaign-cms' ) === false ) {
            return;
        }

        wp_enqueue_style( 'hh-campaign-cms-admin-styles', HH_CAMPAIGN_CMS_URL . 'admin/css/admin-styles.css', array(), HH_CAMPAIGN_CMS_VERSION );
        wp_enqueue_script( 'hh-campaign-cms-admin-scripts', HH_CAMPAIGN_CMS_URL . 'admin/js/admin-scripts.js', array( 'jquery', 'wp-mediaelement', 'jquery-ui-datepicker' ), HH_CAMPAIGN_CMS_VERSION, true );
        wp_enqueue_media(); // For WordPress media uploader

        // Localize script for AJAX and other JS variables
        wp_localize_script( 'hh-campaign-cms-admin-scripts', 'hhCampaignCMS', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'hh_campaign_cms_nonce' ),
            'media_title' => __( 'Choose Campaign Image', 'hh-campaign-cms' ),
            'media_button' => __( 'Use This Image', 'hh-campaign-cms' ),
        ) );

        wp_enqueue_style('jquery-ui-datepicker-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');

        // Enqueue FullCalendar assets ONLY on the calendar page
        if ( $hook === 'toplevel_page_hh-campaign-cms-calendar' || $hook === 'campaign-cms_page_hh-campaign-cms-calendar' ) {
            // FullCalendar CSS (core)
            wp_enqueue_style( 'fullcalendar-main-css', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css', array(), '6.1.11' );
            // FullCalendar JS (core)
            wp_enqueue_script( 'fullcalendar-core-js', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js', array(), '6.1.11', true );

            // Localize script with AJAX URL for calendar events
            wp_localize_script( 'hh-campaign-cms-admin-scripts', 'hhCampaignCalendar', array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'hh_campaign_cms_calendar_nonce' ),
            ) );
        }

    }

    /**
     * Renders the main Campaign Dashboard page.
     */
    public function render_dashboard_page() {
        if ( ! current_user_can( 'manage_options' ) ) { // Security check
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'hh-campaign-cms' ) );
        }
        $campaigns = $this->campaign_manager->get_all_campaigns();
        include HH_CAMPAIGN_CMS_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Renders the Campaign Creation/Editing page.
     */
    public function render_campaign_edit_page() {
        if ( ! current_user_can( 'manage_options' ) ) { // Security check
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'hh-campaign-cms' ) );
        }

        $campaign_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
        $campaign = null;
        $campaign_assets = array();
        $all_slots = $this->campaign_manager->get_all_slots();

        if ( $campaign_id > 0 ) {
            $campaign = $this->campaign_manager->get_campaign( $campaign_id );
            if ( ! $campaign ) {
                wp_die( __( 'Campaign not found.', 'hh-campaign-cms' ) );
            }
            $campaign_assets_raw = $this->campaign_manager->get_campaign_assets( $campaign_id );
            // Reorganize assets for easier access in the form
            foreach ( $campaign_assets_raw as $asset ) {
                $campaign_assets[ $asset->slot_id . '_' . $asset->device_type ] = $asset;
            }
        }
        include HH_CAMPAIGN_CMS_DIR . 'admin/views/campaign-edit.php';
    }

    /**
     * Renders the Slot Management page.
     */
    public function render_slot_management_page() {
        if ( ! current_user_can( 'manage_options' ) ) { // Security check
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'hh-campaign-cms' ) );
        }
        $slots = $this->campaign_manager->get_all_slots();
        include HH_CAMPAIGN_CMS_DIR . 'admin/views/slot-management.php';
    }

    /**
     * Renders the Calendar View page.
     */
    public function render_calendar_page() {
        if ( ! current_user_can( 'manage_options' ) ) { // Security check
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'hh-campaign-cms' ) );
        }
        include HH_CAMPAIGN_CMS_DIR . 'admin/views/calendar.php';
    }

    // --- AJAX Handlers ---
    public function ajax_save_campaign() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }

        $status = $this->get_status($_POST['start_date'], $_POST['end_date'], $_POST['status']);   

        $campaign_id = isset( $_POST['campaign_id'] ) ? intval( $_POST['campaign_id'] ) : 0;
        $data = array(
            'campaign_name'      => sanitize_text_field( $_POST['campaign_name'] ),
            'internal_reference' => sanitize_text_field( $_POST['internal_reference'] ),
            'start_date'         => sanitize_text_field( $_POST['start_date'] ),
            'end_date'           => sanitize_text_field( $_POST['end_date'] ),
            'notes'              => sanitize_textarea_field( $_POST['notes'] ),
            'status'             => sanitize_text_field( $status ),
        );

        if ( $campaign_id ) {
            $result = $this->campaign_manager->update_campaign( $campaign_id, $data );
        } else {
            $campaign_id = $this->campaign_manager->create_campaign( $data );
            if($campaign_id == 'date_conflict') wp_send_json_error( array( 'message' => __( 'Campaign dates overlap with an existing campaign.', 'hh-campaign-cms' ) ) );
            if($campaign_id == 'invalid_date_range') wp_send_json_error( array( 'message' => __( 'End date must be greater than start date.', 'hh-campaign-cms' ) ) );
            $result = $campaign_id;
        }

        if ( $result ) {
            // Handle asset assignments
            $slot_ids = isset( $_POST['slot_ids'] ) ? $_POST['slot_ids'] : array(); // Now this comes as a flat array
            $image_urls = isset( $_POST['image_urls'] ) ? $_POST['image_urls'] : array();
            $webp_urls = isset( $_POST['webp_urls'] ) ? $_POST['webp_urls'] : array();
            $destination_urls = isset( $_POST['destination_urls'] ) ? $_POST['destination_urls'] : array();
            $alt_texts = isset( $_POST['alt_texts'] ) ? $_POST['alt_texts'] : array(); 

            // Loop through the submitted assets (JS sends them in a flat, parallel array structure)
            // Ensure all arrays have the same count before iterating to avoid errors
            $asset_count = count($slot_ids); // All parallel arrays should have this many elements

            for ($i = 0; $i < $asset_count; $i++) {
                $current_slot_id         = isset($slot_ids[$i]) ? intval($slot_ids[$i]) : 0;
                $current_image_url       = isset($image_urls[$i]) ? $image_urls[$i] : '';
                $current_webp_url        = isset($webp_urls[$i]) ? $webp_urls[$i] : null;
                $current_destination_url = isset($destination_urls[$i]) ? $destination_urls[$i] : null;
                $current_alt_text        = isset($alt_texts[$i]) ? $alt_texts[$i] : null; 
                $current_device_type     = 'desktop';

                if ($current_slot_id > 0 && !empty($current_image_url)) {
                    $this->campaign_manager->assign_campaign_asset(
                        $campaign_id,
                        $current_slot_id,
                        $current_image_url,
                        $current_webp_url,
                        $current_destination_url,
                        $current_alt_text, 
                        $current_device_type
                    );
                }
                // TODO: If an image was previously assigned and now is empty in the form,
                // you might want to add logic here to delete the asset from the database.
                // For now, if current_image_url is empty, it just skips this assignment.
            }

            wp_send_json_success( array( 'message' => __( 'Campaign saved successfully!', 'hh-campaign-cms' ), 'campaign_id' => $campaign_id ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to save campaign.', 'hh-campaign-cms' ) ) );
        }
    }

    public function get_status($start_date_raw, $end_date_raw, $posted_status) { 

        $start_date = strtotime( $start_date_raw );
        $end_date   = strtotime( $end_date_raw );
        $now        = time();
 
        $posted_status = strtolower( trim( $posted_status )); 
 
        $valid_statuses = [ 'upcoming', 'live', 'paused', 'expired' ];

        if ( ! $start_date || ! $end_date ) {
            $status = 'Invalid';  
        } elseif ( $end_date < $now ) {
            $status = 'expired';
        } elseif ( $start_date > $now ) {  
            if ( $posted_status === 'paused' ) {
                $status = 'paused';
            } else {
                $status = 'upcoming';
            }
        } else {  
            if ( $posted_status === 'paused' ) {
                $status = 'paused';
            } else {
                $status = 'live';  
            }
        }

        return $status;
    }

    public function ajax_delete_campaign() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }
        $campaign_id = isset( $_POST['campaign_id'] ) ? intval( $_POST['campaign_id'] ) : 0;
        if ( $campaign_id && $this->campaign_manager->delete_campaign( $campaign_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Campaign deleted successfully.', 'hh-campaign-cms' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete campaign.', 'hh-campaign-cms' ) ) );
        }
    }

    public function ajax_pause_campaign() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }
        $campaign_id = isset( $_POST['campaign_id'] ) ? intval( $_POST['campaign_id'] ) : 0;
        if ( $campaign_id && $this->campaign_manager->pause_campaign( $campaign_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Campaign paused.', 'hh-campaign-cms' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to pause campaign.', 'hh-campaign-cms' ) ) );
        }
    }

    public function ajax_activate_campaign() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }

        $campaign_id = isset( $_POST['campaign_id'] ) ? intval( $_POST['campaign_id'] ) : 0;

        if ( $campaign_id && $this->campaign_manager->activate_campaign( $campaign_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Campaign activated.', 'hh-campaign-cms' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to activate campaign.', 'hh-campaign-cms' ) ) );
        }
    }

    public function ajax_save_slot() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        } 

        $slot_id = isset( $_POST['slot_id'] ) ? intval( $_POST['slot_id'] ) : 0;
        $data = array( 
            'slot_name'             => sanitize_text_field( $_POST['slot_name'] ), 
            'slot_location'         => sanitize_text_field( $_POST['slot_location'] ),
            'dimensions'            => sanitize_text_field( $_POST['dimensions'] ), 
            'fallback_behavior'     => sanitize_text_field( $_POST['fallback_behavior'] ),
            'default_image'         => wp_kses_post( $_POST['image_urls'] ),
            'default_image_webp'    => wp_kses_post( $_POST['webp_urls'] ),
            'default_link'          => wp_kses_post( $_POST['default_link'] ),
            'default_alt'           => wp_kses_post( $_POST['default_alt'] ), 
        );
 
  
        

        if ( $slot_id ) {
            if($slot_id == '15' ) wp_send_json_error( array( 'message' => __( 'You cannot modify Home slot.', 'hh-campaign-cms' ) ) );
 
            $result = $this->campaign_manager->update_slot( $slot_id, $data );
        } else {    
 
            if($this->campaign_manager->get_slot_id_by_name($data['slot_name'])) {
                wp_send_json_error( array( 'message' => __( 'Failed to save slot. Slot name already exist.', 'hh-campaign-cms' ) ) );
            }
            
            $slot_id = $this->campaign_manager->create_slot(
                $data['slot_name'],
                $data['slot_location'],
                $data['dimensions'],
                $data['fallback_behavior'],
                $data['default_image'],
                $data['default_image_webp'],
                $data['default_link'],
                $data['default_alt']
            );
            $result = $slot_id;
            
        }

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Slot saved successfully!', 'hh-campaign-cms' ), 'slot_id' => $slot_id ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to save slot. Slot name might already exist.', 'hh-campaign-cms' ) ) );
        }
    }

    public function ajax_delete_slot() {
        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }
        $slot_id = isset( $_POST['slot_id'] ) ? intval( $_POST['slot_id'] ) : 0;
        if ( $slot_id && $this->campaign_manager->delete_slot( $slot_id ) ) {
            wp_send_json_success( array( 'message' => __( 'Slot deleted successfully.', 'hh-campaign-cms' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete slot.', 'hh-campaign-cms' ) ) );
        }
    }

    /**
     * Handles AJAX request to upload/process an image asset for a campaign slot.
     * This now expects an 'attachment_id' from the WordPress Media Library.
     */
    public function ajax_upload_asset() {

        check_ajax_referer( 'hh_campaign_cms_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }

        $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
        if ( ! $attachment_id ) {
            wp_send_json_error( array( 'message' => __( 'No media attachment ID provided.', 'hh-campaign-cms' ) ) );
        }

        // Sanitize names for file naming and pass device type
        $campaign_name = isset( $_POST['campaign_name'] ) ? sanitize_text_field( $_POST['campaign_name'] ) : 'unknown-campaign';
        $slot_name     = isset( $_POST['slot_name'] ) ? sanitize_text_field( $_POST['slot_name'] ) : 'unknown-slot';
        $slot_location     = isset( $_POST['slot_location'] ) ? sanitize_text_field( $_POST['slot_location'] ) : 'unknown-location';
        $device_type   = isset( $_POST['device_type'] ) ? sanitize_text_field( $_POST['device_type'] ) : 'desktop';


        // Call the asset uploader to process the attachment ID from media library
        // This method should be implemented in HH_Asset_Uploader to fetch the attachment
        // and copy/process it to your custom directory, generating WebP.
        $uploaded_data = $this->asset_uploader->process_attachment_for_campaign( $attachment_id, $campaign_name, $slot_name, $device_type );

        if ( is_wp_error( $uploaded_data ) ) {
            wp_send_json_error( array( 'message' => $uploaded_data->get_error_message() ) );
        } else {
            wp_send_json_success( $uploaded_data );
        }
    }

    /**
     * AJAX handler to fetch all campaigns formatted for FullCalendar.
     */
    public function ajax_get_calendar_events() {
        check_ajax_referer( 'hh_campaign_cms_calendar_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'hh-campaign-cms' ) ) );
        }
        
        $campaigns = $this->campaign_manager->get_all_campaigns();
        $events = array();

        if ( $campaigns ) {
            foreach ( $campaigns as $campaign ) {
                $status_class = '';
                switch ( $campaign->status ) {
                    case 'live':
                        $status_class = 'calendar-event-live';
                        break;
                    case 'upcoming':
                        $status_class = 'calendar-event-upcoming';
                        break;
                    case 'paused':
                        $status_class = 'calendar-event-paused';
                        break;
                    case 'expired':
                        $status_class = 'calendar-event-expired';
                        break;
                    default:
                        $status_class = 'calendar-event-default';
                        break;
                }

                $events[] = array(
                    'id'        => $campaign->id,
                    'title'     => $campaign->campaign_name . ' (' . ucfirst($campaign->status) . ')',
                    'start'     => $campaign->start_date,
                    'end'       => $campaign->end_date,
                    'url'       => admin_url( 'admin.php?page=hh-campaign-cms-edit&id=' . $campaign->id ), // Link to edit campaign
                    'className' => $status_class, // CSS class for coloring events by status
                    'extendedProps' => array(
                        'internal_reference' => $campaign->internal_reference,
                        'notes' => $campaign->notes,
                        'status' => $campaign->status,
                    ),
                );
            }
        }
        wp_send_json( $events ); // FullCalendar expects JSON array
    }
}
