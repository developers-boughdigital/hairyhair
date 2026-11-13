<?php
/**
 * Component: Image + Text Block
 *
 * ACF Flexible Content layout name: image_text_block
 *
 * Fields:
 *   title          (text)
 *   description    (wysiwyg)
 *   image          (image)
 *   image_position (select)   left | right   default: left
 *   button         (link)
 *   button_style   (select)   secondary-btn | dark-btn | outline-btn
 *   bg_color       (color)    default: #D6E8F7
 *   padding        (text)     CSS padding, default: 80px 0
 */

$itb_title          = get_sub_field( 'title' );
$itb_description    = get_sub_field( 'description' );
$itb_image          = get_sub_field( 'image' );
$itb_image_position = get_sub_field( 'image_position' ) ?: 'left';
$itb_button         = get_sub_field( 'button' );
$itb_button_style   = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$itb_bg_color       = get_sub_field( 'bg_color' ) ?: '#D6E8F7';
$itb_padding        = get_sub_field( 'padding' ) ?: '80px 0';

$img_order  = $itb_image_position === 'left' ? 'order-lg-1 order-1' : 'order-lg-2 order-2';
$text_order = $itb_image_position === 'left' ? 'order-lg-2 order-2' : 'order-lg-1 order-1';
?>
<section class="component-image-text-block" style="    margin: 0px;
    position: relative;
    top: 90px;
padding:<?php echo esc_attr( $itb_padding ); ?>;">
    <div class="container">
        <div class="container-fluid px-0" style="background-color:<?php echo esc_attr( $itb_bg_color ); ?>;border-radius:10px;">
            <div class="row g-0 align-items-center">

                <?php if ( $itb_image ) : ?>
                <div class="col-lg-5 col-12 <?php echo esc_attr( $img_order ); ?> itb-img-col">
                    <div class="itb-img-wrap overflow-hidden" style="border-radius:10px;">
                        <img src="<?php echo esc_url( $itb_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $itb_image['alt'] ); ?>"
                             class="itb-img" />
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-lg-7 col-12 <?php echo esc_attr( $text_order ); ?> itb-text-col">
                    <div class="itb-content">

                        <?php if ( $itb_title ) : ?>
                            <h2 class="itb-title"><?php echo esc_html( $itb_title ); ?></h2>
                        <?php endif; ?>

                        <?php if ( $itb_description ) : ?>
                            <div class="itb-desc"><?php echo wp_kses_post( $itb_description ); ?></div>
                        <?php endif; ?>

                        <?php if ( $itb_button ) : ?>
                        <div class="itb-btn-wrap" style="    padding-top: 30px;">
                            <a class="btn <?php echo esc_attr( $itb_button_style ); ?>"
                               href="<?php echo esc_url( $itb_button['url'] ); ?>"
                               target="<?php echo esc_attr( $itb_button['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $itb_button['title'] ); ?>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>