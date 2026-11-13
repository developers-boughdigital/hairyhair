<?php
/**
 * Component: Image + Text (Full Bleed)
 *
 * ACF Flexible Content layout name: image_text_full
 *
 * Fields:
 *   title          (text)
 *   description    (wysiwyg)
 *   image          (image)
 *   image_position (select) left | right   default: left
 *   button         (link)
 *   button_style   (select) secondary-btn | dark-btn | outline-btn
 *   bg_color       (color)  default: #D6E8F7
 *   section_height (text)   default: 500px — controls the image/section min-height
 *   extra_class    (text)   optional extra CSS class on the <section>
 */

$title          = get_sub_field( 'title' );
$description    = get_sub_field( 'description' );
$image          = get_sub_field( 'image' );
$image_position = get_sub_field( 'image_position' ) ?: 'left';
$button         = get_sub_field( 'button' );
$button_style   = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$bg_color       = get_sub_field( 'bg_color' ) ?: '#D6E8F7';
$section_height = get_sub_field( 'section_height' ) ?: '500px';
$extra_class    = get_sub_field( 'extra_class' ) ?: '';

$img_order  = $image_position === 'left' ? 'order-lg-1 order-1' : 'order-lg-2 order-1';
$text_order = $image_position === 'left' ? 'order-lg-2 order-2' : 'order-lg-1 order-2';
?>
<section class="component-image-text-full <?php echo esc_attr( $extra_class ); ?>"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;">

    <div class="container-fluid px-0">
        <div class="row g-0 align-items-center">

            <?php if ( $image ) : ?>
            <!-- ── Image column ── -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $img_order ); ?> itf-img-col"
                 style="min-height:<?php echo esc_attr( $section_height ); ?>;">
                <div class="itf-img-wrap">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         class="itf-img" />
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Text column ── -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $text_order ); ?> itf-text-col">
                <div class="itf-content">

                    <?php if ( $title ) : ?>
                        <h2 class="itf-title">
                            <?php echo esc_html( $title ); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if ( $description ) : ?>
                        <div class="itf-desc">
                            <?php echo wp_kses_post( $description ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $button ) : ?>
                        <div class="itf-btn-wrap">
                            <a class="btn <?php echo esc_attr( $button_style ); ?>"
                               href="<?php echo esc_url( $button['url'] ); ?>"
                               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $button['title'] ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>
