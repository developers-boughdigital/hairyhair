<?php
/**
 * Component: Hero Banner
 * Fields: title, subtitle, bg_image, overlay_color, button, button_style, text_align
 */

$title         = get_sub_field( 'title' );
$subtitle      = get_sub_field( 'subtitle' );
$bg_image      = get_sub_field( 'bg_image' );
$overlay_color = get_sub_field( 'overlay_color' ) ?: 'rgba(0,0,0,0.4)';
$button        = get_sub_field( 'button' );
$button_style  = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$text_align    = get_sub_field( 'text_align' ) ?: 'text-center';

$inline_style = '';
if ( $bg_image ) {
    $inline_style = 'background-image:url(' . esc_url( $bg_image['url'] ) . ');background-size:cover;background-position:center;';
}
?>
<section class="component-hero-banner" style="<?php echo esc_attr( $inline_style ); ?>">

    <?php if ( $bg_image ) : ?>
    <div class="hero-banner-overlay" style="position:absolute;inset:0;background:<?php echo esc_attr( $overlay_color ); ?>;"></div>
    <?php endif; ?>

    <div class="container">
        <div class="<?php echo esc_attr( $text_align ); ?>">

            <?php if ( $title ) : ?>
                <h1><?php echo esc_html( $title ); ?></h1>
            <?php endif; ?>

            <?php if ( $subtitle ) : ?>
                <div class="hero-subtitle"><?php echo wp_kses_post( $subtitle ); ?></div>
            <?php endif; ?>

            <?php if ( $button ) : ?>
                <a class="btn <?php echo esc_attr( $button_style ); ?>"
                   href="<?php echo esc_url( $button['url'] ); ?>"
                   target="<?php echo esc_attr( $button['target'] ); ?>">
                    <?php echo esc_html( $button['title'] ); ?>
                </a>
            <?php endif; ?>

        </div>
    </div>
</section>
