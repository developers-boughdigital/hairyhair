<?php get_header();?>
    <div class="main-page product-detail-page">

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="container">
                    <?php 
                    
                    if ( function_exists( 'woocommerce_breadcrumb' ) ) {
                        woocommerce_breadcrumb();
                      }
                                          ?>

<?php
defined( 'ABSPATH' ) || exit;
global $product;
?>

<div class="product-details">
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="product-detail-slider">
                <div class="main-slider bottom-arrows">
                    <?php
                    // --- Main gallery (or fallback to the featured image) ---
                    $attachment_ids = $product->get_gallery_image_ids();
                    if ( $attachment_ids ) {
                        foreach ( $attachment_ids as $attachment_id ) {
                            $url = wp_get_attachment_image_url( $attachment_id, 'full' );
                            $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                            echo '<div><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"></div>';
                        }
                    } else {
                        $url = wp_get_attachment_image_url( $product->get_image_id(), 'full' );
                        $alt = get_post_meta( $product->get_image_id(), '_wp_attachment_image_alt', true );
                        echo '<div><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '"></div>';
                    }
                    ?>
                </div>

                <div class="thumb-slider">
                    <?php
                    // --- Thumbnails ---
                    if ( $attachment_ids ) {
                        foreach ( $attachment_ids as $attachment_id ) {
                            $thumb = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
                            $alt   = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                            echo '<div><img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( $alt ) . '"></div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="details-wrapper">
                <div class="all-details">
                    <h1><?php the_title(); ?></h1>

                    <div class="rating">
                        <div class="star-rating">
                            <?php 
                            // WooCommerce star HTML + numeric rating
                            echo wc_get_rating_html( $product->get_average_rating() );
                            ?>
                            <p>
                                <?php echo number_format_i18n( $product->get_average_rating(), 1 ); ?>
                                <span>(
                                    <?php echo $product->get_review_count(); ?> Review<?php echo $product->get_review_count() > 1 ? 's' : ''; ?>
                                )</span>
                            </p>
                        </div>
                        <div class="stock">
                            <span>
                                <?php 
                                if ( $product->is_in_stock() ) {
                                    echo $product->get_stock_quantity() . ' Available In Stock';
                                } else {
                                    echo 'Out of Stock';
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="price">
                        <span><?php echo wc_price( $product->get_regular_price() ); ?></span>
                        <?php if ( $product->is_on_sale() ) : ?>
                            <h6><?php echo wc_price( $product->get_sale_price() ); ?></h6>
                            <div class="offer">
                                <p>
                                    Save 
                                    <?php 
                                    $save = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
                                    echo $save; 
                                    ?>%
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-desc">
                    <div class="grey-check-list">
                        <ul>
                            <?php
                            // turn each line of the short description into a <li>
                            $short = wp_strip_all_tags( $product->get_short_description() );
                            $lines = preg_split( '/\r\n|\r|\n/', $short );
                            foreach ( $lines as $line ) {
                                if ( trim( $line ) ) {
                                    echo '<li>' . esc_html( trim( $line ) ) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>

                    <div class="best-service">
                        <?php
                        // Example: if you have ACF fields or custom meta for these promises, pull them here.
                        // For now we'll echo the same HTML but you could replace the src + text with dynamic calls.
                        ?>
                        <div class="each-service d-flex flex-wrap">
                            <div class="service-image">
                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/promise-1.png' ); ?>" alt="promise-1">
                            </div>
                            <p>No Harsh Chemicals</p>
                        </div>
                        <div class="each-service d-flex flex-wrap">
                            <div class="service-image">
                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/promise-2.png' ); ?>" alt="promise-2">
                            </div>
                            <p>Eco-Friendly Packaging</p>
                        </div>
                        <div class="each-service d-flex flex-wrap">
                            <div class="service-image">
                                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/promise-3.png' ); ?>" alt="promise-3">
                            </div>
                            <p>Backed by Science</p>
                        </div>
                    </div>
                </div>

                <div class="size-quantity">
                    <div class="wrapper">
                        <div class="size-selector   ">
                            <label>Size</label>
                            <div class="size-options">
                                <?php
                                // If this is a variable product, list its "size" attribute terms:
                                if ( $product->is_type( 'variable' ) ) {
                                    $available_variations = $product->get_available_variations();
                                    $seen                = [];
                                    foreach ( $available_variations as $variation ) {
                                        $size = $variation['attributes']['attribute_pa_size'] ?? false;
                                        if ( $size && ! in_array( $size, $seen, true ) ) {
                                            $seen[] = $size;
                                            echo '<button class="size-btn">' . esc_html( strtoupper( $size ) ) . '</button>';
                                        }
                                    }
                                } else {
                                    // simple product: just show one size
                                    echo '<button class="size-btn active">' . esc_html( $product->get_attribute( 'pa_size' ) ) . '</button>';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="quantity-price-wrapper">
                            <div class="quantity-selector">
                                <label>Quantity</label>
                                <div class="quantity-control">
                                <div class="quantity-selector">
                                    <?php
                                    // WooCommerce quantity input
                                    woocommerce_quantity_input( [
                                        'input_value' => 1, // Starting value
                                        'classes'     => [ 'qty-btn','qty-value', ], // for the minus/plus?
                                    ] );
                                    ?>
                                </div>
                            </div>

                            <div class="price-display">
                                <span class="price"><?php echo $product->get_price_html(); ?></span><span class="each">/Each</span>
                            </div>
                        </div>

                        <div class="button-wrapper">
                            <?php
                            // Add to Cart & Buy Now buttons
                            echo apply_filters( 'woocommerce_loop_add_to_cart_link',
                                sprintf(
                                    '<button class="btn dark-btn" data-quantity="1" data-product_id="%s" data-product_sku="%s">%s</button>',
                                    esc_attr( $product->get_id() ),
                                    esc_attr( $product->get_sku() ),
                                    esc_html( 'Buy Now' )
                                ),
                            $product );
                            ?>
                            <button class="btn secondary-btn" onclick="window.location='<?php echo esc_url( wc_get_checkout_url() ); ?>?add-to-cart=<?php echo esc_attr( $product->get_id() ); ?>'">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>

                <div class="payment-shipping-block">
                    <!-- shipping & payment accordions remain static or pull from options -->
                    <?php woocommerce_template_single_sharing(); /* if you want additional hooks */ ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-main-shop-fea">
    <!-- tabs: you can either hard-code the panels as you have them… -->
    <?php
    // Or dynamically pull WooCommerce tabs:
    $tabs = apply_filters( 'woocommerce_product_tabs', [] );
    ?>
    <!-- keep your HTML exactly the same; inside each pane, echo the tab content: -->
    <div class="tab-content" id="nav-tabContent">
        <?php foreach ( $tabs as $key => $tab ) : ?>
            <div class="tab-pane fade <?php echo $key === array_key_first( $tabs ) ? 'active show' : ''; ?>"
                 id="<?php echo esc_attr( "nav-{$key}-tab" ); ?>"
                 role="tabpanel">
                <?php call_user_func( $tab['callback'], $key, $tab ); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>







                </div>
            </div>
        </section>

        <section class="video-wrapper">
            <div class="container">
                <div class="global-title">
                    <h2>Hairy Hair Super Shampoo and Conditioner </h2>
                </div>

                <div class="video">
                    <video autoplay loop controls muted playsinline poster="assets/detailspag.jpeg"
                        style="width: 100%; height: auto;">
                        <source
                            src="https://endtest-videos.s3-us-west-2.amazonaws.com/documentation/endtest_data_driven_testing_csv.mp4"
                            type="video/mp4">
                    </video>
                    <p>This shampoo and conditioner combo transformed my dry, brittle hair into soft, silky strands! The
                        hydration lasts all day without making my scalp greasy. Definitely a game-changer!</p>
                    <p>This shampoo and conditioner combo transformed my dry, brittle hair into soft, silky strands! The
                        hydration lasts all day without making my scalp greasy. Definitely a game-changer!</p>
                </div>
            </div>
        </section>

        <?php
defined( 'ABSPATH' ) || exit;
global $product;

// get up to 10 related product IDs (you can change the limit)
$related_ids = wc_get_related_products( $product->get_id(), 10 );

if ( $related_ids ) : ?>
    <section class="white-radius-bg">
        <div class="wrapper product">
            <div class="container">
                <div class="global-title">
                    <h2><?php esc_html_e( 'Related Products', 'your-text-domain' ); ?></h2>
                </div>
                <div class="product-slider-wrapper">
                    <?php
                    foreach ( $related_ids as $related_id ) :
                        $rel_prod   = wc_get_product( $related_id );
                        $image_id   = $rel_prod->get_image_id();
                        $image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : wc_placeholder_img_src();
                        // get first category name
                        $cats = wp_get_post_terms( $related_id, 'product_cat' );
                        $cat_name = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
                        // stock qty
                        $stock_qty = $rel_prod->is_in_stock() ? $rel_prod->get_stock_quantity() : 0;
                        // short description for tagline
                        $tagline = wp_strip_all_tags( $rel_prod->get_short_description() );
                        // price
                        $price   = $rel_prod->get_price();
                    ?>
                        <div class="product-slide">
                            <div class="product-image">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $rel_prod->get_name() ); ?>">
                            </div>
                            <div class="product-detail">
                            <h5><?php echo esc_html( $rel_prod->get_name() ); ?></h5>

                                <p><?php echo esc_html( $tagline ); ?></p>
                                <div class="bottom-detail">
                                    <p>
                                        <?php echo $rel_prod->is_in_stock() ? esc_html__( 'In Stock', 'your-text-domain' ) : esc_html__( 'Out of Stock', 'your-text-domain' ); ?>
                                        <span>(<?php echo absint( $stock_qty ); ?>)</span>
                                    </p>
                                    <h6><?php echo esc_html__( 'From', 'your-text-domain' ) . ' ' . wc_price( $price ); ?></h6>
                                </div>
                                <?php if ( $rel_prod->is_in_stock() ) : ?>
                                    <button class="btn dark-btn"
                                            onclick="window.location.href='<?php echo esc_url( $rel_prod->add_to_cart_url() ); ?>'">
                                        <span><?php esc_html_e( 'Buy Now', 'your-text-domain' ); ?></span>
                                    </button>
                                <?php else : ?>
                                    <button class="btn dark-btn disabled" disabled>
                                        <span><?php esc_html_e( 'Out of Stock', 'your-text-domain' ); ?></span>
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
defined( 'ABSPATH' ) || exit;
global $product;

// 1) Figure out which sort the user wants:
$sort_options = [ 'Most Recent', 'Highest Rated', 'Lowest Rated' ];
$sort = isset( $_GET['sort'] ) && in_array( $_GET['sort'], $sort_options, true )
      ? $_GET['sort']
      : 'Most Recent';

// 2) Build our get_comments() args based on that:
$args = [
    'post_id' => $product->get_id(),
    'status'  => 'approve',
];
switch ( $sort ) {
    case 'Highest Rated':
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'rating';
        $args['order']    = 'DESC';
        break;
    case 'Lowest Rated':
        $args['orderby']  = 'meta_value_num';
        $args['meta_key'] = 'rating';
        $args['order']    = 'ASC';
        break;
    default: // Most Recent
        $args['orderby'] = 'comment_date';
        $args['order']   = 'DESC';
}
$comments = get_comments( $args );
?>

<section class="cust-reviews">
    <div class="container">
        <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h2>Customer Reviews</h2>
                <div class="rating-review">
                    <div class="star-icon">
                        <img src="assets/rating-star.png" alt="rating-star">
                        <img src="assets/rating-star.png" alt="rating-star">
                        <img src="assets/rating-star.png" alt="rating-star">
                        <img src="assets/rating-star.png" alt="rating-star">
                        <img src="assets/rating-star.png" alt="rating-star">
                    </div>
                    <p>
                        <?php 
                        // display overall average + count
                        echo number_format_i18n( $product->get_average_rating(), 1 );
                        ?> 
                        <span>(<?php echo $product->get_review_count(); ?> Review)</span>
                    </p>
                </div>
            </div>
            <button id="write-review-btn" class="btn dark-btn"><span>Write A Review</span></button>
        </div>

        <div class="gallary-wrapper">
        <div class="d-flex flex-wrap justify-content-between gallery-title">
                        <h6>Image & Video Gallery</h6>
                        <a href="#">
                            see all
                        </a>
                    </div>
                    <div class="gallry-slider">
                        <div><img src="assets/gallery-1.png" alt="Thumb 1"></div>
                        <div><img src="assets/gallery-2.png" alt="Thumb 2"></div>
                        <div><img src="assets/gallery-3.png" alt="Thumb 3"></div>
                        <div><img src="assets/gallery-4.png" alt="Thumb 4"></div>
                        <div><img src="assets/gallery-5.png" alt="Thumb 5"></div>
                        <div><img src="assets/gallery-6.png" alt="Thumb 1"></div>
                        <div><img src="assets/gallery-7.png" alt="Thumb 2"></div>
                        <div><img src="assets/gallery-8.png" alt="Thumb 3"></div>
                        <div><img src="assets/gallery-1.png" alt="Thumb 4"></div>
                        <div><img src="assets/gallery-2.png" alt="Thumb 5"></div>
                        <div><img src="assets/gallery-3.png" alt="Thumb 3"></div>
                        <div><img src="assets/gallery-4.png" alt="Thumb 4"></div>
                    </div>
        </div>
        <?php
defined( 'ABSPATH' ) || exit;
global $product;

// 1) Enqueue media scripts so `media_handle_upload()` works
if ( ! function_exists( 'wp_enqueue_media' ) ) {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
}
wp_enqueue_media();

// 2) Handle file uploads when a review is submitted
add_action( 'comment_post', function( $comment_id ) {
    if ( ! empty( $_FILES['review_media'] ) ) {
        $files = $_FILES['review_media'];
        $attachment_ids = [];
        foreach ( $files['name'] as $i => $filename ) {
            if ( $files['error'][ $i ] === UPLOAD_ERR_OK ) {
                $file       = [
                    'name'     => $files['name'][ $i ],
                    'type'     => $files['type'][ $i ],
                    'tmp_name' => $files['tmp_name'][ $i ],
                    'error'    => $files['error'][ $i ],
                    'size'     => $files['size'][ $i ],
                ];
                $_FILE_SAVE = media_handle_sideload( $file, 0 );
                if ( ! is_wp_error( $_FILE_SAVE ) ) {
                    $attachment_ids[] = $_FILE_SAVE;
                }
            }
        }
        if ( $attachment_ids ) {
            add_comment_meta( $comment_id, 'review_media', $attachment_ids );
        }
    }
}, 20, 1 );
?>
        <!-- UPDATED: review-form wrapper -->
        <div id="review_form_wrapper" style="margin:1.5rem 0;">
            <form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post"
                  enctype="multipart/form-data" id="commentform">
                <?php comment_id_fields( $product->get_id() ); ?>
                <!-- Star Rating Field -->
                <p class="comment-form-rating">
                    <label for="rating">
                        <?php esc_html_e( 'Your Rating', 'woocommerce' ); ?> 
                        <span class="required">*</span>
                    </label>
                    <select name="rating" id="rating" required>
                        <option value=""><?php esc_html_e( 'Rate&hellip;', 'woocommerce' ); ?></option>
                        <option value="5"><?php esc_html_e( 'Perfect', 'woocommerce' ); ?></option>
                        <option value="4"><?php esc_html_e( 'Good', 'woocommerce' ); ?></option>
                        <option value="3"><?php esc_html_e( 'Average', 'woocommerce' ); ?></option>
                        <option value="2"><?php esc_html_e( 'Not that bad', 'woocommerce' ); ?></option>
                        <option value="1"><?php esc_html_e( 'Very Poor', 'woocommerce' ); ?></option>
                    </select>
                </p>

                <!-- Review Text -->
                <p class="comment-form-comment">
                    <label for="comment"><?php esc_html_e( 'Your Review', 'woocommerce' ); ?> <span class="required">*</span></label>
                    <textarea id="comment" name="comment" cols="45" rows="8" required></textarea>
                </p>

                <!-- Media Upload -->
                <p class="comment-form-upload">
                    <label for="review_media">
                        <?php esc_html_e( 'Upload Images or Videos', 'your-text-domain' ); ?>
                    </label>
                    <input id="review_media" name="review_media[]" type="file"
                           accept="image/*,video/*" multiple />
                </p>

                <p class="form-submit">
                    <input name="submit" type="submit"
                           id="submit" class="submit btn dark-btn"
                           value="<?php esc_attr_e( 'Submit Review', 'woocommerce' ); ?>" />
                </p>
            </form>
        </div>

        <!-- UPDATED: Gallery now loops uploaded media -->
        <div class="gallary-wrapper">
            <div class="d-flex flex-wrap justify-content-between gallery-title">
                <h6>Image & Video Gallery</h6>
                <a href="#"><?php esc_html_e( 'see all', 'your-text-domain' ); ?></a>
            </div>
            <div class="gallry-slider">
                <?php
                // Collect all media attachments from approved reviews
                $comments = get_comments( [
                    'post_id' => $product->get_id(),
                    'status'  => 'approve',
                    'meta_key'=> 'review_media'
                ] );
                $seen = [];
                foreach ( $comments as $c ) {
                    $media = get_comment_meta( $c->comment_ID, 'review_media', true );
                    if ( is_array( $media ) ) {
                        foreach ( $media as $att_id ) {
                            if ( ! in_array( $att_id, $seen, true ) ) {
                                $seen[] = $att_id;
                                $url   = wp_get_attachment_url( $att_id );
                                $ext   = pathinfo( $url, PATHINFO_EXTENSION );
                                echo '<div>';
                                if ( in_array( strtolower( $ext ), [ 'mp4','webm','ogg' ] ) ) {
                                    echo '<video src="' . esc_url( $url ) . '" controls></video>';
                                } else {
                                    echo '<img src="' . esc_url( $url ) . '" alt="Review media">';
                                }
                                echo '</div>';
                            }
                        }
                    }
                }
                // fallback if no media yet
                if ( empty( $seen ) ) {
                    echo '<div><p>' . esc_html__( 'No review media yet.', 'your-text-domain' ) . '</p></div>';
                }
                ?>
            </div>
        </div>
        <div class="reviews-container">
            <div class="reviews-header">
                <label for="sort">Sort by</label>
                <select id="sort">
                    <?php foreach ( $sort_options as $opt ) : ?>
                        <option<?php selected( $sort, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if ( $comments ) : ?>
                <?php foreach ( $comments as $comment ) : 
                    $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
                ?>
                    <div class="review-card">
                        <?php if ( trim( $comment->comment_content ) && strpos( $comment->comment_content, "\n" ) === false ) : ?>
                            <!-- if it's a one-line “title” -->
                            <h3><?php echo esc_html( $comment->comment_content ); ?></h3>
                        <?php else : ?>
                            <p><?php echo wp_kses_post( nl2br( esc_html( $comment->comment_content ) ) ); ?></p>
                        <?php endif; ?>

                        <div class="d-flex flex-wrap justify-content-between">
                            <div class="user-info">
                                <img
                                  src="<?php echo esc_url( get_avatar_url( $comment->user_id, ['size'=>48] ) ); ?>"
                                  alt="<?php echo esc_attr( $comment->comment_author ); ?>" />
                                <div>
                                    <strong><?php echo esc_html( $comment->comment_author ); ?></strong>
                                    <div class="stars">
                                        <?php
                                        // ★ for each rating point, ☆ for the remainder
                                        echo str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <span class="date">
                                <?php echo date_i18n( 'd/m/Y', strtotime( $comment->comment_date ) ); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p><?php esc_html_e( 'No reviews yet.', 'your-text-domain' ); ?></p>
            <?php endif; ?>

            <div class="d-flex flex-wrap justify-content-center">
                <button class="load-more btn dark-btn">LOAD MORE</button>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        var btn     = document.getElementById('write-review-btn');
        var wrapper = document.getElementById('review_form_wrapper');
        if ( btn && wrapper ) {
            btn.addEventListener('click', function(e){
            e.preventDefault();
            // toggle visibility
            wrapper.style.display = (wrapper.style.display === 'none') ? 'block' : 'none';
            wrapper.scrollIntoView({ behavior: 'smooth' });
            });
        } else {
            console.warn('Write Review button or wrapper not found:', btn, wrapper);
        }
        });
document.getElementById('sort').addEventListener('change', function() {
    // reload the page with the chosen sort
    window.location.href = '<?php echo get_permalink(); ?>?sort=' + encodeURIComponent(this.value);
});
</script>


        <section class="faq-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="global-title">
                            <h2>Frequently Asked Questions</h2>
                            <p>We have put some commonly asked questions.</p>
                            <button class="btn secondary-btn">
                                <span>More FAQs</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-12">
                        <div class="all-faq">
                            <div class="container">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne" aria-expanded="true"
                                                aria-controls="collapseOne">
                                                How often should I use a hair growth shampoo?
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <p>Platelet-rich plasma (PRP) treatment involves drawing blood from the
                                                    patient, isolating the beneficial nutrients and injecting it into
                                                    the scalp where hair loss is occurring. This promotes hair growth
                                                    and has many other applications from encouraging healing to skin
                                                    rejuvenation.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseTwo"
                                                aria-expanded="false" aria-controls="collapseTwo">
                                                What type of treatment is PRP?
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <p>Platelet-rich plasma (PRP) treatment involves drawing blood from the
                                                    patient, isolating the beneficial nutrients and injecting it into
                                                    the scalp where hair loss is occurring. This promotes hair growth
                                                    and has many other applications from encouraging healing to skin
                                                    rejuvenation.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseThree"
                                                aria-expanded="false" aria-controls="collapseThree">
                                                What kind of hair loss can PRP treatment help with?
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <p>Platelet-rich plasma (PRP) treatment involves drawing blood from the
                                                    patient, isolating the beneficial nutrients and injecting it into
                                                    the scalp where hair loss is occurring. This promotes hair growth
                                                    and has many other applications from encouraging healing to skin
                                                    rejuvenation.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    
    <?php get_footer();?>