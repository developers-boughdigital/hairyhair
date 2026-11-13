<?php
/**
 * Component: Hero Classic (Split Layout)
 *
 * Works in TWO contexts:
 *   1. Inside ACF flexible_content loop  → reads via get_sub_field()
 *   2. Direct include from a page template → reads existing page-level ACF fields:
 *        banner_title, banner_description, banner_button, banner_image
 *      Pass the campaign slot name before including:
 *        set_query_var( 'hh_hero_campaign_slot', 'Home Hero' );
 *        get_template_part( 'template-part/components/layout', 'hero_classic' );
 */

// ── Detect context ────────────────────────────────────────────────────────────
$in_flex_context = function_exists( 'get_row_layout' ) && ! empty( get_row_layout() );

if ( $in_flex_context ) {
    // ── Flexible Content sub-fields ───────────────────────────────────────────
    $campaign_slot     = get_sub_field( 'campaign_slot' );
    $title             = get_sub_field( 'title' );
    $description       = get_sub_field( 'description' );
    $button            = get_sub_field( 'button' );
    $image             = get_sub_field( 'image' );
    $show_review_badge = (bool) get_sub_field( 'show_review_badge' );
    $bg_image          = get_sub_field( 'bg_image' );
    $overlay_color     = get_sub_field( 'overlay_color' ) ?: 'rgba(0,0,0,0.45)';
    $extra_class       = get_sub_field( 'extra_class' );
    $left_col_class    = get_sub_field( 'left_col_class' );
    $right_col_class   = get_sub_field( 'right_col_class' );
    $section_id        = get_sub_field( 'section_id' );
    $right_col_id      = get_sub_field( 'right_col_id' );
} else {
    // ── Page-level ACF fields (direct homepage include) ───────────────────────
    $campaign_slot     = get_query_var( 'hh_hero_campaign_slot', '' );
    $title             = get_field( 'banner_title' );
    $description       = get_field( 'banner_description' );  // already outputs raw HTML
    $button            = get_field( 'banner_button' );
    $image             = get_field( 'banner_image' );
    $show_review_badge = true;
    $bg_image          = null;
    $overlay_color     = 'rgba(0,0,0,0.45)';
    $extra_class       = '';
    $left_col_class    = '';
    $right_col_class   = '';
    $section_id        = get_field( 'section_id' );
    $right_col_id      = get_field( 'right_col_id' );
}

// ── Campaign widget (optional override) ───────────────────────────────────────
$campaign_html = '';
if ( $campaign_slot ) {
    $campaign_html = do_shortcode( '[hh_campaign_widget slot="' . esc_attr( $campaign_slot ) . '"]' );
}

// ── Build section classes & inline style ─────────────────────────────────────
$section_classes = 'main-banner-content mb-0';
if ( $extra_class ) {
    $sanitized_extra = implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( $extra_class ) ) ) );
    $section_classes .= ' ' . $sanitized_extra;
}

$inline_style = '';
if ( $campaign_html !== '' ) {
    $inline_style = 'background-image:none !important;';
} elseif ( $bg_image ) {
    $inline_style = 'background-image:url(' . esc_url( $bg_image['url'] ) . ');';
}
?>
<section <?php if ( $section_id ) echo 'id="' . esc_attr( $section_id ) . '"'; ?> class="<?php echo esc_attr( $section_classes ); ?>" style="<?php echo $inline_style; ?>">

    <?php if ( $bg_image && $campaign_html === '' ) : ?>
    <div class="hero-classic-overlay" style="background:<?php echo esc_attr( $overlay_color ); ?>;"></div>
    <?php endif; ?>

    <?php if ( $campaign_html !== '' ) : ?>

        <div class="hero-banner-content-enabled"><?php echo $campaign_html; ?></div>

    <?php else : ?>

        <div class="container hero-classic-inner">
            <div class="row align-items-center">

                <!-- Text / Left Column -->
                <?php
                $left_col_classes = 'col-lg-5 col-md-12 col-sm-12 py-5 ban-left-content-col';
                if ( $left_col_class ) {
                    $left_col_classes .= ' ' . implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( $left_col_class ) ) ) );
                }
                ?>
                <div class="<?php echo esc_attr( $left_col_classes ); ?>">
                    <div class="ban-left-content">

                        <?php if ( ! $in_flex_context ) : ?>
                        <nav aria-label="breadcrumb">
                            <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                <?php if ( function_exists( 'bcn_display' ) ) {
                                    bcn_display();
                                } ?>
                            </div>
                        </nav>
                        <?php endif; ?>

                        <?php if ( $title ) : ?>
                            <h1 class="fw-bold mb-3"><?php echo esc_html( $title ); ?></h1>
                        <?php endif; ?>

                        <?php if ( $description ) : ?>
                            <div class="hero-description mb-4">
                                <?php echo wp_kses_post( $description ); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ( $button ) : ?>
                        <div class="contact-btn mt-4">
                            <a class="btn secondary-btn"
                               href="<?php echo esc_url( $button['url'] ); ?>"
                               target="<?php echo esc_attr( $button['target'] ); ?>">
                                <?php echo esc_html( $button['title'] ); ?>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- Spacer — image is positioned absolute to section, outside grid flow -->  
                <div class="col-lg-7 d-none d-lg-block"></div>

                <!-- Image — absolute to <section> (full viewport width), flush to right of screen -->
                <?php
                $right_col_classes = 'ban-right-img ban-right-img-col position-absolute top-0 bottom-0 end-0 d-none d-lg-flex align-items-center justify-content-end';
                if ( $right_col_class ) {
                    $right_col_classes .= ' ' . implode( ' ', array_map( 'sanitize_html_class', preg_split( '/\s+/', trim( $right_col_class ) ) ) );
                }
                ?>
                <div <?php if ( $right_col_id ) echo 'id="' . esc_attr( $right_col_id ) . '"'; ?> class="<?php echo esc_attr( $right_col_classes ); ?>">

                    <?php if ( $image ) : ?>
                        <img src="<?php echo esc_url( $image['url'] ); ?>"
                             alt="<?php echo esc_attr( $image['alt'] ); ?>"
                             class="ban-hero-img" />
                    <?php endif; ?>

                    <?php if ( $show_review_badge ) : ?>
                    <div class="review-img">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/review-img.png"
                             alt="review-img" title="review-img">
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Mobile image — in normal flow on small screens -->
                <?php if ( $image ) : ?>
                <div class="col-12 d-lg-none text-center">
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>"
                         class="img-fluid ban-hero-img-mobile" />
                </div>
                <?php endif; ?>

            </div>
        </div>

    <?php endif; ?>

</section>
