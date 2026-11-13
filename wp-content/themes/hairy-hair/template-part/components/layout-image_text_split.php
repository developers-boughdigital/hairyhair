<?php
/**
 * Component: Image + Text Split
 *
 * ACF Flexible Content layout name: image_text_split
 *
 * Fields:
 *   image          (image)
 *   title          (text)
 *   content        (wysiwyg)
 *   button         (link)
 *   button_style   (text)   default: 'primary-btn'
 *   image_position (select) left | right   default: left
 *   bg_color       (color)  default: #ffffff
 *   padding        (text)   default: 80px 0
 */

$image          = get_sub_field( 'image' );
$title          = get_sub_field( 'title' );
$content        = get_sub_field( 'content' );
$button         = get_sub_field( 'button' );
$button_style   = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$image_position = get_sub_field( 'image_position' ) ?: 'left';
$bg_color       = get_sub_field( 'bg_color' ) ?: '#ffffff';
$padding        = get_sub_field( 'margin_top' ) ?: '100px';

// Column order based on image position
if ( $image_position === 'left' ) {
    $img_order  = 'order-lg-1 order-1';
    $text_order = 'order-lg-2 order-2';
} else {
    $img_order  = 'order-lg-2 order-1';
    $text_order = 'order-lg-1 order-2';
}
?>
<section class="component-image-text-split"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;margin-top:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">
        <div class="row align-items-center g-0 g-lg-5">

            <?php if ( $image ) : ?>
            <!-- Image column -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $img_order ); ?> its-img-col">
                <div class="its-img-wrap">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         class="img-fluid w-100"
                         style="display:block;height:100%;object-fit:cover;" />
                </div>
            </div>
            <?php endif; ?>

            <!-- Text column -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $text_order ); ?> its-text-col py-5 py-lg-0 px-4 px-lg-5">

                <?php if ( $title ) : ?>
                    <h2 class="its-title fw-bold mb-4">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                <?php endif; ?>

                <?php if ( $content ) : ?>
                    <div class="its-content mb-4">
                        <?php echo wp_kses_post( $content ); ?>
                    </div>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                    <div class="its-btn-wrap mt-4">
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
</section>
