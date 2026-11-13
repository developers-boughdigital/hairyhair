<?php
/**
 * Component: Text + Image
 * Fields: title, content, image, image_position, button, button_style, bg_color
 */

$title          = get_sub_field( 'title' );
$content        = get_sub_field( 'content' );
$image          = get_sub_field( 'image' );
$image_position = get_sub_field( 'image_position' ) ?: 'right';
$button         = get_sub_field( 'button' );
$button_style   = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$bg_color       = get_sub_field( 'bg_color' ) ?: '#ffffff';

$text_col  = ( $image_position === 'right' ) ? 'order-1' : 'order-2';
$image_col = ( $image_position === 'right' ) ? 'order-2' : 'order-1';
?>
<section class="component-text-image" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:60px 0;">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-6 col-md-12 <?php echo esc_attr( $text_col ); ?>">
                <?php if ( $title ) : ?>
                    <h2><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>

                <?php if ( $content ) : ?>
                    <div class="text-image-content"><?php echo wp_kses_post( $content ); ?></div>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                    <a class="btn <?php echo esc_attr( $button_style ); ?>"
                       href="<?php echo esc_url( $button['url'] ); ?>"
                       target="<?php echo esc_attr( $button['target'] ); ?>">
                        <span><?php echo esc_html( $button['title'] ); ?></span>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( $image ) : ?>
            <div class="col-lg-6 col-md-12 <?php echo esc_attr( $image_col ); ?>">
                <img src="<?php echo esc_url( $image['url'] ); ?>"
                     alt="<?php echo esc_attr( $image['alt'] ); ?>"
                     class="img-fluid" />
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
