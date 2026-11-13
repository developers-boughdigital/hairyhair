<?php
/**
 * Component: Before/After Transformation Slider
 *
 * ACF Flexible Content layout name: before_after_slider
 *
 * Fields:
 *   bg_color           (color)     section background, default: #3B82C4
 *   padding            (text)      CSS padding, default: '80px 0'
 *   title              (text)      e.g. "See the Hair Transformation"
 *   subtitle           (textarea)  description text
 *   show_trustpilot    (true_false) show Trustpilot bar
 *   trustpilot_label   (text)      e.g. "Excellent"
 *   trustpilot_rating  (text)      e.g. "4.6"
 *   trustpilot_count   (text)      e.g. "300"
 *   slides             (repeater)
 *     └ before_image   (image)
 *     └ after_image    (image)
 *     └ caption        (text)      e.g. "Homepage test 1"
 *     └ months         (text)      e.g. "11"
 *     └ consultations  (text)      e.g. "4"
 *   cta_button         (link)      CTA button
 */

$bg_color          = get_sub_field( 'bg_color' ) ?: '#3B82C4';
$padding           = get_sub_field( 'padding' ) ?: '80px 0';
$title             = get_sub_field( 'title' );
$subtitle          = get_sub_field( 'subtitle' );
$show_trustpilot   = get_sub_field( 'show_trustpilot' );
$trustpilot_label  = get_sub_field( 'trustpilot_label' ) ?: 'Excellent';
$trustpilot_rating = get_sub_field( 'trustpilot_rating' ) ?: '4.6';
$trustpilot_count  = get_sub_field( 'trustpilot_count' ) ?: '300';
$slides            = get_sub_field( 'slides' );
$cta_button        = get_sub_field( 'cta_button' );
$uid               = 'ba-slider-' . uniqid();
?>
<section class="component-ba-slider" id="<?php echo esc_attr( $uid ); ?>" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="ba-header text-center">
            <?php if ( $title ) : ?>
                <h2 class="ba-title"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="ba-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( $show_trustpilot ) : ?>
        <div class="ba-trustpilot d-flex justify-content-center align-items-center flex-wrap gap-2">
            <span class="ba-tp-label"><?php echo esc_html( $trustpilot_label ); ?></span>
            <span class="ba-tp-stars" aria-label="<?php echo esc_attr( $trustpilot_rating ); ?> out of 5">
                <?php for ( $s = 0; $s < 5; $s++ ) : ?>
                <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="10,1 12.6,7.2 19.5,7.6 14.2,12 15.8,18.8 10,15 4.2,18.8 5.8,12 0.5,7.6 7.4,7.2"
                             fill="#00B67A" stroke="none"/>
                </svg>
                <?php endfor; ?>
            </span>
            <span class="ba-tp-rating"><?php echo esc_html( $trustpilot_rating ); ?> rating from <?php echo esc_html( $trustpilot_count ); ?> reviews</span>
            <span class="ba-tp-logo">
                <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                    <polygon points="8,0.5 10,6 16,6 11,9.5 13,15.5 8,11.5 3,15.5 5,9.5 0,6 6,6" fill="#00B67A"/>
                </svg>
                Trustpilot
            </span>
        </div>
        <?php endif; ?>

        <?php if ( $slides ) : ?>
        <div class="ba-carousel">
            <div class="ba-track">
                <?php foreach ( $slides as $i => $slide ) :
                    $before = $slide['before_image'] ?? null;
                    $after  = $slide['after_image'] ?? null;
                    if ( ! $before || ! $after ) continue;

                    // Handle ACF image return formats: Array, URL string, or ID
                    if ( is_array( $before ) ) {
                        $before_url = $before['url'] ?? '';
                    } elseif ( is_numeric( $before ) ) {
                        $before_url = wp_get_attachment_url( $before );
                    } else {
                        $before_url = $before;
                    }

                    if ( is_array( $after ) ) {
                        $after_url = $after['url'] ?? '';
                    } elseif ( is_numeric( $after ) ) {
                        $after_url = wp_get_attachment_url( $after );
                    } else {
                        $after_url = $after;
                    }

                    if ( ! $before_url || ! $after_url ) continue;

                    $caption       = $slide['caption'] ?? '';
                    $months        = $slide['months'] ?? '';
                    $consultations = $slide['consultations'] ?? '';
                ?>
                <div class="ba-slide" data-index="<?php echo esc_attr( $i ); ?>">
                    <div class="ba-comparison">
                        <figure>
                            <img src="<?php echo esc_url( $after_url ); ?>" class="ba-img ba-img--after" alt="After">
                            <img src="<?php echo esc_url( $before_url ); ?>" class="ba-img ba-img--before" alt="Before">
                            <div class="ba-divisor"></div>
                            <div class="ba-handle"><svg width="34" height="10" viewBox="0 0 34 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.27591 9.64389C8.16496 10.1539 8.88574 9.73609 8.88574 8.71151L8.88574 1.15058C8.88574 0.124983 8.16496 -0.292251 7.27591 0.21724L0.667277 4.00724C-0.222075 4.5174 -0.222075 5.34392 0.667277 5.85395L7.27591 9.64389Z" fill="#F5F5F5"/><path d="M26.6098 0.217434C25.7208 -0.292538 25 0.125237 25 1.14981V8.71074C25 9.73635 25.7208 10.1536 26.6098 9.64409L33.2185 5.85408C34.1078 5.34393 34.1078 4.51741 33.2185 4.00738L26.6098 0.217434Z" fill="#F5F5F5"/></svg></div>
                            <span class="ba-label ba-label--before">BEFORE</span>
                            <span class="ba-label ba-label--after">AFTER</span>
                        </figure>
                        <input type="range" min="0" max="100" value="50" class="ba-range" aria-label="Drag to compare before and after">
                    </div>
                    <div class="ba-slide-info text-center">
                        <?php if ( $caption ) : ?>
                            <span class="ba-caption"><?php echo esc_html( $caption ); ?></span>
                        <?php endif; ?>
                        <?php if ( $months || $consultations ) : ?>
                        <div class="ba-meta">
                            <?php if ( $months ) : ?>
                                <span>Months: <strong><?php echo esc_html( $months ); ?></strong></span>
                            <?php endif; ?>
                            <?php if ( $consultations ) : ?>
                                <span>Consultation: <strong><?php echo esc_html( $consultations ); ?></strong></span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="ba-nav-row">
                <button class="ba-nav ba-nav--prev" type="button" aria-label="Previous slide">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 4L7 10L13 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="ba-nav ba-nav--next" type="button" aria-label="Next slide">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 4L13 10L7 16" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $cta_button ) : ?>
        <div class="ba-cta text-center">
            <a class="btn secondary-btn"
               href="<?php echo esc_url( $cta_button['url'] ); ?>"
               target="<?php echo esc_attr( $cta_button['target'] ?: '_self' ); ?>">
                <?php echo esc_html( $cta_button['title'] ); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
