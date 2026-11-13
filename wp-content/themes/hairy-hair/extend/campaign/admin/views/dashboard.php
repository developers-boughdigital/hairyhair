<?php
// extend/campaign/admin/views/dashboard.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}




?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Campaign CMS Dashboard', 'hh-campaign-cms' ); ?></h1>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=hh-campaign-cms-edit' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New Campaign', 'hh-campaign-cms' ); ?></a>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <?php
                        // This would typically be a custom WP_List_Table implementation for robust display
                        // For simplicity, we'll use a basic table here.
                        ?>
                        <table class="wp-list-table widefat fixed striped pages">
                            <thead>
                                <tr>
                                    <th scope="col" class="manage-column column-title"><?php esc_html_e( 'Campaign Name', 'hh-campaign-cms' ); ?></th>
                                    <!-- <th scope="col" class="manage-column"><?php esc_html_e( 'Internal Reference', 'hh-campaign-cms' ); ?></th> -->
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'Status', 'hh-campaign-cms' ); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'Start Date', 'hh-campaign-cms' ); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'End Date', 'hh-campaign-cms' ); ?></th>
                                    <th scope="col" class="manage-column"><?php esc_html_e( 'Actions', 'hh-campaign-cms' ); ?></th>
                                </tr>
                            </thead>
                            <tbody id="the-list">
                                <?php if ( ! empty( $campaigns ) ) : ?>
                                    <?php foreach ( $campaigns as $campaign ) : ?>
                                        <tr id="campaign-<?php echo esc_attr( $campaign->id ); ?>">
                                            <td>
                                                <strong><?php echo esc_html( $campaign->campaign_name ); ?></strong>
                                                <div class="row-actions">
                                                    <span class="edit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=hh-campaign-cms-edit&id=' . $campaign->id ) ); ?>"><?php esc_html_e( 'Edit', 'hh-campaign-cms' ); ?></a> | </span>
                                                    <?php if ( $campaign->status === 'live' || $campaign->status === 'upcoming' ) : ?>
                                                        <span class="pause"><a href="#" class="hh-campaign-cms-action-button" data-action="pause" data-id="<?php echo esc_attr( $campaign->id ); ?>"><?php esc_html_e( 'Pause', 'hh-campaign-cms' ); ?></a> | </span>
                                                    <?php elseif ( $campaign->status === 'paused' || $campaign->status === 'expired' ) : ?>
                                                        <span class="activate"><a href="#" class="hh-campaign-cms-action-button" data-action="activate" data-id="<?php echo esc_attr( $campaign->id ); ?>"><?php esc_html_e( 'Activate', 'hh-campaign-cms' ); ?></a> | </span>
                                                    <?php endif; ?>
                                                    <span class="delete"><a href="#" class="hh-campaign-cms-action-button" data-action="delete" data-id="<?php echo esc_attr( $campaign->id ); ?>"><?php esc_html_e( 'Delete', 'hh-campaign-cms' ); ?></a></span>
                                                </div>
                                            </td>
                                            <!-- <td><?php echo esc_html( $campaign->internal_reference ); ?></td> -->
                                            <td><span class="campaign-status-<?php echo esc_attr( $campaign->status ); ?>"><?php echo esc_html( ucfirst( $campaign->status ) ); ?></span></td>
                                            <td><?php echo esc_html( $campaign->start_date ); ?></td>
                                            <td><?php echo esc_html( $campaign->end_date ); ?></td>
                                            <td>
                                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=hh-campaign-cms-edit&id=' . $campaign->id ) ); ?>" class="button button-small"><?php esc_html_e( 'Edit', 'hh-campaign-cms' ); ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6"><?php esc_html_e( 'No campaigns found.', 'hh-campaign-cms' ); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>