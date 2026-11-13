<?php
/**
 * Component: Hero – Split (Text + Image)
 * Full landing-page style hero with headline, content, 2 buttons and a side image.
 * Fields: eyebrow, title, content, button_1, button_1_style, button_2, button_2_style,
 *         image, image_side, bg_color, bg_image, overlay_color, min_height
 */

$eyebrow       = get_sub_field( 'eyebrow' );
$title         = get_sub_field( 'title' );
$content       = get_sub_field( 'content' );
$button_1      = get_sub_field( 'button_1' );
$btn_1_style   = get_sub_field( 'button_1_style' ) ?: 'secondary-btn';
$button_2      = get_sub_field( 'button_2' );
$btn_2_style   = get_sub_field( 'button_2_style' ) ?: 'dark-btn';
$image         = get_sub_field( 'image' );
$image_side    = get_sub_field( 'image_side' ) ?: 'right';
$bg_color      = get_sub_field( 'bg_color' ) ?: '#ffffff';
$bg_image      = get_sub_field( 'bg_image' );
$overlay_color = get_sub_field( 'overlay_color' ) ?: '';
$min_height    = (int) ( get_sub_field( 'min_height' ) ?: 500 );

// Column order based on image side
$text_col_class  = ( $image_side === 'right' )
    ? 'col-lg-5 col-md-12 order-1'
    : 'col-lg-5 col-md-12 order-2';
$image_col_class = ( $image_side === 'right' )
    ? 'col-lg-7 col-md-12 order-2 px-0'
    : 'col-lg-7 col-md-12 order-1 px-0';

// Section inline style
$section_style = 'position:relative;overflow:hidden;min-height:' . $min_height . 'px;';
if ( $bg_image ) {
    $section_style .= 'background-image:url(' . esc_url( $bg_image['url'] ) . ');background-size:cover;background-position:center top;';
} else {
    $section_style .= 'background-color:' . esc_attr( $bg_color ) . ';';
}
?>
<section class="component-hero-split" style="<?php echo $section_style; ?>">

    <?php if ( $bg_image && $overlay_color ) : ?>
    <div class="hero-split-overlay" style="background:<?php echo esc_attr( $overlay_color ); ?>;z-index:0;pointer-events:none;"></div>
    <?php endif; ?>

    <div class="container-fluid px-0" style="position:relative;z-index:1;min-height:<?php echo $min_height; ?>px;">
        <div class="container">
            <div class="row align-items-center" style="min-height:<?php echo $min_height; ?>px;">

                <!-- Text Column -->
                <div class="<?php echo esc_attr( $text_col_class ); ?> py-5">

                    <?php if ( $eyebrow ) : ?>
                        <span class="hero-split-eyebrow d-inline-block mb-2">
                            <?php echo esc_html( $eyebrow ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( $title ) : ?>
                        <h1 class="hero-split-headline fw-bold mb-3">
                            <?php echo esc_html( $title ); ?>
                        </h1>
                    <?php endif; ?>

                    <?php if ( $content ) : ?>
                        <div class="hero-split-content mb-4">
                            <?php echo wp_kses_post( $content ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $button_1 || $button_2 ) : ?>
                    <div class="hero-split-buttons d-flex flex-wrap gap-3 mt-2">
                        <?php if ( $button_1 ) : ?>
                        <a class="btn <?php echo esc_attr( $btn_1_style ); ?>"
                           href="<?php echo esc_url( $button_1['url'] ); ?>"
                           target="<?php echo esc_attr( $button_1['target'] ); ?>">
                            <?php echo esc_html( $button_1['title'] ); ?>
                        </a>
                        <?php endif; ?>
                        <?php if ( $button_2 ) : ?>
                        <a class="btn <?php echo esc_attr( $btn_2_style ); ?>"
                           href="<?php echo esc_url( $button_2['url'] ); ?>"
                           target="<?php echo esc_attr( $button_2['target'] ); ?>">
                            <?php echo esc_html( $button_2['title'] ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Image Column — flush right, image overflows naturally -->
                <?php if ( $image ) : ?>
                <div class="<?php echo esc_attr( $image_col_class ); ?> d-flex align-items-end justify-content-end">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         class="hero-split-image d-block"
                         style="max-width:100%;height:<?php echo $min_height; ?>px;width:auto;object-fit:contain;object-position:right bottom;" />
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</section>
