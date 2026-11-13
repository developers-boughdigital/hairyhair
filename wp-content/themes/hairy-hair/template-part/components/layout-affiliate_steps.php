<?php
/**
 * Component: Affiliate Steps (Image + Feature List)
 *
 * ACF Flexible Content layout name: affiliate_steps
 *
 * Matches the "Join Hairy Hair Affiliate Program And Start Earning" reference design:
 *   - Section title + subtitle centred above
 *   - Left column: single image
 *   - Right column: repeater of steps, each with a circular icon, bold title, and description
 *   - Image position toggle (left / right)
 *
 * Fields:
 *   bg_color        (color_picker)   default: #ffffff
 *   padding         (text)           default: 80px 0
 *   title           (text)
 *   subtitle        (textarea)
 *   image           (image)
 *   image_position  (select)         left | right
 *   button          (link)           optional
 *   button_style    (select)         secondary-btn | dark-btn | outline-btn
 *   steps[]
 *     icon          (image)
 *     title         (text)
 *     description   (textarea)
 */

$bg_color       = get_sub_field( 'bg_color' )       ?: '#ffffff';
$padding        = get_sub_field( 'padding' )         ?: '80px 0';
$title          = get_sub_field( 'title' );
$subtitle       = get_sub_field( 'subtitle' );
$image          = get_sub_field( 'image' );
$image_position = get_sub_field( 'image_position' ) ?: 'left';
$button         = get_sub_field( 'button' );
$button_style   = get_sub_field( 'button_style' )   ?: 'secondary-btn';

// Column order
if ( $image_position === 'left' ) {
    $img_col  = 'order-lg-1 order-1';
    $text_col = 'order-lg-2 order-2';
} else {
    $img_col  = 'order-lg-2 order-1';
    $text_col = 'order-lg-1 order-2';
}
?>
<section class="affiliate-steps-section"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="affiliate-steps-header text-center mb-5">
            <?php if ( $title ) : ?>
                <h2 class="affiliate-steps-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="affiliate-steps-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row align-items-center g-4 g-lg-5">

            <?php if ( $image ) : ?>
            <!-- Image column -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $img_col ); ?> affiliate-steps-img-col">
                <div class="affiliate-steps-img-wrap">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         class="img-fluid w-100" />
                </div>
            </div>
            <?php endif; ?>

            <!-- Steps column -->
            <div class="col-lg-6 col-12 <?php echo esc_attr( $text_col ); ?> affiliate-steps-list-col">

                <?php if ( have_rows( 'steps' ) ) : ?>
                <div class="affiliate-steps-list">
                    <?php while ( have_rows( 'steps' ) ) : the_row(); ?>
                    <?php
                    $icon  = get_sub_field( 'icon' );
                    $stitle = get_sub_field( 'title' );
                    $desc  = get_sub_field( 'description' );
                    ?>
                    <div class="affiliate-step-item">
                        <?php if ( $icon ) : ?>
                        <div class="affiliate-step-icon">
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>
                        <div class="affiliate-step-body">
                            <?php if ( $stitle ) : ?>
                                <h4 class="affiliate-step-title"><?php echo esc_html( $stitle ); ?></h4>
                            <?php endif; ?>
                            <?php if ( $desc ) : ?>
                                <p class="affiliate-step-desc"><?php echo esc_html( $desc ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                <div class="affiliate-steps-cta mt-4">
                    <a class="btn <?php echo esc_attr( $button_style ); ?>"
                       href="<?php echo esc_url( $button['url'] ); ?>"
                       target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $button['title'] ); ?>
                    </a>
                </div>
                <?php endif; ?>

            </div><!-- /.affiliate-steps-list-col -->

        </div><!-- /.row -->

    </div><!-- /.container -->
</section>
