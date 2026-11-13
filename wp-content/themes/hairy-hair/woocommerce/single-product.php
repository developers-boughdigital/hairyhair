<?php get_header(); ?>
<div class="main-page product-detail-page">
    <section class="white-radius-bg">
        <div class="wrapper">
            <div class="container">
                <?php

                if (function_exists('woocommerce_breadcrumb')) {
                    woocommerce_breadcrumb();
                }
                ?>

                <?php
                defined('ABSPATH') || exit;
                global $product;
                // Make sure WC’s variation JS is loaded for our custom form
                if ($product->is_type(array('variable', 'variable-subscription'))) {
                    wp_enqueue_script('wc-add-to-cart-variation');
                }
                ?>

                <div class="product-details">
                    <div class="row">
                        <div class="col-lg-7 col-md-12">


                            <div class="product-detail-slider">
                                <div class="main-slider bottom-arrows">
                                    <?php
                                    $attachment_ids = $product->get_gallery_image_ids();
                                    if ($attachment_ids) {
                                        foreach ($attachment_ids as $attachment_id) {
                                            $url = wp_get_attachment_image_url($attachment_id, 'full');
                                            $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                                            echo '<div><img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"></div>';
                                        }
                                    } else {
                                        $url = wp_get_attachment_image_url($product->get_image_id(), 'full');
                                        $alt = get_post_meta($product->get_image_id(), '_wp_attachment_image_alt', true);
                                        echo '<div><img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '"></div>';
                                    }
                                    ?>
                                </div>
                                <div class="thumb-slider">
                                    <?php
                                    if ($attachment_ids) {
                                        foreach ($attachment_ids as $attachment_id) {
                                            $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');
                                            $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
                                            echo '<div><img src="' . esc_url($thumb) . '" alt="' . esc_attr($alt) . '"></div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-5 col-md-12">
                            <div class="details-wrapper">
                                <div class="all-details">
                                    <?php
                                    $product_type = get_field('product_type', get_the_ID());

                                    if ($product_type === 'Prescriptive') {
                                        echo '<div class="product-badge badge-green-bg"><span>Prescriptive</span></div>';
                                    } elseif ($product_type === 'Non-Prescriptive') {
                                        echo '<div class="product-badge badge-purple-bg"><span>Non-Prescriptive</span></div>';
                                    }
                                    ?>
                                    <?php do_action('woocommerce_before_single_product'); ?>
                                    <h1><?php the_title(); ?></h1>

                                    <div class="rating">
                                        <div class="star-rating-wrap">
                                            <?php
                                            // WooCommerce star HTML + numeric rating
                                            echo wc_get_rating_html($product->get_average_rating());
                                            ?>
                                            <p><?php echo number_format_i18n($product->get_average_rating(), 1); ?>
                                                <span>(
                                                    <?php echo $product->get_review_count(); ?> Review<?php echo $product->get_review_count() > 1 ? 's' : ''; ?>
                                                    )</span>
                                            </p>
                                        </div>
                                        <div class="stock">
                                            <span>
                                                <?php
                                                if ($product->is_in_stock()) {
                                                    echo $product->get_stock_quantity() . ' Available In Stock';
                                                } else {
                                                    echo 'Out of Stock';
                                                }
                                                ?></span>
                                        </div>
                                    </div>
                                    <div class="price">
                                        <?php echo apply_filters('woocommerce_get_price_html', $product->get_price_html(), $product); ?>
                                        <?php

                                        // 2) If it’s on sale, calculate and display the % off
                                        if ($product->is_on_sale()) {
                                            $regular_price = (float) $product->get_regular_price();
                                            $sale_price    = (float) $product->get_sale_price();

                                            if ($regular_price > 0 && $sale_price < $regular_price) {
                                                $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                                                echo ' <span class="discount-percentage">' . $percentage . '% off</span>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="product-desc">
                                    <div class="grey-check-list">
                                        <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt); ?>
                                    </div>
                                    <div class="best-service">
                                        <div class="each-service d-flex flex-wrap">
                                            <div class="service-image">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/australian-made.png" alt="promise-1">
                                            </div>
                                            <p>Australian made</p>
                                        </div>
                                        <div class="each-service d-flex flex-wrap">
                                            <div class="service-image">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/clinically-backed-formula.png" alt="promise-1">
                                            </div>
                                            <p>Clinically Backed Formulas</p>
                                        </div>
                                        <div class="each-service d-flex flex-wrap">
                                            <div class="service-image">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/prescribe-by-doctors.png" alt="promise-1">
                                            </div>
                                            <p>Prescribed by Doctors</p>
                                        </div>
                                    </div>
                                </div>
                                <?php  ?>


                                <?php
                                // Buttons and Select 
                                ?>
                                <div class="wrapper">

                                    <?php
                                    // VARIABLE & VARIABLE-SUBSCRIPTION PRODUCTS
                                    if ($product->is_type(array('variable', 'variable-subscription'))) :
                                        // Gather data
                                        $available_variations = $product->get_available_variations();
                                        $attributes           = $product->get_variation_attributes();
                                        $selected_attributes  = $product->get_default_attributes();
                                    ?>

                                        <?php
                                        // Core hook before form (optional)
                                        do_action('woocommerce_before_add_to_cart_form');
                                        ?>

                                        <form
                                            class="variations_form cart"
                                            action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>"
                                            method="post"
                                            enctype="multipart/form-data"
                                            data-product_id="<?php echo absint($product->get_id()); ?>"
                                            data-product_variations="<?php echo htmlspecialchars(wp_json_encode($available_variations), ENT_QUOTES, 'UTF-8'); ?>">

                                            <?php do_action('woocommerce_before_variations_form'); ?>

                                            <div class="variations">
                                                <?php foreach ($attributes as $name => $options) :
                                                    $attr_key = sanitize_title($name);
                                                    $label    = wc_attribute_label($name);
                                                ?>
                                                    <div class="variation">
                                                        <span class="variation-label"><?php echo esc_html($label); ?></span>

                                                        <!-- Hidden select that WC’s variation script needs -->
                                                        <select
                                                            name="attribute_<?php echo esc_attr($attr_key); ?>"
                                                            data-attribute_name="attribute_<?php echo esc_attr($attr_key); ?>"
                                                            style="display:none;">
                                                            <option value="">
                                                                <?php echo esc_html(sprintf(__('Choose %s', 'woocommerce'), $label)); ?>
                                                            </option>
                                                            <?php foreach ($options as $opt) :
                                                                $is_selected = (isset($selected_attributes[$name]) && $selected_attributes[$name] === $opt);
                                                            ?>
                                                                <option
                                                                    value="<?php echo esc_attr($opt); ?>"
                                                                    <?php selected($is_selected); ?>><?php echo esc_html(apply_filters('woocommerce_variation_option_name', $opt)); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>

                                                        <!-- Your button-style swatches -->
                                                        <div class="variation-buttons variation-<?php echo esc_attr($attr_key); ?>">
                                                            <?php foreach ($options as $opt) : ?>
                                                                <button
                                                                    type="button"
                                                                    class="variation-button btn<?php echo (($selected_attributes[$name] ?? '') === $opt) ? ' selected' : ''; ?>"
                                                                    data-attr="<?php echo esc_attr($attr_key); ?>"
                                                                    data-value="<?php echo esc_attr($opt); ?>"><?php echo esc_html(apply_filters('woocommerce_variation_option_name', $opt)); ?></button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div><!-- /.variations -->

                                            <?php do_action('woocommerce_after_variations_form'); ?>

                                            <div class="single_variation_wrap">

                                                <!-- Your Buy Now button -->
                                                <?php session_start();
                                                $acess_to_add_prescriptive_products = $_SESSION['acess_to_add_prescriptive_products']; ?>
                                                <?php
                                                $product_type = get_field('product_type', $product->get_id());
                                                // echo 'Product ID: ' . $product->get_id() . '<br>';

                                                if ($product->is_in_stock()) {
                                                    //if ($product_type == 'Prescriptive' && !$acess_to_add_prescriptive_products) {
                                                ?>
                                                        <!-- <a class="btn dark-btn" href="<?php echo home_url(); ?>/hair-treatment-quiz">
                                                            <span>
                                                                <?php echo esc_html__(QUIZ_BUTTON, 'woocommerce'); ?>
                                                            </span>
                                                        </a> -->
                                                    <?php
                                                    //} else {
                                                    ?>
                                                        <?php
                                                        // Must include these hooks in this exact order:
                                                        do_action('woocommerce_before_single_variation');
                                                        do_action('woocommerce_single_variation');
                                                        do_action('woocommerce_after_single_variation');
                                                        ?>
                                                        <?php
                                                        if($product_type == 'Prescriptive' && !$acess_to_add_prescriptive_products) {
                                                            ?>
                                                            <a class="buy-now-button btn secondary-btn" href="<?php echo home_url(); ?>/hair-treatment-quiz" data-product-url="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                                                <span>
                                                                    <?php echo esc_html__( 'Buy Now', 'woocommerce' ); ?>
                                                                </span>
                                                            </a>

                                                            <button
                                                                type="submit"
                                                                class="d-none actualCartBtn"
                                                                >
                                                                <?php esc_html_e('Buy Now', 'your-text-domain'); ?>
                                                            </button>
                                                            <?php
                                                        } else { 
                                                        ?>
                                                        <button
                                                            type="submit"
                                                            class="buy-now-button btn secondary-btn"
                                                            >
                                                            <?php esc_html_e('Buy Now', 'your-text-domain'); ?>
                                                        </button>
                                                        <?php } ?>
                                                <?php
                                                   // }
                                                } else {
                                                    // Out of stock → disabled button
                                                    echo '<button class="btn dark-btn" disabled><span>'
                                                        . esc_html__('Out of Stock', 'woocommerce')
                                                        . '</span></button>';
                                                }
                                                ?>


                                            </div><!-- /.single_variation_wrap -->

                                            <?php do_action('woocommerce_after_add_to_cart_form'); ?>
                                        </form>

                                    <?php
                                    // SIMPLE PRODUCTS
                                    else :

                                        if (! $product->is_purchasable()) {
                                            return;
                                        }
                                    ?>

                                        <form
                                            class="cart"
                                            action="<?php echo esc_url($product->add_to_cart_url()); ?>"
                                            method="post"
                                            enctype="multipart/form-data">
                                            <div class="button-wrapper">
                                                <div class="quantity-price-wrapper">
                                                    <div class="quantity-selector">
                                                        <label><?php esc_html_e('Quantity', 'your-text-domain'); ?></label>
                                                        <div class="quantity-control">
                                                            <?php
                                                            woocommerce_quantity_input(array(
                                                                'min_value'   => apply_filters('woocommerce_quantity_input_min', 1, $product),
                                                                'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                                                                'input_value' => 1,
                                                            ));
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Add to Cart -->

                                                <?php
                                                $product_type = get_field('product_type', $product->get_id());
                                                if ($product->is_in_stock()) {
                                                    if ($product_type == 'Prescriptive' && !$acess_to_add_prescriptive_products) {
                                                ?>
                                                        <button
                                                            type="submit"
                                                            name="add-to-cart"
                                                            value="<?php echo esc_attr($product->get_id()); ?>"
                                                            class="single_add_to_cart_button btn dark-btn"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>
                                                        
                                                        <a class="buy-now-button btn secondary-btn" href="<?php echo home_url(); ?>/hair-treatment-quiz">
                                                            <span>
                                                                <?php echo esc_html__('Buy Now', 'woocommerce'); ?>
                                                            </span>
                                                        </a>

                                                        <button
                                                            type="button"
                                                            class="d-none actualCartBtn"
                                                            onclick="window.location.href='<?php echo esc_url(add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url())); ?>'"><?php esc_html_e('Buy Now', 'your-text-domain'); ?></button>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <button
                                                            type="submit"
                                                            name="add-to-cart"
                                                            value="<?php echo esc_attr($product->get_id()); ?>"
                                                            class="single_add_to_cart_button btn dark-btn"><?php echo esc_html($product->single_add_to_cart_text()); ?></button>

                                                        <!-- Buy Now -->
                                                        <button
                                                            type="button"
                                                            class="buy-now-button btn secondary-btn"
                                                            onclick="window.location.href='<?php echo esc_url(add_query_arg('add-to-cart', $product->get_id(), wc_get_checkout_url())); ?>'"><?php esc_html_e('Buy Now', 'your-text-domain'); ?></button>
                                                <?php
                                                    }
                                                } else {
                                                    // Out of stock → disabled button
                                                    echo '<button class="btn dark-btn" disabled><span>'
                                                        . esc_html__('Out of Stock', 'woocommerce')
                                                        . '</span></button>';
                                                }
                                                ?>
                                            </div>
                                        </form>

                                    <?php endif; // end variable vs simple 
                                    ?>

                                </div><!-- /.wrapper -->

                                <div class="payment-shipping-block">
                                    <div class="accordion" id="accordionShipping">
                                        <div class="accordion-item">
                                            <div class="shipping">
                                                <h2 class="accordion-header" id="headingOneShipping">
                                                    <button class="accordion-button" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseOneShipping" aria-expanded="true"
                                                        aria-controls="collapseOneShipping">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/shipping.png" alt="shipping icon">
                                                        Shipping & Return
                                                    </button>
                                                </h2>
                                                <div id="collapseOneShipping"
                                                    class="accordion-collapse collapse show"
                                                    aria-labelledby="headingOneShipping"
                                                    data-bs-parent="#accordionShipping">
                                                    <div class="accordion-body">
                                                        <div class="each-ship-detail dark-check">
                                                            <p>
                                                                Delivery Time
                                                            </p>
                                                            <p>
                                                                3-4 Working Day’s
                                                            </p>
                                                        </div>
                                                        <div class="each-ship-detail dark-check">
                                                            <p>
                                                                Estimated Arrive
                                                            </p>
                                                            <p>
                                                                <?php echo date('j F', strtotime('+4 days')); ?>
                                                            </p>
                                                        </div>
                                                        <!-- <div class="each-ship-detail dark-check">
                                                            <p>
                                                                Return
                                                            </p>
                                                            <p>
                                                                30 days of delivery.
                                                            </p>
                                                        </div> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <div class="payment">
                                                <h2 class="accordion-header" id="headingTwoPayment">
                                                    <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseTwoPayment" aria-expanded="false"
                                                        aria-controls="collapseTwoPayment">
                                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/payment.png" alt="shipping icon">
                                                        Payment Options
                                                    </button>
                                                </h2>
                                                <div id="collapseTwoPayment" class="accordion-collapse collapse"
                                                    aria-labelledby="headingTwoPayment"
                                                    data-bs-parent="#accordionpayment">
                                                    <div class="accordion-body">
                                                        <!-- <div class="each-ship-detail dark-check">
                                                            <p>
                                                                Buy-now-pay-later
                                                            </p>
                                                        </div> -->
                                                        <div class="each-ship-detail dark-check">
                                                            <p>
                                                                Payment Methods
                                                            </p>
                                                        </div>
                                                        <div class="payment-cards">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/paypal.png" alt="paypal">
                                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/visa.png" alt="paypal">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php if (have_rows('product_details_tab')) : ?>

                    <div class="tab-main-shop-fea">
                        <!-- Responsive Tabs / Dropdown -->
                        <div class="d-block d-md-none mb-3">
                            <select class="form-select" id="tabDropdown">

                                <?php $count = 1; ?>
                                <?php if (have_rows('product_details_tab')) : ?>
                                    <?php while (have_rows('product_details_tab')) : the_row(); ?>
                                        <option value="nav-tab-<?php echo $count; ?>" selected><?php the_sub_field('title'); ?></option>
                                        <?php $count = $count + 1; ?>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <?php // No rows found 
                                    ?>
                                <?php endif; ?>

                            </select>
                        </div>

                        <nav class="shop-tab-sticky">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">

                                <?php $count = 1; ?>
                                <?php if (have_rows('product_details_tab')) : ?>
                                    <?php while (have_rows('product_details_tab')) : the_row(); ?>
                                        <button class="nav-link  <?php if ($count == 1) {
                                                                        echo 'active';
                                                                    } ?>" data-bs-toggle="tab" data-bs-target="#nav-tab-<?php echo $count; ?>" type="button" role="tab" aria-controls="nav-tab-<?php echo $count; ?>" aria-selected="true"><?php the_sub_field('title'); ?></button>
                                        <?php $count = $count + 1; ?>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <?php // No rows found 
                                    ?>
                                <?php endif; ?>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <?php $count = 1; ?>
                            <?php if (have_rows('product_details_tab')) : ?>
                                <?php while (have_rows('product_details_tab')) : the_row(); ?>
                                    <div class="tab-pane fade <?php if ($count == 1) {
                                                                    echo 'show active';
                                                                } ?>" id="nav-tab-<?php echo $count; ?>" role="tabpanel" aria-labelledby="nav-tab-<?php echo $count; ?>">
                                        <div class="details-box">
                                            <?php the_sub_field('content'); ?>
                                        </div>
                                    </div>
                                    <?php $count = $count + 1; ?>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <?php // No rows found 
                                ?>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>




            </div>
        </div>
    </section>
    <?php if (get_field('video') != "") { ?>
        <section class="video-wrapper">
            <div class="container">
                <div class="global-title">
                    <h2><?php the_field('video_title'); ?> </h2>
                </div>
                <div class="video">
                    <video autoplay loop controls muted playsinline poster="<?php echo get_template_directory_uri(); ?>/assets/detailspag.jpeg"
                        style="width: 100%; height: auto;">
                        <source
                            src="<?php the_field('video'); ?>"
                            type="video/mp4">
                    </video>
                    <?php the_field('video_content'); ?>
                </div>
            </div>
        </section>
    <?php } ?>


    <?php
    defined('ABSPATH') || exit;
    global $product;

    // get up to 10 related product IDs (you can change the limit)
    $related_ids = wc_get_related_products($product->get_id(), 10);

    if ($related_ids) : ?>
        <section class="white-radius-bg">
            <div class="wrapper product ">
                <div class="container">
                    <div class="global-title">
                        <h2>Related Products</h2>
                    </div>
                    <div class="product-slider-wrapper">
                        <?php
                        foreach ($related_ids as $related_id) :
                           
                            $rel_prod   = wc_get_product($related_id);
                             $product_type = get_field( 'product_type', $related_id );
                             $badge_class = ( 'Prescriptive' === $product_type )
                ? 'badge-green-bg'
                : 'badge-purple-bg';
                            $image_id   = $rel_prod->get_image_id();
                            $image_url  = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : wc_placeholder_img_src();
                            // get first category name
                            $cats = wp_get_post_terms($related_id, 'product_cat');
                            $cat_name = (! empty($cats) && ! is_wp_error($cats)) ? $cats[0]->name : '';
                            // stock qty
                            $stock_qty = $rel_prod->is_in_stock() ? $rel_prod->get_stock_quantity() : 0;
                            // short description for tagline
                            $tagline = wp_strip_all_tags($rel_prod->get_short_description());
                            // price
                            $price   = $rel_prod->get_price();
                        ?>
                            <div class="product-slide" data-prescription="<?php echo esc_attr( strtolower($product_type) ); ?>">
                                <div class="product-image">
                                    <a href="<?php echo esc_url(get_permalink($rel_prod->get_id())); ?>"> <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($rel_prod->get_name()); ?>"></a>
                                    <?php 
                                        if($product_type != 'Normal' && $product_type != '' ){
                                        ?>
                                        <div class="product-badge <?php echo esc_attr( $badge_class ); ?>">
                                        <span><?php echo esc_html( $product_type ); ?></span>
                                    </div>
                                        <?php
                                        }
                                    ?>
                                </div>
                                <div class="product-detail">
                                    <h5><?php echo esc_html($rel_prod->get_name()); ?></h5>

                                    <p><?php echo esc_html($tagline); ?></p>
                                    <div class="bottom-detail">
                                        <p>
                                            <?php echo $rel_prod->is_in_stock() ? esc_html__('In Stock', 'your-text-domain') : esc_html__('Out of Stock', 'your-text-domain'); ?>
                                            <span>(<?php echo absint($stock_qty); ?>)</span>
                                        </p>
                                        <h6><?php echo esc_html__('From', 'your-text-domain') . ' ' . wc_price($price); ?></h6>
                                    </div>
                                    <?php if ($rel_prod->is_in_stock()) : ?>
                                        <a class="btn dark-btn"
                                            href="<?php echo esc_url(get_permalink($rel_prod->get_id())); ?>">
                                            <span><?php esc_html_e('Buy Now', 'your-text-domain'); ?></span>
                                    </a>
                                    <?php else : ?>
                                        <button class="btn dark-btn disabled" disabled>
                                            <span><?php esc_html_e('Out of Stock', 'your-text-domain'); ?></span>
                                        </button>
                                    <?php endif; ?>

                                </div>





                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    global $product;

    // … your existing single‐product template …

    // **Insert this where you want the reviews section to appear**:
    if (comments_open() || $product->get_review_count()) {
        wc_get_template_part('single-product', 'reviews');
    }
    ?>




    <?php include get_template_directory() . '/template-part/faq-section.php'; ?>
</div>

<script src="<?php echo get_template_directory_uri(); ?>/dist/js/custom.min.js"></script>

<?php get_footer(); ?>


<?php if ($product->is_type(array('variable', 'variable-subscription'))) : ?>
    <script>
       jQuery(function($){
    var $form = $('.variations_form');

    // 1) On load: for each attribute group, if nothing is selected, pick the first
    $form.each(function(){
        var $f = $(this);
        $f.find('.variation-buttons').each(function(){
            var $grp = $(this),
                $sel = $grp.find('.variation-button.selected'),
                $first = $grp.find('.variation-button').first();
            if ( !$sel.length && $first.length ) {
                selectButton( $first, $f );
            }
        });
    });

    // 2) Delegate click: toggle or switch selection
    $(document).on('click', '.variation-button', function(e){
        e.preventDefault();
        var $btn  = $(this),
            $grp  = $btn.closest('.variation-buttons'),
            $f    = $btn.closest('form.variations_form'),
            attr  = $btn.data('attr'),
            value = $btn.data('value');

        if ( $btn.hasClass('selected') ) {
            // deselect: clear this attribute
            $grp.find('.variation-button').removeClass('selected');
            $f.find('select[name="attribute_' + attr + '"]')
              .val('')
              .trigger('change');
        } else {
            // switch to new value
            $grp.find('.variation-button').removeClass('selected');
            selectButton( $btn, $f );
        }
    });

    // Helper to apply selection
    function selectButton( $btn, $form ) {
        var attr  = $btn.data('attr'),
            value = $btn.data('value');
        $btn.addClass('selected');
        $form.find('select[name="attribute_' + attr + '"]')
             .val( value )
             .trigger('change');
    }
    
});
    </script>

<script>
jQuery(function($){
    // 1) Variable‐subscription products: look for subscription data in the variations JSON
    $('.variations_form').each(function(){
        var variations = $(this).data('product_variations') || [],
            isSub      = false;
        $.each(variations, function(i, v){
            console.log(v);
            if ( v.attributes.attribute_quantity == "") { // WC Subscriptions adds this key
                isSub = true;
                return false;
            }
        });
        if ( isSub ) {
            $('.buy-now-button').remove();
        }
    });

    // 2) Simple subscription products: Subscriptions plugin injects hidden inputs
    if ( $('form.cart input[name="subscription_period_interval"]').length ) {
        $('.buy-now-button').remove();
    }
});
</script>

<?php endif; ?>
