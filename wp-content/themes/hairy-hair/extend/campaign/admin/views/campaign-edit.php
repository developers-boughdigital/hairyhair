<?php
// extend/campaign/admin/views/campaign-edit.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

 

$is_editing = isset( $campaign ) && $campaign->id > 0;
$page_title = $is_editing ? __( 'Edit Campaign', 'hh-campaign-cms' ) : __( 'Add New Campaign', 'hh-campaign-cms' );
?>
<!-- Cropper CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />

<!-- Cropper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div class="wrap">
    <h1><?php echo esc_html( $page_title ); ?></h1>

    <form id="hh-campaign-cms-form" method="post" action="">
        <input type="hidden" name="action" value="hh_campaign_cms_save_campaign">
        <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'hh_campaign_cms_nonce' ) ); ?>">
        <input type="hidden" name="campaign_id" value="<?php echo esc_attr( $is_editing ? $campaign->id : 0 ); ?>">

        <div class="campaign-slot-box">
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="campaign_name"><?php esc_html_e( 'Campaign Name', 'hh-campaign-cms' ); ?></label></th>
                        <td><input type="text" name="campaign_name" id="campaign_name" class="regular-text" value="<?php echo esc_attr( $is_editing ? $campaign->campaign_name : '' ); ?>" required /></td>
                    </tr>
                    <!-- <tr>
                        <th scope="row"><label for="internal_reference"><?php esc_html_e( 'Internal Reference', 'hh-campaign-cms' ); ?></label></th>
                        <td><input type="text" name="internal_reference" id="internal_reference" class="regular-text" value="<?php echo esc_attr( $is_editing ? $campaign->internal_reference : '' ); ?>" /></td>
                    </tr> -->
                    <tr>
                        <th scope="row"><label for="start_date"><?php esc_html_e( 'Start Date/Time', 'hh-campaign-cms' ); ?></label></th>
                        <td><input type="text" class="datetime-picker" name="start_date" id="start_date" value="<?php echo esc_attr( $is_editing ? date( 'Y-m-d\TH:i', strtotime( $campaign->start_date ) ) : '' ); ?>" required autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="end_date"><?php esc_html_e( 'End Date/Time', 'hh-campaign-cms' ); ?></label></th>
                        <td><input type="text" class="datetime-picker" name="end_date" id="end_date" value="<?php echo esc_attr( $is_editing ? date( 'Y-m-d\TH:i', strtotime( $campaign->end_date ) ) : '' ); ?>" required autocomplete="off" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="status"><?php esc_html_e( 'Status', 'hh-campaign-cms' ); ?></label></th>
                        <td>
                            <select name="status" id="status">
                                <option value="upcoming" <?php selected( $is_editing && $campaign->status === 'upcoming' ); ?>>
                                    <?php esc_html_e( 'Upcoming', 'hh-campaign-cms' ); ?>
                                </option>
                                <option value="live" <?php selected( $is_editing && $campaign->status === 'live' ); ?>>
                                    <?php esc_html_e( 'Live', 'hh-campaign-cms' ); ?>
                                </option>
                                <option value="paused" <?php selected( $is_editing && $campaign->status === 'paused' ); ?>>
                                    <?php esc_html_e( 'Paused', 'hh-campaign-cms' ); ?>
                                </option>
                                <option value="expired" <?php selected( $is_editing && $campaign->status === 'expired' ); ?> disabled>
                                    <?php esc_html_e( 'Expired (Auto)', 'hh-campaign-cms' ); ?>
                                </option>
                            </select> 
                            Status will be automatically
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="notes"><?php esc_html_e( 'Internal Notes', 'hh-campaign-cms' ); ?></label></th>
                        <td><textarea name="notes" id="notes" rows="5" class="large-text"><?php echo esc_textarea( $is_editing ? $campaign->notes : '' ); ?></textarea></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2><?php esc_html_e( 'Assign Assets to Widget Slots', 'hh-campaign-cms' ); ?></h2>
        <p><?php esc_html_e( 'Select the widget slots where this campaign will display its assets.', 'hh-campaign-cms' ); ?></p>
 

        <?php if ( ! empty( $all_slots ) ) : ?>
            <div id="campaign-slots-container" style="display: flex; flex-wrap: wrap; gap: 1%;">
                <?php foreach ( $all_slots as $slot ) :
                    $current_asset_desktop = isset( $campaign_assets[ $slot->id . '_desktop' ] ) ? $campaign_assets[ $slot->id . '_desktop' ] : null;
                    $current_asset_mobile = isset( $campaign_assets[ $slot->id . '_mobile' ] ) ? $campaign_assets[ $slot->id . '_mobile' ] : null;
                ?>
                    <div class="campaign-slot-box" id="slot_<?php echo esc_attr( $slot->id ); ?>" style="width: 46%;">
                        <h3>
                            <?php echo esc_html( $slot->slot_name ); ?> (<?php echo esc_html( $slot->dimensions ); ?>)<br>
                            <small><?php echo esc_html( $slot->slot_location ); ?></small>
                        </h3>
                        <input type="hidden" name="slot_ids[]" value="<?php echo esc_attr( $slot->id ); ?>" />
  
                        <table class="form-table">
                            <tr>
                                <td style="width:30%">
                                    <input type="hidden" class="hh-campaign-image-url" name="image_urls[]" value="<?php echo esc_url( $current_asset_desktop ? $current_asset_desktop->image_url : '' ); ?>" />
                                    <input type="hidden" class="hh-campaign-webp-url" name="webp_urls[]" value="<?php echo esc_url( $current_asset_desktop ? $current_asset_desktop->image_url_webp : '' ); ?>" />
                                    
                                    <div class="hh-campaign-image-preview" id="slot_<?php echo esc_attr( $slot->id ); ?>_desktop_preview">
                                        <?php if ( $current_asset_desktop ) : ?>
                                            <img src="<?php echo esc_url( $current_asset_desktop->image_url ); ?>" style="max-width:200px; height:auto;" />
                                        <?php endif; ?>
                                    </div>
                                    <p>
                                        <button 
                                            type="button" 
                                            class="button hh-campaign-upload-button" 
                                            data-slot-id="slot_<?php echo esc_attr( $slot->id ); ?>_desktop_preview" 
                                            data-device-type="desktop"
                                        >
                                            <?php esc_html_e( 'Upload Image', 'hh-campaign-cms' ); ?>
                                        </button>

                                        <button 
                                            type="button" 
                                            class="button hh-campaign-remove-button" 
                                            data-slot-id="desktop_<?php echo esc_attr( $slot->id ); ?>" 
                                            <?php echo $current_asset_desktop ? '' : 'style="display:none;"'; ?>
                                        >
                                            <?php esc_html_e( 'Remove Image', 'hh-campaign-cms' ); ?>
                                        </button>
                                        
                                    </p>
                                </td>
                                <td style="width:70%">
                                    <table>
                                        <tr>
                                            <td><label for="destination_url_<?php echo esc_attr( $slot->id ); ?>_desktop"><?php esc_html_e( 'Destination URL:', 'hh-campaign-cms' ); ?></label></td>
                                            <td><input type="url" name="destination_urls[]" id="destination_url_<?php echo esc_attr( $slot->id ); ?>_desktop" class="regular-text" value="<?php echo esc_url( $current_asset_desktop ? $current_asset_desktop->destination_url : '' ); ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td><label for="alt_text_<?php echo esc_attr( $slot->id ); ?>_desktop"><?php esc_html_e( 'Alt Text:', 'hh-campaign-cms' ); ?></label></td>
                                            <td><input type="text" name="alt_texts[]" id="alt_text_<?php echo esc_attr( $slot->id ); ?>_desktop" class="regular-text" value="<?php echo esc_attr( $current_asset_desktop ? $current_asset_desktop->alt_text : '' ); ?>" /></td>
                                        </tr> 
                                    </table> 
                                </td>
                            </tr>
                        </table> 
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p><?php esc_html_e( 'No widget slots defined yet. Please go to the "Manage Slots" page to create them.', 'hh-campaign-cms' ); ?></p>
        <?php endif; ?>

        <div id="message" style="display:none;"></div>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( $is_editing ? __( 'Update Campaign', 'hh-campaign-cms' ) : __( 'Add Campaign', 'hh-campaign-cms' ) ); ?>"  data-original-text="<?php echo esc_attr( $is_editing ? __( 'Update Campaign', 'hh-campaign-cms' ) : __( 'Add Campaign', 'hh-campaign-cms' ) ); ?>">
        </p>

        <img id="image-to-crop">
    </form>
</div>