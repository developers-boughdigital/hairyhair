<?php
// extend/campaign/admin/views/slot-management.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_slot = null;
$edit_slot_id = isset( $_GET['edit_id'] ) ? intval( $_GET['edit_id'] ) : 0;
if ( $edit_slot_id > 0 ) {
    $current_slot = HH_Campaign_Manager::get_instance()->get_slot_details( $edit_slot_id ); 
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Manage Widget Slots', 'hh-campaign-cms' ); ?></h1>
    <hr class="wp-header-end">

    <div id="message" style="display:none;"></div>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2" style="display: grid;grid-template-columns: 5fr 4fr;width: 100%;">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo $current_slot ? esc_html__( 'Edit Slot', 'hh-campaign-cms' ) : esc_html__( 'Add New Slot', 'hh-campaign-cms' ); ?></span></h2>
                        <div class="inside">
                            <form id="hh-slot-form" method="post" action="">
                                <input type="hidden" name="action" value="hh_campaign_cms_save_slot">
                                <input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'hh_campaign_cms_nonce' ) ); ?>">
                                <input type="hidden" name="slot_id" value="<?php echo esc_attr( $current_slot ? $current_slot->id : 0 ); ?>">
                                

                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><label for="slot_name"><?php esc_html_e( 'Slot Name', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <?php if(!$current_slot->id) : ?>
                                                <input type="text" name="slot_name" id="slot_name" class="regular-text" value="<?php echo esc_attr( $current_slot ? $current_slot->slot_name : '' ); ?>" required />
                                                <p class="description"><?php esc_html_e( 'A unique, descriptive name for this widget slot. Used in shortcodes. Cannot rename.', 'hh-campaign-cms' ); ?></p>
                                                <?php else: ?>
                                                    <b><?php echo esc_attr( $current_slot ? $current_slot->slot_name : '' ); ?></b>
                                                    <input type="hidden" name="slot_name" value="<?php echo esc_attr( $current_slot ? $current_slot->slot_name : '' ); ?>" required />
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="slot_location"><?php esc_html_e( 'Location', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <input type="text" name="slot_location" id="slot_location" class="regular-text" value="<?php echo esc_attr( $current_slot ? $current_slot->slot_location : '' ); ?>" required />
                                                <p class="description"><?php esc_html_e( 'Location where you want to place this.', 'hh-campaign-cms' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="dimensions"><?php esc_html_e( 'Dimensions', 'hh-campaign-cms' ); ?></label></th>
                                            <td><input type="text" name="dimensions" id="dimensions" class="regular-text" value="<?php echo esc_attr( $current_slot ? $current_slot->dimensions : '' ); ?>" placeholder="e.g., 1920x400" required />
                                            <p class="description"><?php esc_html_e( 'Enter the ideal image dimensions in "widthxheight" format (e.g., "1920x400").', 'hh-campaign-cms' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="fallback_behavior"><?php esc_html_e( 'Fallback Behavior', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <select name="fallback_behavior" id="fallback_behavior">
                                                    <option value="default" <?php selected( $current_slot && $current_slot->fallback_behavior, 'default' ); ?>><?php esc_html_e( 'Show Default Content', 'hh-campaign-cms' ); ?></option>
                                                    <option value="omit" <?php if($current_slot && $current_slot->fallback_behavior == 'omit' ) echo 'selected'; ?>><?php esc_html_e( 'Omit Widget', 'hh-campaign-cms' ); ?></option>
                                                </select>
                                                <p class="description"><?php esc_html_e( 'What should happen if no active campaign is running for this slot?', 'hh-campaign-cms' ); ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="default_content"><?php esc_html_e( 'Default Content', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <div class="campaign-slot-box" style="border:0;padding:0">
                                                    <input type="hidden" class="hh-campaign-image-url" name="image_urls" value="<?php echo esc_url( $current_slot ? $current_slot->default_image : '' ); ?>" />
                                                    <input type="hidden" class="hh-campaign-webp-url" name="webp_urls" value="<?php echo esc_url( $current_slot ? $current_slot->default_image_webp : '' ); ?>" />
                                                    
                                                    <div class="hh-campaign-image-preview" id="slot_<?php echo esc_attr( $slot->id ); ?>_desktop_preview">
                                                        <?php if ( $current_slot ) : ?>
                                                            <img src="<?php echo esc_url( $current_slot->default_image ); ?>" style="max-width:200px; height:auto;" />
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
                                                            <?php echo $current_slot ? '' : 'style="display:none;"'; ?>
                                                        >
                                                            <?php esc_html_e( 'Remove Image', 'hh-campaign-cms' ); ?>
                                                        </button>
                                                    </p> 
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="default_link"><?php esc_html_e( 'Default Link', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <input type="text" name="default_link" id="default_link" class="regular-text" value="<?php echo esc_attr( $current_slot ? $current_slot->default_link : '' ); ?>" placeholder="https://google.com"   />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="default_alt"><?php esc_html_e( 'Default Alt', 'hh-campaign-cms' ); ?></label></th>
                                            <td>
                                                <input type="text" name="default_alt" id="default_alt" class="regular-text" value="<?php echo esc_attr( $current_slot ? $current_slot->default_alt : '' ); ?>" placeholder="Google"   />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p class="submit">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr( $current_slot ? __( 'Update Slot', 'hh-campaign-cms' ) : __( 'Add New Slot', 'hh-campaign-cms' ) ); ?>">
                                    <?php if ( $current_slot ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=hh-campaign-cms-slots' ) ); ?>" class="button button-secondary"><?php esc_html_e( 'Cancel Edit', 'hh-campaign-cms' ); ?></a>
                                    <?php endif; ?>
                                </p> 
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container" style="width: 95%; margin-left: 5%;">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php esc_html_e( 'Existing Widget Slots', 'hh-campaign-cms' ); ?></span></h2>
                        <div class="inside">
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Slot Name', 'hh-campaign-cms' ); ?></th>
                                        <th><?php esc_html_e( 'Dimensions', 'hh-campaign-cms' ); ?></th>
                                        <th><?php esc_html_e( 'Fallback', 'hh-campaign-cms' ); ?></th> 
                                        <th style="width: 300px;"><?php esc_html_e( 'Short Code', 'hh-campaign-cms' ); ?></th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ( ! empty( $slots ) ) : ?>
                                        <?php foreach ( $slots as $slot ) : ?>

                                            <tr id="slot-<?php echo esc_attr( $slot->id ); ?>">
                                                <td>
                                                    <strong>
                                                        <?php echo esc_html( $slot->slot_name ); ?> <br>
                                                        <small><?php echo esc_html( $slot->slot_location ); ?></small>
                                                    </strong>
                                                    <div class="row-actions">
                                                        <?php if($slot->id != 15) : ?>
                                                            <span class="edit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=hh-campaign-cms-slots&edit_id=' . $slot->id ) ); ?>"><?php esc_html_e( 'Edit', 'hh-campaign-cms' ); ?></a> | </span>
                                                            <span class="delete"><a href="#" class="hh-slot-delete-button" data-id="<?php echo esc_attr( $slot->id ); ?>"><?php esc_html_e( 'Delete', 'hh-campaign-cms' ); ?></a></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                
                                                <td><?php echo esc_html( $slot->dimensions ); ?></td>
                                                <td><?php echo esc_html( ucfirst( $slot->fallback_behavior ) ); ?></td>
                                                <td>[hh_campaign_widget slot="<?php echo esc_html( $slot->slot_name ); ?>"] </td>
                                            </tr>
                                            
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="4"><?php esc_html_e( 'No widget slots created yet.', 'hh-campaign-cms' ); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>