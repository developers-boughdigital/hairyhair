<?php
/**
 * Component: Affiliate Hero Banner
 *
 * Layout name: affiliate_hero
 *
 * Design: full-width light-blue section.
 *   • Left column  – person PNG cutout (bottom-anchored, overflow allowed)
 *   • Centre column – bold headline, paragraph, yellow CTA button
 *   • Right column  – person PNG cutout (bottom-anchored, overflow allowed)
 *
 * Fields:
 *   bg_color             (color_picker)  default: #d6eef9
 *   bg_gradient          (text)          optional CSS background override
 *   bg_image             (image)         optional full-width background image
 *   bg_image_overlay     (text)          rgba overlay on top of bg image
 *   title                (text)
 *   description          (wysiwyg)
 *   button               (link)
 *   button_style         (select)        yellow-btn | secondary-btn | dark-btn | outline-btn
 *   image_left           (image)
 *   image_right          (image)
 *   section_height       (number)        default: 420
 */

$bg_color        = get_sub_field( 'bg_color' )           ?: '#d6eef9';
$bg_gradient     = get_sub_field( 'bg_gradient' );
$bg_image        = get_sub_field( 'bg_image' );
$bg_overlay      = get_sub_field( 'bg_image_overlay' );
$title           = get_sub_field( 'title' );
$description     = get_sub_field( 'description' );
$button          = get_sub_field( 'button' );
$button_style    = get_sub_field( 'button_style' )        ?: 'yellow-btn';
$image_left      = get_sub_field( 'image_left' );
$image_right     = get_sub_field( 'image_right' );
$min_height      = (int) ( get_sub_field( 'section_height' ) ?: 420 );

// Build inline background style: bg image wins > gradient > solid colour
if ( $bg_image ) {
    $bg_css = 'url(' . esc_url( $bg_image['url'] ) . ') center center / cover no-repeat';
} elseif ( $bg_gradient ) {
    $bg_css = $bg_gradient;
} else {
    $bg_css = $bg_color;
}
?>
<div class="affiliate-hero-wrap container">
<section class="component-affiliate-hero<?php echo $bg_image ? ' has-bg-image' : ''; ?>"
         style="background:<?php echo esc_attr( $bg_css ); ?>;">

    <?php if ( $bg_image && $bg_overlay ) : ?>
    <div class="affiliate-hero-overlay" style="background:<?php echo esc_attr( $bg_overlay ); ?>;"></div>
    <?php endif; ?>

    <div class="row g-0 align-items-end affiliate-hero-row">

        <!-- Left person image -->
        <div class="col-lg-4 col-md-4 col-4 affiliate-hero-img-col affiliate-hero-img-col--left">
            <?php if ( $image_left ) : ?>
            <img src="<?php echo esc_url( $image_left['url'] ); ?>"
                 alt="<?php echo esc_attr( $image_left['alt'] ); ?>"
                 class="affiliate-hero-person-img"
                 loading="lazy" />
            <?php endif; ?>
        </div>

        <!-- Centre content -->
        <div class="col-lg-4 col-md-4 col-12 affiliate-hero-center-col">
            <div class="affiliate-hero-content text-center">
                <?php if ( $title ) : ?>
                    <h2 class="affiliate-hero-title"><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                    <div class="affiliate-hero-desc"><?php echo wp_kses_post( $description ); ?></div>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                <div class="affiliate-hero-cta">
                    <a class="btn secondary-btn "
                       href="<?php echo esc_url( $button['url'] ); ?>"
                       target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $button['title'] ); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right person image -->
        <div class="col-lg-4 col-md-4 col-4 affiliate-hero-img-col affiliate-hero-img-col--right">
            <?php if ( $image_right ) : ?>
            <img src="<?php echo esc_url( $image_right['url'] ); ?>"
                 alt="<?php echo esc_attr( $image_right['alt'] ); ?>"
                 class="affiliate-hero-person-img"
                 loading="lazy" />
            <?php endif; ?>
        </div>

    </div><!-- /.row -->

</section>
</div><!-- /.affiliate-hero-wrap -->
