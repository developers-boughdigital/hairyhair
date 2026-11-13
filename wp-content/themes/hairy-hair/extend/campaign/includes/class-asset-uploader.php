<?php
// extend/campaign/includes/class-asset-uploader.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HH_Asset_Uploader {
    private static $instance = null;
    private $upload_dir;
    private $upload_url;

    private function __construct() {
        $upload_info = wp_upload_dir();
        // Custom directory for campaign assets
        $this->upload_dir = $upload_info['basedir'] . '/hh-campaign-assets/';
        $this->upload_url = $upload_info['baseurl'] . '/hh-campaign-assets/';

        // Ensure the custom upload directory exists
        if ( ! wp_mkdir_p( $this->upload_dir ) ) {
            error_log( 'HH_Campaign_CMS: Failed to create upload directory: ' . $this->upload_dir );
            // You might want to throw an exception or set a flag here
            // to prevent further operations if the directory cannot be created.
        }
    }

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Processes an existing WordPress media attachment for campaign usage.
     * Copies the image to the custom campaign assets directory,
     * renames it, and generates a WebP version.
     *
     * @param int    $attachment_id The ID of the WordPress media attachment.
     * @param string $campaign_name Used for naming convention.
     * @param string $slot_name Used for naming convention.
     * @param string $device_type 'desktop' or 'mobile'.
     * @return array|WP_Error Array with 'original_url' and 'webp_url' on success, WP_Error on failure.
     */
    public function process_attachment_for_campaign( $attachment_id, $campaign_name, $slot_name, $device_type ) {
        if ( ! function_exists( 'wp_get_image_editor' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        $original_path = get_attached_file( $attachment_id );
        if ( ! $original_path || ! file_exists( $original_path ) ) {
            return new WP_Error( 'file_not_found', __( 'Original media file not found in Media Library.', 'hh-campaign-cms' ) );
        }

        // Sanitize names for file paths and add device type
        $campaign_slug = sanitize_title( $campaign_name );
        $slot_slug = sanitize_title( $slot_name );
        $device_slug = sanitize_title( $device_type ); // e.g., 'desktop', 'mobile'
        $date_suffix = current_time( 'YmdHis' ); // Unique timestamp

        $file_extension = pathinfo( $original_path, PATHINFO_EXTENSION );
        // Construct new filename with all relevant parts
        $new_filename = "{$campaign_slug}-{$slot_slug}-{$device_slug}-{$date_suffix}.{$file_extension}";

        $destination_path = trailingslashit( $this->upload_dir ) . $new_filename;
        $destination_url = trailingslashit( $this->upload_url ) . $new_filename;
        $webp_url = null;
        $webp_path = null;

        // Ensure the custom upload directory exists before copying
        if ( ! wp_mkdir_p( $this->upload_dir ) ) {
            return new WP_Error( 'dir_creation_failed', __( 'Could not create campaign assets directory.', 'hh-campaign-cms' ) );
        }

        // Copy the original file to the custom directory
        if ( ! copy( $original_path, $destination_path ) ) {
            return new WP_Error( 'file_copy_failed', __( 'Failed to copy file to campaign assets directory.', 'hh-campaign-cms' ) );
        }

        // Generate WebP version from the copied file
        $editor = wp_get_image_editor( $destination_path );
        if ( ! is_wp_error( $editor ) ) {
            $webp_path = str_replace( '.' . $file_extension, '.webp', $destination_path );
            $editor->set_quality( 80 ); // Adjust quality as needed for WebP
            if ( $editor->save( $webp_path, 'image/webp' ) ) {
                $webp_url = str_replace( '.' . $file_extension, '.webp', $destination_url );
            } else {
                error_log( 'HH_Campaign_CMS: Failed to generate WebP for ' . $destination_path . ' Error: ' . ( is_wp_error( $editor ) ? $editor->get_error_message() : 'Unknown' ) );
            }
        } else {
            error_log( 'HH_Campaign_CMS: Failed to get image editor for ' . $destination_path . ' Error: ' . $editor->get_error_message() );
        }

        return array(
            'original_url' => $destination_url,
            'webp_url'     => $webp_url,
            // You can return paths too if needed for backend operations
            // 'path'         => $destination_path,
            // 'webp_path'    => $webp_path,
        );
    }

    /**
     * Original handle_upload method (if still needed for direct file uploads).
     * This might not be used if all uploads go through the WordPress Media Library.
     * I've kept it for completeness, but it's likely not the source of your current issue.
     *
     * @param array  $file_data Array from $_FILES.
     * @param string $campaign_name Used for naming convention.
     * @param string $slot_name Used for naming convention.
     * @return array|WP_Error Array with 'original_url' and 'webp_url' on success, WP_Error on failure.
     */
    public function handle_upload( $file_data, $campaign_name, $slot_name ) {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        if ( ! function_exists( 'wp_get_image_editor' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        // Sanitize names for file paths
        $campaign_slug = sanitize_title( $campaign_name );
        $slot_slug = sanitize_title( $slot_name );
        $date_suffix = current_time( 'YmdHis' ); // Unique suffix

        $file_extension = pathinfo( $file_data['name'], PATHINFO_EXTENSION );
        $new_filename = "{$campaign_slug}-{$slot_slug}-{$date_suffix}.{$file_extension}";

        $upload_overrides = array(
            'test_form'  => false,
            'upload_dir' => $this->upload_dir,
            'upload_url' => $this->upload_url,
        );

        // Temporarily change filename for wp_handle_upload to ensure custom naming
        $original_file_name = $file_data['name'];
        $file_data['name'] = $new_filename;

        $uploaded_file = wp_handle_upload( $file_data, $upload_overrides );

        $file_data['name'] = $original_file_name; // Restore original name

        if ( isset( $uploaded_file['error'] ) && $uploaded_file['error'] ) {
            return new WP_Error( 'upload_error', $uploaded_file['error'] );
        }

        $original_url = $uploaded_file['url'];
        $original_path = $uploaded_file['file'];
        $webp_url = null;

        $editor = wp_get_image_editor( $original_path );
        if ( ! is_wp_error( $editor ) ) {
            $webp_path = str_replace( '.' . $file_extension, '.webp', $original_path );
            $editor->set_quality( 80 );
            if ( $editor->save( $webp_path, 'image/webp' ) ) {
                $webp_url = str_replace( '.' . $file_extension, '.webp', $original_url );
            }
        }

        return array(
            'original_url' => $original_url,
            'webp_url'     => $webp_url,
            'path'         => $original_path,
        );
    }
}
