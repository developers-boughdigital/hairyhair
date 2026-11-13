<?php
/**
 * Override WooCommerce Cart Template
 * Copy this file to your-theme/woocommerce/cart/cart.php
 */
defined( 'ABSPATH' ) || exit;

// Print any notices and open the cart
wc_print_notices();
do_action( 'woocommerce_before_cart' );
?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
<section class="about-cart">
    <div class="container">
        <div class="row">

            <!-- LEFT: Cart Items -->
            <div class="col-lg-8 col-md-12">
                <div class="cart-left-side-section">

                    <div class="cart-header">
                        <div class="pro-heading"><?php esc_html_e( 'Product', 'woocommerce' ); ?></div>
                        <div class="price-heading"><?php esc_html_e( 'Price', 'woocommerce' ); ?></div>
                        <div class="qnt-heading"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></div>
                        <div class="total-heading"><?php esc_html_e( 'Total', 'woocommerce' ); ?></div>
                    </div>

                    <style> .item_not_applicable { background: #ff000021; } </style>
                    <?php if ( ! WC()->cart->is_empty() ) : ?>
                        <?php 
                            $is_cart_ok = true; 
                            $item_class = '';//'item_not_applicable';
                        ?>
                        <?php $acess_to_add_prescriptive_products = $_SESSION['acess_to_add_prescriptive_products']; ?>
                        <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                            //TODO: product id is not correct, it should be cart_item['product_id'] but it is not working
                            $product_type = get_field( 'product_type', $_product->get_id() - 1 );  
                       
                            $price      = $_product ? $_product->get_price() : 0;
                            $qty        = $cart_item['quantity'];
                            $line_total = $_product ? WC()->cart->get_product_subtotal( $_product, $qty ) : '';
                            if ( ! $_product || ! $_product->exists() || $qty <= 0 ) {
                                continue;
                            }
                        ?>
                        <div class="cart-item 
                            <?php  

                                if(trim($product_type) == 'Prescriptive' && $acess_to_add_prescriptive_products === false) {
                                    echo $item_class; 
                                    $is_cart_ok = false;
                                }
                            ?>" data-price="<?php echo esc_attr( $price ); ?>" > 
                            <div class="product-info">
                                <?php
                                echo apply_filters(
                                    'woocommerce_cart_item_remove_link',
                                    sprintf(
                                        '<button type="submit" class="remove-btn" name="remove_cart_item" value="%s" aria-label="%s">×</button>',
                                        esc_attr( $cart_item_key ),
                                        esc_attr__( 'Remove this item', 'woocommerce' )
                                    ),
                                    $cart_item_key
                                );
                                ?>
                                <?php echo $_product->get_image( 'thumbnail' ); ?>
                                <div class="product-details">
                                    <h4>
                                        <a style="color: #000; text-decoration: none;" href="<?php echo esc_url( get_permalink( $_product->get_id() ) ); ?>">
                                            <?php echo esc_html( $_product->get_name() ); ?>
                                        </a>
                                    </h4>
                                    <?php //echo 'Product ID: '.$_product->get_id().'<br>'; ?>
                                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); ?>
                                </div>
                            </div>
                            <div class="price"><?php echo wc_price( $price ); ?></div>
                            <div class="quantity">
                                <button type="button" class="decrease">−</button>
                                <input
                                  type="number"
                                  class="qty"
                                  name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]"
                                  value="<?php echo esc_attr( $qty ); ?>"
                                  min="0"
                                  max="100"
                                />
                                <button type="button" class="increase">+</button>
                            </div>
                            <div class="total"><?php echo wp_kses_post( $line_total ); ?></div>
                        </div>
                        <?php endforeach; ?>
                        <?php
                        // === Upsell Section ===
                        // Ambil produk di cart
                        $cart_items = WC()->cart->get_cart();
                        $category_slugs = [];

                        // Ambil kategori produk dari semua item di cart
                        foreach ( $cart_items as $cart_item ) {
                            $product_id = $cart_item['product_id'];
                            $terms = get_the_terms( $product_id, 'product_cat' );

                            if ( $terms && ! is_wp_error( $terms ) ) {
                                foreach ( $terms as $term ) {
                                    $category_slugs[] = $term->slug;
                                }
                            }
                        }

                        // Hapus duplikat kategori
                        $category_slugs = array_unique( $category_slugs );

                        // Jika ada kategori, tampilkan produk terkait
                        if ( ! empty( $category_slugs ) ) :
                            $args = array(
                                'post_type'      => 'product',
                                'posts_per_page' => 4, // jumlah produk ditampilkan
                                'orderby'        => 'rand', // random biar lebih menarik
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field'    => 'slug',
                                        'terms'    => $category_slugs,
                                    ),
                                ),
                                'post__not_in'   => wp_list_pluck( $cart_items, 'product_id' ), // jangan tampilkan produk yang sudah ada di cart
                            );

                            $related_query = new WP_Query( $args );

                            if ( $related_query->have_posts() ) :
                        ?>
                                <div class="cart-upsell-section" style="display: none;">
                                    <h3>You may also like</h3>
                                    <ul class="products columns-4">
                                        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); global $product; ?>
                                            <li <?php wc_product_class( '', $product ); ?>>
                                                <a class="product-information" href="<?php the_permalink(); ?>">
                                                    <?php echo woocommerce_get_product_thumbnail(); ?>
                                                    <h2 class="woocommerce-loop-product__title"><?php the_title(); ?></h2>
                                                    <?php echo $product->get_price_html(); ?>
                                                </a>
                                                <?php woocommerce_template_loop_add_to_cart(); ?>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                        <?php
                            endif;
                            wp_reset_postdata();
                        endif;
                        ?>

                    <?php else : ?>


                        <p><?php esc_html_e( 'Your cart is currently empty.', 'woocommerce' ); ?></p>


                        
                    <?php endif; ?>

                </div>
            </div>

            <!-- RIGHT: Totals & Coupon -->
            <div class="col-lg-4 col-md-12">
                <div class="cart-box">
                    <div class="card">
                        <h2><?php esc_html_e( 'Cart Total', 'woocommerce' ); ?></h2>

                        <div class="summary-row">
                            <span><?php esc_html_e( 'Items', 'woocommerce' ); ?></span>
                            <span><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                        </div>

                        <div class="summary-row">
                            <span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
                            <span><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></span>
                        </div>

                        <?php if ( WC()->cart->get_cart_contents_tax() > 0 ) : ?>
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Tax and Charges', 'woocommerce' ); ?></span>
                            <span><?php echo wp_kses_post( wc_price( WC()->cart->get_cart_contents_tax() ) ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ( WC()->cart->get_discount_total() > 0 ) : ?>
                        <div class="summary-row">
                            <span><?php esc_html_e( 'Coupon Discount', 'woocommerce' ); ?></span>
                            <span>-<?php echo wp_kses_post( wc_price( WC()->cart->get_discount_total() ) ); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="summary-row total">
                            <span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
                            <span><?php echo wp_kses_post( WC()->cart->get_total() ); ?></span>
                        </div>

                        <button
                          type="submit"
                          class="btn dark-btn mb-3"
                          name="update_cart"
                          value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"
                          disabled
                        >
                          <?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
                        </button>
                        <?php 
                            $category_link = get_term_link( 'prescriptive-treatments', 'product_cat' ); 
                        ?>
                        <a href="<?php echo esc_url( $category_link ); ?>" class="btn btn-info" style="margin-bottom:20px; display:block;">
                            <?php esc_html_e( '← Continue Shopping', 'woocommerce' ); ?>
                        </a>
                        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn secondary-btn">
                            <?php esc_html_e( 'Proceed to Checkout', 'woocommerce' ); ?>
                        </a>
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                    </div>
                </div>

                <?php if ( wc_coupons_enabled() ) : ?>
                <div class="promo">
                    <label for="coupon_code"><?php esc_html_e( 'Do you have a promotional code?', 'woocommerce' ); ?></label>
                    <small><?php esc_html_e( 'Please apply it below.', 'woocommerce' ); ?></small>
                    <input
                        type="text"
                        name="coupon_code"
                        class="input-text"
                        id="coupon_code"
                        placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>"
                        value="<?php echo isset( $_POST['coupon_code'] ) ? esc_attr( wp_unslash( $_POST['coupon_code'] ) ) : ''; ?>"
                    />
                    <button
                        type="submit"
                        class="dark-btn btn"
                        name="apply_coupon"
                        value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"
                    >
                        <?php esc_html_e( 'Apply', 'woocommerce' ); ?>
                    </button>
                    <?php do_action( 'woocommerce_cart_coupon' ); ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
</form>

<!-- Inline script to handle ± buttons and enable Update Cart -->
<script>
jQuery(function($){
  // grab cart URL and nonce from the form
  var cart_url = '<?php echo esc_url( wc_get_cart_url() ); ?>';
  var nonce    = $('input[name="woocommerce-cart-nonce"]').val();

  // 1) Remove item when remove-btn is clicked
  $('.about-cart').on('click', '.remove-btn', function(e){
    e.preventDefault();
    var key = $(this).val();
    // build the remove URL and redirect
    window.location.href = cart_url
      + '?remove_item=' + encodeURIComponent(key)
      + '&_wpnonce='     + encodeURIComponent(nonce);
  });

  // 2) Your existing qty +/- logic
  var $cart = $('.about-cart');
  $cart.on('click', '.increase, .decrease', function(){
    var $wrap = $(this).closest('.quantity'),
        $inp  = $wrap.find('input.qty'),
        val   = parseInt( $inp.val(), 10 ) || 0;

    $inp.val( $(this).hasClass('increase') ? val + 1 : Math.max(val - 1, 0) )
        .trigger('change');
  });

  // 3) Enable Update Cart button on qty change
  $cart.on('change', 'input.qty', function(){
    $('button[name="update_cart"]').prop('disabled', false);
  });
});
</script>

<?php do_action( 'woocommerce_after_cart' ); ?>

<style>
    .cart-upsell-section .products .button.add_to_cart_button {
        background: #0b1d38;
        border-radius: 12px;
        color: #fff !important;
        font-weight: 600;
        font-size: 20px;
        font-family: "Altone SemBd";
        line-height: 26px;
        text-transform: capitalize;
        margin-bottom: 12px;
        letter-spacing: -1px;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
        overflow: hidden;
        text-decoration: none;
        color: #0b1d38;
        text-align: center;
        width: 100%;
    }
    .cart-upsell-section {
        padding: 20px;
    }
    .cart-upsell-section h2 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        line-height: 20px;
    }
    .cart-upsell-section h2,
    .cart-upsell-section span {
        color: #000;
    }
    @media (min-width: 769px) {
        .cart-upsell-section ul.products li.product > a.product-information {
            min-height: 340px;
            display: block;
        }
    }
    /* === PRODUCT LIST MOBILE STYLING === */
    @media screen and (max-width: 768px) {
        ul.products li.product {
            justify-content: flex-start;
            gap: 12px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
        }

        /* Gambar produk */
        ul.products li.product img {
            width: 90px !important;
            height: 90px !important;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }

        /* Wrapper teks */
        ul.products li.product .product-information {
            
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            flex: 1;
            flex-wrap: nowrap !important;
            text-decoration: none;
        }

        /* Title produk */
        ul.products li.product .woocommerce-loop-product__title {
            font-size: 14px !important;
            margin: 0 0 5px 0;
            line-height: 1.3;
        }

        /* Harga produk */
        ul.products li.product .woocommerce-Price-amount {
            font-size: 13px !important;
            font-weight: bold;
            color: #000;
        }

        /* Subscription details supaya lebih kecil */
        ul.products li.product .subscription-details {
            font-size: 11px;
            color: #666;
            display: none;
        }

        /* Tombol add to cart */
        ul.products li.product .button {
            background-color: #000;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            margin-top: 6px;
            width: fit-content;
            transition: background-color 0.3s ease;
        }

        ul.products li.product .button:hover {
            background-color: #333;
        }
    }

</style>