<?php /* Template Name: My Account Quiz */ ?>
<?php get_header(); ?>

<style>
    .quizSelectors {
        border: 1px solid #dee6ed;
        cursor: pointer;
        height: 46px;
    }
    .qs {
        border: 1px solid #dee6ed; 
        cursor: pointer; 
        height: 46px;
        cursor: no-drop;
    }

    .quizSelectors.active {
        border: 1px solid #3E7BD2;
        cursor: pointer;
        background: #DBF1FF;
    }

    .selectedMarker {
        height: 25px;
        width: 25px;
        border-radius: 50%;
        border: 1px solid #dee6ed;
        background-repeat: no-repeat;
        background-position: center center;
        background-image: url('<?php echo get_template_directory_uri(); ?>/assets/tick.svg');
    }

    .quizSelectors.active .selectedMarker {
        background-color: #3E7BD2;
    }

    .account-quiz-title h3 {
        font-size: 22px;
        line-height: 28px;
        margin-bottom: 12px;
    }
</style>


<div class="main-page contact-us">
    <section class="main-banner-content cms-main-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="ban-left-content cms-h1">
                        <nav aria-label="breadcrumb">

                            <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">

                                <?php if (function_exists('bcn_display')) {
                                    bcn_display();
                                } ?>

                            </div>
                        </nav>
                        <h1><?php the_title(); ?></h1>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="privacy-body-content" style="background: transparent !important;">
        <div class="container">

            <div class="woocommerce">
                <?php
                $user_id = get_current_user_id();

                if ($user_id) {
                ?>
                    <div class="rx-woocommerce-myaccount-navigation">
                        <?php do_action('woocommerce_account_navigation'); ?>
                    </div>
                <?php } ?>

                <div class="woocommerce-MyAccount-content" style="<?php if (!$user_id) echo 'width:100%'; ?>"> 
                
                    <div class="mb-5 text-center"> 
                        <p class="mb-2"><b>Your Personalised Hair Plan</b></p> 
                        <p class="mb-2">Based on your answers, here are the treatments we recommend to kickstart your hair journey. These options include both prescription and non-prescription products, carefully selected to match your needs. Prescription treatments are recommended by our doctors and custom-made by pharmacists—just for you.</p>
                        <p class="mb-2">Got questions? Call us anytime on <b>1300 800 546</b>.</p>
                    </div>

                    <div class="account-quiz-title">
                        <h3>Recommended Just for You</h3>
                    </div>

                    <div class="row">
                        <?php 

                        // Guests: only their own quiz (server-issued guest-access token), never an email cookie.
                        $guest_quiz = ( ! $user_id && function_exists( 'hh_guest_access_row' ) ) ? hh_guest_access_row( 'quiz' ) : null;

                        if ( $user_id || $guest_quiz ) {
                            global $wpdb;

                            // Newest submission (each quiz submission is kept as its own history row).
                            $recommended_products = $user_id
                                ? $wpdb->get_var( $wpdb->prepare( "SELECT recommended_products FROM {$wpdb->prefix}hh_quiz_results WHERE user_id = %d ORDER BY ID DESC LIMIT 1", $user_id ) )
                                : $guest_quiz['recommended_products'];

                            // echo json_encode($recommended_products); 
                            $recommended_products = true;$product_ids = [2482, 2242, 2249];

                            if ($recommended_products) {

                                // $product_ids = array_map('intval', explode(',', $recommended_products));

                                $args = [
                                    'post_type' => 'product',
                                    'post__in'  => $product_ids,
                                    'orderby'   => 'post__in',
                                    'posts_per_page' => -1,
                                ];

                                $products = new WP_Query($args);

                                if ($products->have_posts()) :
                                    while ($products->have_posts()) : $products->the_post();
                                        global $product;
                                        
                                         

                                    // thumbnail URL fallback
                                    $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'medium' ) : wc_placeholder_img_src();
                                    // stock and price
                                    $in_stock = $product->is_in_stock();
                                    $product_type = get_field( 'product_type', $products->get_id() );
                                    $badge_class = ( 'Prescriptive' === $product_type )
                                                    ? 'badge-green-bg'
                                                    : 'badge-purple-bg';
                                    $qty      = $product->managing_stock() ? $product->get_stock_quantity() : '';
                                    $price    = $product->get_price_html();
                                    // excerpt
                                    $excerpt  = wp_trim_words( get_the_excerpt(), 12, '…' );
                                    ?>
                                    <div class="col-lg-4 col-md-6 col-12" 
                                        data-price="<?php echo esc_attr( $product->get_price() ); ?>"
                                    data-rating="<?php echo esc_attr( round( $product->get_average_rating() ) ); ?>"
                                    data-prescription="<?php echo esc_attr( strtolower($product_type) ); ?>">
                                        
                                        <div class="product-slide">
                                            <div class="product-image">
                                                <?php //echo $product->get_id(); ?>
                                                <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                                    <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>">
                                                </a>
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
                                                <h5><a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php the_title(); ?></a></h5>
                                                <p><?php echo esc_html( $excerpt ); ?></p>
                                                <div class="bottom-detail">
                                                    <?php if ( $qty !== '' ) : ?>
                                                        <p>In Stock <span>(<?php echo intval( $qty ); ?>)</span></p>
                                                    <?php endif; ?>
                                                    <h6>From <?php echo $price; ?></h6>
                                                </div>

                                                <?php session_start(); $acess_to_add_prescriptive_products = $_SESSION['acess_to_add_prescriptive_products']; ?>
                                    
                                                <?php   
                                                    
                                                        if ( $product->is_in_stock() ) {
                                                            if($product_type == 'Prescriptive' && !$acess_to_add_prescriptive_products) {
                                                                ?>
                                                                <a class="btn dark-btn" href="<?php echo home_url(); ?>/hair-treatment-quiz" data-product-url="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                                                    <span>
                                                                        <?php echo esc_html__( QUIZ_BUTTON, 'woocommerce' ); ?>
                                                                    </span>
                                                                </a>
                                                                <?php
                                                            } else {
                                                                // In stock → link to the product page
                                                                printf(
                                                                    '<a href="%s" class="btn dark-btn" aria-label="%s"><span>%s</span></a>',
                                                                    esc_url( custom_add_to_cart_url( $product) ),
                                                                    esc_attr( sprintf( __( 'View “%s” details', 'woocommerce' ), $product->get_name() ) ),
                                                                    esc_html__( 'Buy Now', 'woocommerce' )
                                                                );
                                                            }
                                                        } else {
                                                            // Out of stock → disabled button
                                                            echo '<button class="btn dark-btn" disabled><span>'
                                                                . esc_html__( 'Out of Stock', 'woocommerce' )
                                                                . '</span></button>'; 
                                                        }  
                                                ?>
                                            </div>
                                        </div>
                                    </div>


                                    <?php
                                    endwhile;
                                    wp_reset_postdata();
                                endif;
                            } else {
                                echo '<div class="col-md-12">No recommended products available right now. Please take the quiz below to continue.</div>';
                            }
                        }


                        $comparision_type = 'default';


                        ?>


                    </div>




                    <!-- <div class="account-quiz-title mt-5">
                        <h3>Want to take quiz again?</h3>
                    </div>

                    <div class="row my-3">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center rounded p-2 quizSelectors active">
                                <p class="p-0 m-0">Let's Get Started</p>
                                <span class="selectedMarker"></span>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center rounded p-2  qs">
                                <p class="p-0 m-0">Take Hair Treatment Quiz</p>
                                <span class="selectedMarker"></span>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex justify-content-center align-items-center">
                            <div class="contact-btn">
                                <a class="btn secondary-btn w-auto btn-continue" href="<?php echo site_url('/hair-treatment-quiz'); ?>" style="min-width:auto">Continue</a>
                            </div>
                        </div>
                    </div> -->


                </div>
            </div>



        </div>
    </section>

    <section class="treatments-comparison comparison-page" style="background: transparent !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="hair-care-providers-wrap">
                        <div class="search-bar">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="search-field">
                                    <div class="icon">
                                        <span class="blue-dot<?php echo $i > 1 ? ' yellow-dot' : ''; ?><?php echo $i > 2 ? ' green-dot' : ''; ?><?php echo $i > 3 ? ' orange-dot' : ''; ?>"></span>
                                    </div>
                                    <div class="field-info">
                                        <h6>Treatment <?php echo chr(64 + $i); ?></h6>
                                        <select data-index="<?php echo $i; ?>" class="cat-dropdown">
                                            <option value="">Select Treatment</option>
                                            <?php
                                            $treatments = get_posts(array(
                                                'post_type'   => 'hair-treatments',
                                                'numberposts' => -1,
                                                'post_status' => 'publish',
                                            ));
                                            ?>
                                            <?php foreach ($treatments as $t): ?>
                                                <option value="<?php echo $t->ID; ?>"
                                                    <?php
                                                    if ($comparision_type === 'custom') {
                                                        $comparision_ids = [
                                                            1 => $products_data['ORALS']['comparision_id'],
                                                            2 => $products_data['TOPICALS']['comparision_id'],
                                                        ];

                                                        if (isset($comparision_ids[$i]) && $t->ID == $comparision_ids[$i]) {
                                                            echo "selected";
                                                        }
                                                    }
                                                    ?>>
                                                    <?php echo esc_html(get_the_title($t)); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if ($i < 4): ?><div class="divider"></div><?php endif; ?>
                            <?php endfor; ?>
                            <div class="contact-btn">
                                <button id="compare-btn" class="btn secondary-btn" <?php if ($comparision_type !== 'custom') echo 'disabled'; ?>>Compare</button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white compare-results-wrap p-4 rounded-4">
                        <div class="account-quiz-title">
                            <h3>Treatment Comparisons</h3>
                        </div>
                        <div id="compare-results"><?php if ($comparision_type === 'custom') echo "Loading..."; ?></div>

                        <?php if ($comparision_type == 'default') : ?>
                            <div class="default_comparision">
                                <?php
                                // 1. Fetch all hair‐treatments for the filters
                                $treatments = get_posts([
                                    'post_type'      => 'hair-treatments',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC',
                                ]);

                                // 2. Fetch all comparisons once
                                $all_comparisons = get_posts([
                                    'post_type'      => 'comparison',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC',
                                ]);

                                if ($treatments) : ?>
                                    <div class="compare-tab-list-wrap">
                                        <div class="comapre-head-title">
                                            <h6>Compare:</h6>
                                            <div class="all-tabs">
                                                <!-- “All” button -->
                                                <button class="filter-btn is-active" data-filter="all">All Items</button>

                                                <!-- dynamic treatment buttons -->
                                                <?php foreach ($treatments as $t) :
                                                    $slug  = sanitize_title($t->post_name);  // e.g. "prp", "laser-therapy"
                                                    $title = esc_html(get_the_title($t));
                                                ?>
                                                    <button
                                                        class="filter-btn"
                                                        data-filter="<?php echo esc_attr($slug); ?>">
                                                        <?php echo $title; ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="all-tabs-content">
                                            <div class="row">
                                                <?php foreach ($all_comparisons as $c) :
                                                    // get the two treatment IDs
                                                    $t1 = get_post_meta($c->ID, 'treatment_1', true);
                                                    $t2 = get_post_meta($c->ID, 'treatment_2', true);

                                                    // slugs for filtering
                                                    $slug1 = $t1 ? sanitize_title(get_post($t1)->post_name) : '';
                                                    $slug2 = $t2 ? sanitize_title(get_post($t2)->post_name) : '';

                                                    // thumbnails (or empty string)

                                                    $img1 = $t1 && has_post_thumbnail($t1)
                                                        ? get_the_post_thumbnail($t1, 'large', ['class' => 'img-fluid'])
                                                        : '';
                                                    $img2 = $t2 && has_post_thumbnail($t2)
                                                        ? get_the_post_thumbnail($t2, 'large', ['class' => 'img-fluid'])
                                                        : '';




                                                ?>
                                                    <div class="col-lg-6 col-md-6 col-sm-12 <?php echo esc_attr("$slug1 $slug2"); ?>">
                                                        <div class="main-compare-box">
                                                            <div class="two-compare-img">
                                                                <div class="all-compare-img">
                                                                    <div class="left-img">
                                                                        <?php echo $img1; ?>
                                                                        <div class="top-icon">
                                                                            <img
                                                                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/prp-icon.png'); ?>"
                                                                                alt="icon1">
                                                                        </div>
                                                                    </div>
                                                                    <div class="left-img">
                                                                        <?php echo $img2; ?>
                                                                        <div class="top-icon">
                                                                            <img
                                                                                src="<?php echo esc_url(get_template_directory_uri() . '/assets/prp-icon-2.png'); ?>"
                                                                                alt="icon2">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="vs-img">
                                                                    <img
                                                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/vs-img.png'); ?>"
                                                                        alt="vs">
                                                                </div>
                                                            </div>
                                                            <div class="compare-content">
                                                                <div class="left-title">
                                                                    <h6><?php echo esc_html(get_the_title($c)); ?></h6>
                                                                    <p><?php echo esc_html(get_the_excerpt($c)); ?></p>
                                                                </div>
                                                                <div class="btn-link">
                                                                    <a href="<?php echo esc_url(get_permalink($c)); ?>">Compare More</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
    </section>
</div>


<script src="<?php echo get_template_directory_uri(); ?>/dist/js/custom.min.js"></script>



<script>
    $('.quizSelectors').on('click', function() {
        $('.quizSelectors').removeClass('active');
        $(this).addClass('active');
        $('.btn-continue').attr('href', $('.quizSelectors.active').text().trim() === 'Take Hair Treatment Quiz' ? '<?php echo site_url('/hair-treatment-quiz'); ?>' : '<?php echo site_url('/lets-get-started'); ?>');
    });


    jQuery(document).ready(function($) {
        <?php if ($comparision_type === 'custom') : ?>

            function clickCompareBtnWithInterval(maxRetries = 10, intervalTime = 500) {
                let attempts = 0;

                const interval = setInterval(() => {
                    const btn = $('#compare-btn');

                    if (btn.length > 0) {
                        btn.click();
                        clearInterval(interval);
                    } else {
                        attempts++;
                        if (attempts >= maxRetries) {
                            console.warn('#compare-btn not found after multiple attempts');
                            clearInterval(interval);
                        }
                    }
                }, intervalTime);
            }

            clickCompareBtnWithInterval();
        <?php endif; ?>


        $('.btn-continue').attr('href', $('.quizSelectors.active').text().trim() === 'Take Hair Treatment Quiz' ? '<?php echo site_url('/hair-treatment-quiz'); ?>' : '<?php echo site_url('/lets-get-started'); ?>');
        if (window.location.pathname.indexOf('/my-account/quiz') !== -1) {
            $('.woocommerce-MyAccount-navigation-link--quiz').addClass('is-active');
        }
    });

    jQuery(function($) {

        $('#compare-btn').click(function() {
            $('.default_comparision').hide();
            $('#compare-results').text('Loading ...')
        });


        $('.filter-btn').on('click', function(e) {
            e.preventDefault();
            var filter = $(this).data('filter');

            // 1) mark the active button
            $('.filter-btn').removeClass('is-active');
            $(this).addClass('is-active');

            // 2) show/hide boxes
            $('.all-tabs-content .row > div[class*="col-"]').each(function() {
                var $box = $(this);
                if (filter === 'all' || $box.hasClass(filter)) {
                    $box.fadeIn(200);
                } else {
                    $box.hide();
                }
            });
        });

        // 3) on load, trigger the default filter
        $('.filter-btn.is-active').first().trigger('click');
    });
</script>
<?php
get_footer();
?>