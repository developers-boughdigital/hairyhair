<?php
/**
 * Component: CTA Inline Strip
 * Full-width horizontal strip: icon + text on left, buttons on right.
 * Fields: title, subtitle, button_1, button_1_style, button_2, button_2_style,
 *         icon, bg_color, text_color
 */

$title          = get_sub_field( 'title' );
$subtitle       = get_sub_field( 'subtitle' );
$button_1       = get_sub_field( 'button_1' );
$btn_1_style    = get_sub_field( 'button_1_style' ) ?: 'secondary-btn';
$button_2       = get_sub_field( 'button_2' );
$btn_2_style    = get_sub_field( 'button_2_style' ) ?: 'outline-btn';
$icon           = get_sub_field( 'icon' );
$bg_color       = get_sub_field( 'bg_color' ) ?: '#3d3d3d';
$text_color     = get_sub_field( 'text_color' ) ?: '#ffffff';
?>
<section class="component-cta-inline-strip"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;color:<?php echo esc_attr( $text_color ); ?>;padding:30px 0;">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <!-- Left: icon + text -->
            <div class="cta-strip-left d-flex align-items-center gap-3">
                <?php if ( $icon ) : ?>
                <img src="<?php echo esc_url( $icon['url'] ); ?>"
                     alt="<?php echo esc_attr( $icon['alt'] ); ?>"
                     style="width:48px;height:48px;object-fit:contain;flex-shrink:0;" />
                <?php endif; ?>
                <div>
                    <?php if ( $title ) : ?>
                        <h3 style="color:<?php echo esc_attr( $text_color ); ?>;margin:0 0 4px;">
                            <?php echo esc_html( $title ); ?>
                        </h3>
                    <?php endif; ?>
                    <?php if ( $subtitle ) : ?>
                        <p style="color:<?php echo esc_attr( $text_color ); ?>;margin:0;opacity:0.85;">
                            <?php echo esc_html( $subtitle ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: buttons -->
            <?php if ( $button_1 || $button_2 ) : ?>
            <div class="cta-strip-right d-flex flex-wrap gap-3">
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
    </div>
</section>
