<?php
/**
 * Component: Pills – Dual-bottle product CTA with benefits checklist
 *
 * ACF Flexible Content layout name: pills
 *
 * Fields:
 *   bg_color        (color)      section background, default: #E8EDF5
 *   padding         (text)       CSS padding, default: '60px 0'
 *   title           (text)       e.g. "Buy Hair Pills"
 *   price_text      (text)       e.g. "$70/Month (with free shipping)"
 *   badge_text      (text)       e.g. "30% Off your first 3 months"
 *   badge_bg_color  (color)      badge background, default: #D4E4F7
 *   features        (repeater)
 *     └ feature_text (text)      supports <strong> in output
 *   cta_button      (link)       CTA button
 *   ask_text        (text)       "Ask a Question" label
 *   ask_url         (url)        "Ask a Question" URL
 *   share_text      (text)       "Share" label
 *   product_image      (image)      single bottle image (PNG, JPG or SVG)
 *   content_col_class  (text)       extra CSS class(es) on the right content column
 */

$title          = get_sub_field( 'title' );
$price_text     = get_sub_field( 'price_text' );

// wysiwyg wraps content in <p>; strip outer tags so bold/inline markup
// still renders cleanly inside <h2> and <span>.
if ( $title ) {
    $title = preg_replace( '/^\s*<p[^>]*>|<\/p>\s*$/i', '', trim( $title ) );
}
if ( $price_text ) {
    $price_text = preg_replace( '/^\s*<p[^>]*>|<\/p>\s*$/i', '', trim( $price_text ) );
}
$badge_text     = get_sub_field( 'badge_text' );
$badge_bg_color = get_sub_field( 'badge_bg_color' ) ?: '#D4E4F7';
$features       = get_sub_field( 'features' );
$cta_button     = get_sub_field( 'cta_button' );
$ask_text       = get_sub_field( 'ask_text' );
$ask_url        = get_sub_field( 'ask_url' );
$share_text     = get_sub_field( 'share_text' );
$product_image     = get_sub_field( 'product_image' );
$content_col_class = get_sub_field( 'content_col_class' );
?>
<section class="component-pills">
    <div class="container">
        <div class="row align-items-center pills-row">

            <!-- Left: Product bottles (single column) -->
            <div class="col-lg-6 col-md-12 pills-images-col">
                <div class="pills-images pills-images--single-col">

                    <?php if ( $product_image ) :
                        $svg_path = get_attached_file( $product_image['id'] );
                        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
                        $svg_html = ( $svg_path && file_exists( $svg_path ) ) ? file_get_contents( $svg_path ) : '';
                    ?>
                    <div class="pills-bottle">
                        <?php if ( $svg_html ) : ?>
                            <span class="pills-bottle-svg" aria-label="<?php echo esc_attr( $product_image['alt'] ); ?>" role="img">
                                <?php echo $svg_html; // Inline SVG from trusted admin-uploaded file. ?>
                            </span>
                        <?php else : ?>
                            <img src="<?php echo esc_url( $product_image['url'] ); ?>"
                                 alt="<?php echo esc_attr( $product_image['alt'] ); ?>"
                                 class="pills-bottle-svg" />
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Right: Content -->
            <div class="col-lg-6 col-md-12 pills-content-col<?php echo $content_col_class ? ' ' . esc_attr( $content_col_class ) : ''; ?>">

                <?php if ( $title ) : ?>
                    <h2 class="pills-title"><?php echo wp_kses_post( $title ); ?></h2>
                <?php endif; ?>

                <?php if ( $price_text || $badge_text ) : ?>
                <div class="pills-price-row d-flex flex-wrap align-items-center gap-3">
                    <?php if ( $price_text ) : ?>
                        <strong class="pills-price"><?php echo wp_kses_post( $price_text ); ?></strong>
                    <?php endif; ?>
                    <?php if ( $badge_text ) : ?>
                        <span class="pills-badge" style="background-color:<?php echo esc_attr( $badge_bg_color ); ?>;">
                            <?php echo esc_html( $badge_text ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if ( $features ) : ?>
                <div class="pills-benefits-card">
                    <?php $total = count( $features );
                    foreach ( $features as $i => $feature ) :
                        $is_last = ( $i === $total - 1 );
                    ?>
                    <div class="pills-benefit-item<?php echo $is_last ? '' : ' pills-benefit-divider'; ?>">
                        <span class="pills-check-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="12" fill="#3B82C4"/>
                                <path d="M7 12.5L10.5 16L17 9" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="pills-benefit-text">
                            <?php echo wp_kses( $feature['feature_text'], [
                                'strong' => [],
                                'b'      => [],
                                'em'     => [],
                                'i'      => [],
                                'br'     => [],
                                'span'   => [ 'class' => [], 'style' => [] ],
                                'a'      => [ 'href' => [], 'target' => [], 'rel' => [], 'class' => [] ],
                            ] ); ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Bottom actions row -->
                <div class="pills-actions d-flex flex-wrap align-items-center gap-4">

                    <?php if ( $cta_button ) : ?>
                    <a class="btn pills-cta-btn"
                       href="<?php echo esc_url( $cta_button['url'] ); ?>"
                       target="<?php echo esc_attr( $cta_button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $cta_button['title'] ); ?>
                    </a>
                    <?php endif; ?>

                    <?php if ( $ask_url ) : ?>
                    <a class="pills-action-link"
                       href="<?php echo esc_url( $ask_url ); ?>">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="10" stroke="#122B51" stroke-width="1.5"/>
                            <text x="11" y="15" text-anchor="middle" font-size="12" font-weight="600" fill="#122B51">?</text>
                        </svg>
                        <span><?php echo esc_html( $ask_text ?: 'Ask a Question' ); ?></span>
                    </a>
                    <?php endif; ?>

                    <?php if ( $share_text ) : ?>
                    <button class="pills-action-link pills-share-btn" type="button" onclick="if(navigator.share){navigator.share({title:document.title,url:window.location.href})}else{navigator.clipboard.writeText(window.location.href);alert('Link copied!')}">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="15" cy="4" r="2.5" stroke="#122B51" stroke-width="1.5"/>
                            <circle cx="5" cy="10" r="2.5" stroke="#122B51" stroke-width="1.5"/>
                            <circle cx="15" cy="16" r="2.5" stroke="#122B51" stroke-width="1.5"/>
                            <line x1="7.2" y1="8.9" x2="12.8" y2="5.1" stroke="#122B51" stroke-width="1.5"/>
                            <line x1="7.2" y1="11.1" x2="12.8" y2="14.9" stroke="#122B51" stroke-width="1.5"/>
                        </svg>
                        <span><?php echo esc_html( $share_text ); ?></span>
                    </button>
                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>
</section>
