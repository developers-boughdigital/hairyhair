<?php
/**
 * Component: Featured Products Section
 *
 * ACF Flexible Content layout name: featured_products
 * Field group: group_shop_fp_section_001  (attached to shop page ID 47)
 *
 * Sub-fields:
 *   fp_title    (text)         — section heading, default "Featured Products"
 *   fp_products (relationship) — up to 5 WC_Product objects, in drag-drop order
 *
 * Rendered by: archive-product.php via have_rows( 'fp_section', 47 ) loop.
 *
 * Task #1 rule: order must be Regrow & Boost → Regrow & Block Female Pill →
 *               Regrow & Boost Duo → Regrow Topical Spray → Gummy Pill.
 *               Admin enforces this by dragging products into the correct order.
 */

$fp_title    = get_sub_field( 'fp_title' );
$fp_products = get_sub_field( 'fp_products' );

if ( ! $fp_products ) {
    return; // Nothing to render — admin hasn't saved products yet.
}
?>

<section class="shop-featured-products product">
    <div class="container">
        <div class="row">
            <div class="col-12">

                <div class="global-title">
                    <h2><?php echo esc_html( $fp_title ?: 'Featured Products' ); ?></h2>
                </div>

                <div class="product-slider-wrapper">
                    <?php
                    global $post, $product;
                    foreach ( $fp_products as $product ) :
                        $post = get_post( $product->get_id() );
                        setup_postdata( $post );
                        include get_template_directory() . '/template-part/product-block.php';
                    endforeach;
                    wp_reset_postdata();
                    ?>
                </div>

                <div class="features-shop-btn">
                    <a class="btn secondary-btn" href="/shop/"><span>View All Products</span></a>
                </div>

            </div>
        </div>
    </div>
</section>
