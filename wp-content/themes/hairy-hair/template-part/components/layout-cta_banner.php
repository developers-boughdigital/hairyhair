<?php
/**
 * Component: CTA Banner
 * Fields: title, content, button_1, button_1_style, button_2, button_2_style,
 *         bg_color, text_color, bg_image, text_align
 */

$title          = get_sub_field( 'title' );
$content        = get_sub_field( 'content' );
$button_1       = get_sub_field( 'button_1' );
$button_1_style = get_sub_field( 'button_1_style' ) ?: 'secondary-btn';
$button_2       = get_sub_field( 'button_2' );
$button_2_style = get_sub_field( 'button_2_style' ) ?: 'dark-btn';
$bg_color       = get_sub_field( 'bg_color' ) ?: '#3d3d3d';
$text_color     = get_sub_field( 'text_color' ) ?: '#ffffff';
$bg_image       = get_sub_field( 'bg_image' );
$text_align     = get_sub_field( 'text_align' ) ?: 'text-center';

$justify_map = [ 'text-start' => 'start', 'text-center' => 'center', 'text-end' => 'end' ];
$justify     = $justify_map[ $text_align ] ?? 'center';

$section_style = 'background-color:' . esc_attr( $bg_color ) . ';color:' . esc_attr( $text_color ) . ';padding:80px 0;position:relative;';
if ( $bg_image ) {
    $section_style .= 'background-image:url(' . esc_url( $bg_image['url'] ) . ');background-size:cover;background-position:center;';
}
?>
<section class="component-cta-banner" style="<?php echo $section_style; ?>">

    <?php if ( $bg_image ) : ?>
    <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);"></div>
    <?php endif; ?>

    <div class="container" style="position:relative;z-index:1;">
        <div class="<?php echo esc_attr( $text_align ); ?>">

            <?php if ( $title ) : ?>
                <h2 style="color:<?php echo esc_attr( $text_color ); ?>;">
                    <?php echo esc_html( $title ); ?>
                </h2>
            <?php endif; ?>

            <?php if ( $content ) : ?>
                <div class="cta-content" style="color:<?php echo esc_attr( $text_color ); ?>;">
                    <?php echo wp_kses_post( $content ); ?>
                </div>
            <?php endif; ?>

            <?php if ( $button_1 || $button_2 ) : ?>
            <div class="d-flex flex-wrap gap-3 mt-4 justify-content-<?php echo esc_attr( $justify ); ?>">

                <?php if ( $button_1 ) : ?>
                <a class="btn <?php echo esc_attr( $button_1_style ); ?>"
                   href="<?php echo esc_url( $button_1['url'] ); ?>"
                   target="<?php echo esc_attr( $button_1['target'] ); ?>">
                    <?php echo esc_html( $button_1['title'] ); ?>
                </a>
                <?php endif; ?>

                <?php if ( $button_2 ) : ?>
                <a class="btn <?php echo esc_attr( $button_2_style ); ?>"
                   href="<?php echo esc_url( $button_2['url'] ); ?>"
                   target="<?php echo esc_attr( $button_2['target'] ); ?>">
                    <?php echo esc_html( $button_2['title'] ); ?>
                </a>
                <?php endif; ?>

            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
