<?php
/**
 * Component: Hair Pill – Journey to Fuller Hair
 *
 * ACF Flexible Content layout name: hp_journey
 *
 * Fields:
 *   bg_color        (color_picker)  section background, default: #EAF2F9
 *   bg_image        (image)         optional full-width background image (overrides bg_color)
 *   bg_image_overlay (text)          optional rgba overlay on top of bg image, e.g. rgba(0,0,0,0.3)
 *   padding         (text)          CSS padding, default: '80px 0'
 *   top_image    (image)         optional pill/product image above the title
 *   title        (text)          main section heading
 *   button       (link)          CTA button
 *   button_style (select)        yellow-btn | secondary-btn | dark-btn | outline-btn
 *   steps        (repeater)
 *     └ icon        (image)      circular step icon
 *     └ title       (text)       step heading
 *     └ description (textarea)   step description
 */

$bg_color        = get_sub_field( 'bg_color' )        ?: '#EAF2F9';
$bg_image        = get_sub_field( 'bg_image' );
$bg_image_overlay = get_sub_field( 'bg_image_overlay' );
$padding         = get_sub_field( 'padding' )         ?: '80px 0';

// bg image wins over solid colour
if ( $bg_image ) {
    $bg_css = 'url(' . esc_url( $bg_image['url'] ) . ') center center / contain no-repeat';
} else {
    $bg_css = $bg_color;
}
$top_image   = get_sub_field( 'top_image' );
$title       = get_sub_field( 'title' );
$button      = get_sub_field( 'button' );
$button_style = get_sub_field( 'button_style' ) ?: 'yellow-btn';

$steps_raw  = get_sub_field( 'steps' );
$step_count = is_array( $steps_raw ) ? count( $steps_raw ) : 0;
?>
<section class="hp-journey-section<?php echo $bg_image ? ' has-bg-image' : ''; ?>"
         style="background:<?php echo esc_attr( $bg_css ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">

    <?php if ( $bg_image && $bg_image_overlay ) : ?>
    <div class="hp-journey__overlay" style="background:<?php echo esc_attr( $bg_image_overlay ); ?>;"></div>
    <?php endif; ?>

    <div class="container">

        <?php if ( $top_image ) : ?>
        <div class="hp-journey__top-image text-center">
            <img src="<?php echo esc_url( $top_image['url'] ); ?>"
                 alt="<?php echo esc_attr( $top_image['alt'] ); ?>"
                 class="img-fluid hp-journey__pill-img" />
        </div>
        <?php endif; ?>

        <?php if ( $title ) : ?>
        <div class="global-title text-center hp-journey__heading">
            <h2><?php echo esc_html( $title ); ?></h2>
        </div>
        <?php endif; ?>

        <?php if ( have_rows( 'steps' ) ) : ?>
        <div class="hp-journey__steps" data-steps="<?php echo esc_attr( $step_count ); ?>">

            <!-- Dashed connecting line (CSS handles sizing) -->
            <div class="hp-journey__connector" aria-hidden="true"></div>

            <?php $i = 1; while ( have_rows( 'steps' ) ) : the_row(); ?>
            <?php
            $icon  = get_sub_field( 'icon' );
            $stitle = get_sub_field( 'title' );
            $desc  = get_sub_field( 'description' );
            ?>
            <div class="hp-journey__step">

                <div class="hp-journey__icon-wrap">
                    <?php if ( $icon ) : ?>
                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                         alt="<?php echo esc_attr( $icon['alt'] ); ?>"
                         class="hp-journey__icon-img" />
                    <?php else : ?>
                    <span class="hp-journey__icon-fallback"><?php echo esc_html( $i ); ?></span>
                    <?php endif; ?>
                </div>

                <?php if ( $stitle ) : ?>
                <h4 class="hp-journey__step-title"><?php echo esc_html( $stitle ); ?></h4>
                <?php endif; ?>

                <?php if ( $desc ) : ?>
                <p class="hp-journey__step-desc"><?php echo esc_html( $desc ); ?></p>
                <?php endif; ?>

            </div>
            <?php $i++; endwhile; ?>

        </div>
        <?php endif; ?>

        <?php if ( $button ) : ?>
        <div class="hp-journey__cta text-center">
            <a class="btn secondary-btn"
               href="<?php echo esc_url( $button['url'] ); ?>"
               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                <span><?php echo esc_html( $button['title'] ); ?></span>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
