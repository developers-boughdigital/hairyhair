<?php
/**
 * Component: Shop Section (Product Slider + Features Split + Dual Gender Cards)
 *
 * ACF Flexible Content layout name: shop_section
 *
 * ── Sub-group: product_slider ────────────────────────────────────────────────
 *   ps_title        (text)
 *   ps_products     (relationship)  post_type: product
 *
 * ── Sub-group: features_split ────────────────────────────────────────────────
 *   fs_title        (text)
 *   fs_title_color  (color)   default: #122B51
 *   fs_bg_color     (color)   default: #ffffff
 *   fs_features     (repeater)
 *     └ icon        (image)
 *     └ title       (text)
 *     └ content     (textarea)
 *
 * ── Sub-group: dual_gender_cards ─────────────────────────────────────────────
 *   dgc_cards       (repeater — max 2)
 *     └ label         (text)
 *     └ description   (textarea)
 *     └ button        (link)
 *     └ button_style  (text)
 *     └ bg_color      (color)   solid fallback colour  default: #C8D8ED
 *     └ bg_gradient   (text)    full CSS gradient string — overrides bg_color when set
 *                               e.g. linear-gradient(180deg, rgba(61,124,209,0) 21.57%, #3D7CD1 100%)
 *     └ text_color        (color)   default: #122B51
 *     └ icon              (image)
 *     └ product_image     (image)
 *     └ bg_image          (image)
 */

/* ── 1. Product Slider ───────────────────────────────────────────────────── */
$ps_title    = get_sub_field( 'ps_title' );
$ps_products = get_sub_field( 'ps_products' );

/* ── 2. Features Split ───────────────────────────────────────────────────── */
$fs_title       = get_sub_field( 'fs_title' );
$fs_features    = get_sub_field( 'fs_features' );
$fs_bg_color    = get_sub_field( 'fs_bg_color' ) ?: '#ffffff';
$fs_title_color = get_sub_field( 'fs_title_color' ) ?: '#122B51';

/* ── 3. Dual Gender Cards ────────────────────────────────────────────────── */
$dgc_title = get_sub_field( 'dgc_title' );
$dgc_cards = get_sub_field( 'dgc_cards' );
?>

<section class="component-shop-section">

<?php /* ════ BLOCK 1: Product Slider ════ */ ?>
<section class="product mb-0">
    <div class="container">
        <?php if ( $ps_title ) : ?>
            <h2><?php echo esc_html( $ps_title ); ?></h2>
        <?php endif; ?>
        <div class="product-slider-wrapper">
            <?php
            if ( $ps_products && is_array( $ps_products ) ) :
                foreach ( $ps_products as $post ) :
                    setup_postdata( $post );
                    $product = wc_get_product( $post->ID );
                    if ( ! $product ) { continue; }
                    include locate_template( 'template-part/product-block.php' );
                endforeach;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<?php /* ════ BLOCK 2: Features Split ════ */ ?>
<div class="component-features-split shop-section-fs"
     style="background-color:<?php echo esc_attr( $fs_bg_color ); ?>;">
    <div class="container">
        <div class="row align-items-start">

            <div class="col-lg-5 col-md-12 fs-heading-col pe-lg-5 mb-5 mb-lg-0">
                <?php if ( $fs_title ) : ?>
                    <h2 class="fs-main-title fw-bold"
                        style="color:<?php echo esc_attr( $fs_title_color ); ?>;">
                        <?php echo esc_html( $fs_title ); ?>
                    </h2>
                <?php endif; ?>
            </div>

            <div class="col-lg-7 col-md-12 fs-features-col">
                <?php if ( $fs_features ) :
                    $total = count( $fs_features );
                    foreach ( $fs_features as $i => $feat ) :
                        $is_last = ( $i === $total - 1 );
                ?>
                <div class="fs-feature-item d-flex align-items-start gap-3<?php echo $is_last ? '' : ' fs-feature-divider'; ?>">

                    <?php if ( ! empty( $feat['icon'] ) ) : ?>
                        <div class="fs-feature-icon flex-shrink-0">
                            <img src="<?php echo esc_url( $feat['icon']['url'] ); ?>"
                                 alt="<?php echo esc_attr( $feat['icon']['alt'] ); ?>" />
                        </div>
                    <?php endif; ?>

                    <div class="fs-feature-text">
                        <?php if ( ! empty( $feat['title'] ) ) : ?>
                            <h5 class="fs-feature-title fw-bold mb-1">
                                <?php echo esc_html( $feat['title'] ); ?>
                            </h5>
                        <?php endif; ?>
                        <?php if ( ! empty( $feat['content'] ) ) : ?>
                            <p class="fs-feature-desc mb-0">
                                <?php echo esc_html( $feat['content'] ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; endif; ?>
            </div>

        </div>
    </div>
</div>

<?php /* ════ BLOCK 3: Dual Gender Cards ════ */ ?>
<div class="component-dual-gender-cards shop-section-dgc">
    <div class="container px-0">

        <?php if ( $dgc_title ) : ?>
            <h2 class="global-title text-center"><?php echo esc_html( $dgc_title ); ?></h2>
        <?php endif; ?>

        <div class="row dgc-row-gap-mobile">

            <?php if ( $dgc_cards ) :
                foreach ( $dgc_cards as $card_idx => $card ) :
                    $label         = $card['label']         ?? '';
                    $description   = $card['description']   ?? '';
                    $button        = $card['button']        ?? null;
                    $button_style  = ! empty( $card['button_style'] ) ? $card['button_style'] : 'secondary-btn';
                    $bg_color     = ! empty( $card['bg_color'] )     ? $card['bg_color']     : '#C8D8ED';
                    $bg_gradient  = ! empty( $card['bg_gradient'] )  ? $card['bg_gradient']  : '';
                    $text_color   = ! empty( $card['text_color'] )   ? $card['text_color']   : '#122B51';
                    $icon              = $card['icon']              ?? null;
                    $product_image     = $card['product_image']     ?? null;
                    $bg_image          = $card['bg_image']          ?? null;
                    $product_col_class = ! empty( $card['product_col_class'] ) ? ' ' . $card['product_col_class'] : '';
                    $content_col_class = ! empty( $card['content_col_class'] ) ? ' ' . $card['content_col_class'] : '';

                    // Gradient field takes priority; fall back to solid bg_color
                    if ( $bg_gradient ) {
                        $card_style = 'background:' . esc_attr( $bg_gradient ) . ';';
                    } else {
                        $card_style = 'background-color:' . esc_attr( $bg_color ) . ';';
                    }

                    // Card 0 (left): product image LEFT, text content RIGHT
                    // Card 1 (right): text content LEFT, product image RIGHT
                    $product_order = ( $card_idx === 0 ) ? 'order-0' : 'order-1';
                    $content_order = ( $card_idx === 0 ) ? 'order-1' : 'order-0';
            ?>
            <div class="col-lg-6 col-md-6 col-12">
                <div class="dgc-card position-relative overflow-hidden" style="<?php echo $card_style; ?>">

                    <?php if ( $bg_image ) : ?>
                        <img src="<?php echo esc_url( $bg_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $bg_image['alt'] ); ?>"
                             class="dgc-bg-img" />
                    <?php endif; ?>

                    <div class="dgc-card-inner">

                        <!-- Product image column -->
                        <div class="col-lg-4 col-md-3 col-5 <?php echo esc_attr( $product_order . $product_col_class ); ?>">
                            <?php if ( $product_image ) : ?>
                                <img src="<?php echo esc_url( $product_image['url'] ); ?>"
                                     alt="<?php echo esc_attr( $product_image['alt'] ); ?>"
                                     class="" />
                            <?php endif; ?>
                        </div>

                        <!-- Text content column -->
                        <div class="col-lg-7 col-md-8 col-6 dgc-pro-content <?php echo esc_attr( $content_order . $content_col_class ); ?>"
                             style="color:<?php echo esc_attr( $text_color ); ?>;">

                            <?php if ( $icon ) : ?>
                                <div class="dgc-icon mb-3">
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                         alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                </div>
                            <?php endif; ?>

                            <?php if ( $label ) : ?>
                                <h3 class="dgc-label fw-bold mb-3"
                                    style="color:<?php echo esc_attr( $text_color ); ?>;">
                                    <?php echo esc_html( $label ); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if ( $description ) : ?>
                                <p class="dgc-desc mb-4"
                                   style="color:<?php echo esc_attr( $text_color ); ?>;">
                                    <?php echo esc_html( $description ); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ( $button ) : ?>
                                <a class="btn <?php echo esc_attr( $button_style ); ?>"
                                   href="<?php echo esc_url( $button['url'] ); ?>"
                                   target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                                    <?php echo esc_html( $button['title'] ); ?>
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
            <?php endforeach; endif; ?>

        </div>
    </div>
</div>

</section>
