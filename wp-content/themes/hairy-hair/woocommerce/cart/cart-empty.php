<?php
/**
 * Empty Cart Override
 * Copy this file to your-theme/woocommerce/cart/cart-empty.php
 */
defined( 'ABSPATH' ) || exit;

// Any notices or hooks before the empty cart
wc_print_notices();
do_action( 'woocommerce_before_cart' );
?>

<section class="empty-cart-wrap">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center">
            <img 
              src="<?php echo esc_url( get_template_directory_uri() . '/assets/empty-cart.png' ); ?>" 
              alt="empty-cart"
            >
        </div>
        <h4><?php esc_html_e( 'Your cart is empty. Start shopping!', 'woocommerce' ); ?></h4>
        <p><?php esc_html_e( 'Tap below to start filling your cart.', 'woocommerce' ); ?></p>
        <button 
          class="btn dark-btn"
          onclick="window.location.href='<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>'"
        >
          <?php esc_html_e( 'shop now', 'woocommerce' ); ?>
        </button>
    </div>
</section>

<div class="container-fluid">
    <div class="bg-white radius-30">
        <section class="product mb-3">
            <div class="container">
                <h2><?php esc_html_e( 'Hairy Hair Products', 'woocommerce' ); ?></h2>
                 <div class="product-slider-wrapper">
                            <?php
                                // 1. Query top 10 best‐selling products
                                $args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => 10,
                                    'meta_key'       => 'total_sales',
                                    'orderby'        => 'meta_value_num',
                                    'order'          => 'DESC',
                                );
                                $products = new WP_Query( $args );

                                if ( $products->have_posts() ) :
                                    while ( $products->have_posts() ) : $products->the_post();
                                        global $product;
                                        // Preferred: will look in child-theme first, then parent-theme
                                       include get_template_directory() . '/template-part/product-block.php';

                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                                ?>

                        </div>
            </div>
        </section>
    </div>
</div>

<?php
// Any hooks after rendering the empty cart
do_action( 'woocommerce_after_cart' );
