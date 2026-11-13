<?php
/**
 * Component: HG – Treatment Journey (Before/After + Timeline)
 *
 * ACF Flexible Content layout name: hg_treatment_journey
 *
 * Fields:
 *   bg_color       (color)     section background, default: #122B51
 *   padding        (text)      CSS padding, default: '80px 0'
 *   title          (text)      e.g. "Simple Treatment With Dedicated Support"
 *   subtitle       (textarea)  description text below title
 *   before_image   (image)     before photo
 *   after_image    (image)     after photo
 *   months         (text)      e.g. "15"
 *   consultations  (text)      e.g. "4"
 *   steps          (repeater)
 *     └ step_label       (text)     e.g. "3 Months"
 *     └ step_description (textarea)
 */

$bg_color      = get_sub_field( 'bg_color' ) ?: '#122B51';
$padding       = get_sub_field( 'padding' ) ?: '80px 0';
$title         = get_sub_field( 'title' );
$subtitle      = get_sub_field( 'subtitle' );
$before_image  = get_sub_field( 'before_image' );
$after_image   = get_sub_field( 'after_image' );
$months        = get_sub_field( 'months' );
$consultations = get_sub_field( 'consultations' );
$steps         = get_sub_field( 'steps' );

$before_url = '';
$after_url  = '';
if ( is_array( $before_image ) ) {
    $before_url = $before_image['url'] ?? '';
} elseif ( is_numeric( $before_image ) ) {
    $before_url = wp_get_attachment_url( $before_image );
}
if ( is_array( $after_image ) ) {
    $after_url = $after_image['url'] ?? '';
} elseif ( is_numeric( $after_image ) ) {
    $after_url = wp_get_attachment_url( $after_image );
}
?>
<section class="tj-section"

         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="global-title text-center white-title tj-header">
            <?php if ( $title ) : ?>
                <h2><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="white"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="tj-row">

            <!-- Left: Before / After slider -->
            <?php if ( $before_url && $after_url ) : ?>
            <div class="col-12 tj-col-left">
                <div class="tj-ba-wrap" data-tj-slider>
                    <figure>
                        <img src="<?php echo esc_url( $after_url ); ?>"
                             class="tj-ba-img tj-ba-img--after"
                             alt="<?php echo is_array( $after_image ) ? esc_attr( $after_image['alt'] ) : 'After'; ?>">
                        <img src="<?php echo esc_url( $before_url ); ?>"
                             class="tj-ba-img tj-ba-img--before"
                             alt="<?php echo is_array( $before_image ) ? esc_attr( $before_image['alt'] ) : 'Before'; ?>">
                        <div class="tj-ba-divisor"></div>
                        <div class="tj-ba-handle">
                          <svg width="34" height="10" viewBox="0 0 34 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.27591 9.64389C8.16496 10.1539 8.88574 9.73609 8.88574 8.71151L8.88574 1.15058C8.88574 0.124983 8.16496 -0.292251 7.27591 0.21724L0.667277 4.00724C-0.222075 4.5174 -0.222075 5.34392 0.667277 5.85395L7.27591 9.64389Z" fill="#F5F5F5"></path><path d="M26.6098 0.217434C25.7208 -0.292538 25 0.125237 25 1.14981V8.71074C25 9.73635 25.7208 10.1536 26.6098 9.64409L33.2185 5.85408C34.1078 5.34393 34.1078 4.51741 33.2185 4.00738L26.6098 0.217434Z" fill="#F5F5F5"></path></svg>
                        </div>
                        <span class="tj-ba-label tj-ba-label--before">BEFORE</span>
                        <span class="tj-ba-label tj-ba-label--after">AFTER</span>
                    </figure>
                    <input type="range" min="0" max="100" value="50"
                           class="tj-ba-range"
                           aria-label="Drag to compare before and after">
                </div>

                <?php if ( $months || $consultations ) : ?>
                <div class="tj-ba-meta">
                    <?php if ( $months ) : ?>
                        <span>Months: <strong><?php echo esc_html( $months ); ?></strong></span>
                    <?php endif; ?>
                    <?php if ( $consultations ) : ?>
                        <span>Consultation: <strong><?php echo esc_html( $consultations ); ?></strong></span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Right: Timeline steps -->
            <?php if ( $steps ) : ?>
            <div class="col-12 tj-col-right">
                <div class="tj-timeline">
                    <?php $total = count( $steps );
                    foreach ( $steps as $i => $step ) :
                        $is_last = ( $i === $total - 1 );
                    ?>
                    <div class="tj-step">
                        <div class="tj-step-indicator">
                            <span class="tj-step-dot"></span>
                            <?php if ( ! $is_last ) : ?>
                                <span class="tj-step-line"></span>
                            <?php endif; ?>
                        </div>
                        <div class="tj-step-body">
                            <?php if ( $step['step_label'] ) : ?>
                                <h5><?php echo esc_html( $step['step_label'] ); ?></h5>
                            <?php endif; ?>
                            <?php if ( $step['step_description'] ) : ?>
                                <p><?php echo esc_html( $step['step_description'] ); ?></p>
                            <?php endif; ?>
                            <?php if ( ! $is_last ) : ?>
                                <hr class="tj-step-divider">
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- .tj-row -->

    </div>
</section>
