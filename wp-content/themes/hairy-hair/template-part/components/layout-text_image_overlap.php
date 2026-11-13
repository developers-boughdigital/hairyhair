<?php
/**
 * Component: Text + Image (Overlap Style)
 * Stylised two-column section where a smaller "overlap" image floats over the main image.
 * Fields: label, title, content, main_image, overlap_image, button, button_style,
 *         image_side, bg_color
 */

$label          = get_sub_field( 'label' );
$title          = get_sub_field( 'title' );
$content        = get_sub_field( 'content' );
$main_image     = get_sub_field( 'main_image' );
$overlap_image  = get_sub_field( 'overlap_image' );
$button         = get_sub_field( 'button' );
$button_style   = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$image_side     = get_sub_field( 'image_side' ) ?: 'right';
$bg_color       = get_sub_field( 'bg_color' ) ?: '#ffffff';

$text_order  = ( $image_side === 'right' ) ? 'order-1' : 'order-2';
$image_order = ( $image_side === 'right' ) ? 'order-2' : 'order-1';
?>
<section class="component-text-image-overlap" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:80px 0;">
    <div class="container">
        <div class="row align-items-center">

            <!-- Text Column -->
            <div class="col-lg-6 col-md-12 <?php echo esc_attr( $text_order ); ?>">

                <?php if ( $label ) : ?>
                    <span class="tio-label"><?php echo esc_html( $label ); ?></span>
                <?php endif; ?>

                <?php if ( $title ) : ?>
                    <h2 class="tio-title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>

                <?php if ( $content ) : ?>
                    <div class="tio-content"><?php echo wp_kses_post( $content ); ?></div>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                <div class="mt-4">
                    <a class="btn <?php echo esc_attr( $button_style ); ?>"
                       href="<?php echo esc_url( $button['url'] ); ?>"
                       target="<?php echo esc_attr( $button['target'] ); ?>">
                        <span><?php echo esc_html( $button['title'] ); ?></span>
                    </a>
                </div>
                <?php endif; ?>

            </div>

            <!-- Image Column with overlap float -->
            <?php if ( $main_image ) : ?>
            <div class="col-lg-6 col-md-12 <?php echo esc_attr( $image_order ); ?>">
                <div class="tio-image-wrap">
                    <img src="<?php echo esc_url( $main_image['url'] ); ?>"
                         alt="<?php echo esc_attr( $main_image['alt'] ); ?>"
                         class="img-fluid tio-main-image" />

                    <?php if ( $overlap_image ) : ?>
                    <div class="tio-overlap-image <?php echo $image_side === 'right' ? 'tio-overlap--left' : 'tio-overlap--right'; ?>">
                        <img src="<?php echo esc_url( $overlap_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $overlap_image['alt'] ); ?>"
                             class="img-fluid" />
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
