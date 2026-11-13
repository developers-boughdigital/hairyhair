<?php
/**
 * Plugin Name: Hairy Hair Performance
 * Description: Speed optimisations — GTM defer, WooCommerce cleanup, LCP preload, image fixes, star SVGs.
 * Version:     1.1.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'script_loader_tag', 'hh_defer_gtm', 10, 3 );
function hh_defer_gtm( $tag, $handle, $src ) {
    if ( strpos( $src, 'googletagmanager.com/gtm.js' ) !== false ) {
        return str_replace( ' src=', ' defer async src=', $tag );
    }
    return $tag;
}

add_action( 'wp_enqueue_scripts', 'hh_dequeue_woo_on_non_shop', 99 );
function hh_dequeue_woo_on_non_shop() {
    if ( function_exists( 'is_woocommerce' ) &&
         ! is_woocommerce() && ! is_cart() &&
         ! is_checkout() && ! is_account_page() ) {
        wp_dequeue_script( 'wc-cart-fragments' );
        wp_dequeue_script( 'woocommerce' );
        wp_dequeue_script( 'wc-add-to-cart' );
        wp_dequeue_style( 'woocommerce-general' );
        wp_dequeue_style( 'woocommerce-layout' );
        wp_dequeue_style( 'woocommerce-smallscreen' );
    }
}

add_action( 'wp_head', 'hh_preload_lcp_image', 1 );
function hh_preload_lcp_image() {
    if ( ! is_front_page() ) return;
    $hero = 'https://hairyhair.com.au/wp-content/uploads/2026/03/mainbanner.png';
    echo '<link rel="preload" as="image" href="' . esc_url( $hero ) . '" fetchpriority="high">' . "\n";
}

add_filter( 'the_content', 'hh_fix_lazy_load_dupes', 20 );
add_filter( 'wp_get_attachment_image', 'hh_fix_lazy_load_dupes', 20 );
function hh_fix_lazy_load_dupes( $content ) {
    if ( empty( $content ) ) return $content;
    return preg_replace(
        '/<img([^>]*)\s+src=["\']data:image\/svg\+xml[^"\']*["\']([^>]*)>/i',
        '', $content
    );
}

add_action( 'wp_footer', 'hh_js_cleanup', 5 );
function hh_js_cleanup() { ?>
    <script>
    (function() {
        document.querySelectorAll('img[src^="data:image/svg+xml"]').forEach(function(img) {
            var siblings = img.parentNode
                ? img.parentNode.querySelectorAll('img:not([src^="data:image/svg+xml"])')
                : [];
            if (siblings.length > 0) img.remove();
        });
        document.querySelectorAll('img[src*="rebrand-1-1"], img[src*="hero"]').forEach(function(img) {
            img.loading = 'eager';
            img.setAttribute('fetchpriority', 'high');
        });
    })();
    </script>
<?php }

// ── DEFER CLARITY + FACEBOOK PIXEL (HFCM head snippets) ─────────────────────
// Intercepts wp_head output buffer, strips matching <script> blocks,
// then re-injects them in wp_footer wrapped in a 3-second setTimeout.

add_action( 'wp_head', 'hh_track_ob_start', 0 );
function hh_track_ob_start() {
    ob_start();
}

add_action( 'wp_head', 'hh_track_ob_end', 100 );
function hh_track_ob_end() {
    $html = ob_get_clean();
    if ( $html === false ) {
        return;
    }
    $GLOBALS['hh_deferred_tracking'] = [];
    $html = preg_replace_callback(
        '~<script\b[^>]*>.*?</script>~si',
        function ( $m ) {
            if ( strpos( $m[0], 'clarity.ms' ) !== false
              || strpos( $m[0], 'fbevents.js' ) !== false ) {
                $inner = preg_replace( '~\A\s*<script[^>]*>(.*)</script>\s*\z~si', '$1', $m[0] );
                $GLOBALS['hh_deferred_tracking'][] = trim( $inner );
                return '';
            }
            return $m[0];
        },
        $html
    );
    echo $html;
}

add_action( 'wp_footer', 'hh_output_deferred_tracking', 1 );
function hh_output_deferred_tracking() {
    if ( empty( $GLOBALS['hh_deferred_tracking'] ) ) {
        return;
    }
    $code = implode( ";\n", $GLOBALS['hh_deferred_tracking'] );
    echo '<script>setTimeout(function(){' . "\n" . $code . "\n" . '},3000);</script>' . "\n";
}

function hh_star_svg( $filled = true, $color = '#F5A623' ) {
    $fill = $filled ? esc_attr( $color ) : 'none';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" '
         . 'aria-hidden="true" style="display:inline-block;vertical-align:middle">'
         . '<path fill="' . $fill . '" stroke="' . esc_attr( $color ) . '" stroke-width="1.5" '
         . 'd="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>'
         . '</svg>';
}

function hh_five_stars( $count = 5 ) {
    $out = '<span class="hh-stars" aria-label="' . (int) $count . ' out of 5 stars">';
    for ( $i = 1; $i <= 5; $i++ ) { $out .= hh_star_svg( $i <= $count ); }
    return $out . '</span>';
}

add_action( 'wp_head', 'hh_star_styles' );
function hh_star_styles() {
    echo '<style>.hh-stars{display:inline-flex;gap:2px;align-items:center}</style>' . "\n";
}
