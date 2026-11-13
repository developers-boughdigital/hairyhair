    <?php get_header();?>
<style>
    .component-features-split,.component-dual-gender-cards{
        display:none;
    }
</style>

    <div class="main-page">
        <section class="main-banner-content shop-main-banner mb-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="ban-left-content">
                            <nav aria-label="breadcrumb">

                                <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">

                                    <?php if (function_exists('bcn_display')) {
                                bcn_display();
                                } ?>

                                </div>
                            </nav>
                            <h1><?php the_field( 'banner_title', 47); ?></h1>
                            <?php the_field( 'banner_description', 47); ?>
                            <div class="contact-btn">

                                <?php $banner_button = get_field( 'banner_button', 47 ); ?>
                                <?php if ( $banner_button ) : ?>
                                <a class="btn secondary-btn" href="<?php echo esc_url( $banner_button['url'] ); ?>"
                                    target="<?php echo esc_attr( $banner_button['target'] ); ?>"><span><?php echo esc_html( $banner_button['title'] ); ?></span></a>
                                <?php endif; ?>
                            </div>
                            <!-- <div class="shop-client-img">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/review-img.png"
                                    alt="review-img" title="review-img">
                            </div> -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="ban-right-img">
                            <?php $banner_image = get_field( 'banner_image', 47 ); ?>
                            <?php if ( $banner_image ) : ?>
                            <img src="<?php echo esc_url( $banner_image['url'] ); ?>"
                                alt="<?php echo esc_attr( $banner_image['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="preferred-categories mb-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="global-title text-center">
                            <h2>Shop Our Top Categories</h2>
                        </div>
                        <div class="categories-main-wrap">
                            <?php
// Fetch all product categories (set hide_empty=>true if you only want categories with products)
$categories = get_terms( array(
    'taxonomy'   => 'product_cat',
    'number'     => 6,
     'parent'     => 0,    // only top-level
    'hide_empty' => false,
) );

if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
    foreach ( $categories as $cat ) {
        // Skip the Uncategorized bucket
        if ( 'uncategorized' === $cat->slug ) {
            continue;
        }

        // Term link
        $term_link = get_term_link( $cat );

        // Category thumbnail (WooCommerce stores ID in term meta 'thumbnail_id')
        $thumb_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
        $img_url  = $thumb_id
            ? wp_get_attachment_url( $thumb_id )
            : get_stylesheet_directory_uri() . '/assets/cat-img.png'; // fallback

        // Output your exact HTML
        ?>
                            <a class="categories-box" href="<?php echo esc_url( $term_link ); ?>">
                                <div class="cat-img">
                                    <img src="<?php echo esc_url( $img_url ); ?>"
                                        alt="<?php echo esc_attr( $cat->name ); ?>"
                                        title="<?php echo esc_attr( $cat->name ); ?>">
                                </div>
                                <div class="cat-content">
                                    <h6><?php echo esc_html( $cat->name ); ?></h6>
                                    <p><?php echo esc_html( $cat->description ); ?></p>
                                </div>
                            </a>
                            <?php
    }
} else {
    echo '<p>No categories found.</p>';
}
?>


                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container-fluid">
            <div class="bg-white radius-30">
                <?php
                /**
                 * ACF Flexible Content: shop_page_sections (Field Group on page ID 47)
                 *
                 * Supported layouts:
                 *   shop_section  →  template-part/components/layout-shop_section.php
                 *
                 * ACF field setup (add to page 47):
                 *   Field type : Flexible Content
                 *   Field name : shop_page_sections
                 *   Layout     : shop_section  (label: "Shop Section")
                 *     Sub-fields documented in layout-shop_section.php
                 */
                if ( have_rows( 'shop_page_sections', 47 ) ) :
                    while ( have_rows( 'shop_page_sections', 47 ) ) : the_row();
                        if ( get_row_layout() === 'shop_section' ) :
                            include locate_template( 'template-part/components/layout-shop_section.php' );
                        endif;
                    endwhile;
                endif;
                ?>
            </div>

            <?php 
                $in_content_10 = do_shortcode( '[hh_campaign_widget slot="In Content 10"]' ); 
                if($in_content_10 != '') {
                    echo "<div style='text-align: center; margin: 70px 0;'>".$in_content_10.'</div>';
                }
            ?>
        </div>

        <section class="shop-news">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="shop-inner-wrap">
                            <div class="hair-left-img">
                                <?php $shop_news_image = get_field( 'shop_news_image', 47 ); ?>
                                <?php if ( $shop_news_image ) : ?>
                                <img src="<?php echo esc_url( $shop_news_image['url'] ); ?>"
                                    alt="<?php echo esc_attr( $shop_news_image['alt'] ); ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="hair-right-content">
                                <?php the_field( 'shop_news_content', 47 ); ?>
                                <div class="contact-btn">
                                    <?php $shop_news_button = get_field( 'shop_news_button', 47 ); ?>
                                    <?php if ( $shop_news_button ) : ?>
                                    <a class="btn secondary-btn"
                                        href="<?php echo esc_url( $shop_news_button['url'] ); ?>"
                                        target="<?php echo esc_attr( $shop_news_button['target'] ); ?>"><span><?php echo esc_html( $shop_news_button['title'] ); ?></span></a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="promises shop-promises">
            <div class="container">
                <div class="bg-grey-3d">
                    <div class="global-title">
                        <h2><?php the_field( 'promises_title', 47 ); ?></h2>
                    </div>
                    <div class="all-promises">

                        <?php if ( have_rows( 'promises', 47 ) ) : ?>
                        <?php while ( have_rows( 'promises', 47 ) ) : the_row(); ?>
                        <div class="each-promise">
                            <?php $icon = get_sub_field( 'icon', 47 ); ?>
                            <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                            <?php endif; ?>
                            <p><?php the_sub_field( 'title', 47 ); ?></p>
                        </div>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>


                    </div>
                </div>
            </div>
        </section>

        <?php $specialist_image = get_field( 'specialist_image', 47 ); ?>
        <section class="specialist mb-0"
            style="background-image: url('<?php echo esc_url( $specialist_image['url'] ); ?>');">
            <div class="container">
                <div class="bg-white text-center">
                    <?php the_field( 'specialist_content', 47 ); ?>
                    <?php $specialist_button = get_field( 'specialist_button', 47 ); ?>
                    <?php if ( $specialist_button ) : ?>
                    <a class="btn secondary-btn" href="<?php echo esc_url( $specialist_button['url'] ); ?>"
                        target="<?php echo esc_attr( $specialist_button['target'] ); ?>"><?php echo esc_html( $specialist_button['title'] ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
        /*
         * Featured Products Section
         * Admin-editable: WP Admin > Pages > Shop > "Shop — Featured Products Section"
         * Reads stored meta directly — no ACF have_rows() dependency.
         * Task #1 order: Regrow & Boost, Regrow & Block Female Pill,
         *                Regrow & Boost Duo, Regrow Topical Spray, Gummy Pill.
         */
        $fp_title    = get_post_meta( 47, 'fp_section_0_fp_title', true );
        $fp_title    = $fp_title ? $fp_title : 'Featured Products';
        $fp_raw      = maybe_unserialize( get_post_meta( 47, 'fp_section_0_fp_products', true ) );
        $fp_ids      = ( is_array( $fp_raw ) && $fp_raw )
                        ? array_map( 'intval', $fp_raw )
                        : array( 2234, 2238, 13355, 2247, 2242 ); // Task #1 fallback

        $fp_products = wc_get_products( array(
            'include' => $fp_ids,
            'limit'   => 5,
            'orderby' => 'include',
            'status'  => 'publish',
        ) );

        if ( $fp_products ) :
        ?>
        <section class="shop-featured-products product">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="global-title">
                            <h2><?php echo esc_html( $fp_title ); ?></h2>
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
        <?php endif; ?>


        <section class="steps-to-fllow step-shop-follow">
            <div class="container">
                <div class="global-title text-center">
                    <h2>
                        <?php the_field( 'steps_to_flow_title', 47 ); ?>
                    </h2>
                </div>
            </div>
            <div class="step-animation">
                <div class="steps-container" id="stepsFlow">
                    <div class="timeline"></div>
                    <div class="arrow" id="flowArrow"></div>

                    <?php $count = 1; ?>
                    <?php if ( have_rows( 'steps_to_flow', 47 ) ) : ?>
                    <?php while ( have_rows( 'steps_to_flow', 47 ) ) : the_row(); ?>
                    <div class="step" data-step="<?php echo $count; ?>">
                        <div class="top-card">
                            <div class="step-icon">
                                <?php $icon = get_sub_field( 'icon', 47 ); ?>
                                <?php if ( $icon ) : ?>
                                <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                    alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                <?php endif; ?>
                            </div>
                            <h5><?php the_sub_field( 'title', 47 ); ?></h5>
                            <p>
                                <?php the_sub_field( 'description', 47 ); ?>
                            </p>
                        </div>
                        <div class="step-footer">
                            <h6>STEP 1</h6>
                            <p><?php the_sub_field( 'step_time', 47 ); ?></p>
                        </div>
                    </div>
                    <?php $count = $count + 1; ?>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <?php // No rows found ?>
                    <?php endif; ?>


                </div>


                <?php $steps_to_flow_button = get_field( 'steps_to_flow_button', 47 ); ?>
                <?php if ( $steps_to_flow_button ) : ?>
                <a class="btn secondary-btn m-auto d-flex justify-content-center"
                    href="<?php echo esc_url( $steps_to_flow_button['url'] ); ?>"
                    target="<?php echo esc_attr( $steps_to_flow_button['target'] ); ?>"><span><?php echo esc_html( $steps_to_flow_button['title'] ); ?></span></a>
                <?php endif; ?>
            </div>
        </section>


        <!-- the ultimate hair hub slider -->
        <section class="hair-care-resource">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Featured Blog Articles</h2>
                    <a class="btn secondary-btn" href="/resources">See All</a>
                </div>
                <div class="ultimate-hair-care">
                    <?php
                $args = array(
                'post_type' => 'post', // Replace with your CPT slug
                'posts_per_page' => 5     // Or change to however many posts you want
                );
                $query = new WP_Query($args);
                if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                ?>
                    <div class="product-slide">
                        <a href="<?php the_permalink(); ?>">
                            <div class="ultimate-main">
                                <div class="hub-img">
                                    <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', ['alt' => get_the_title(), 'title' => get_the_title()]); ?>
                                    <?php else : ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/hub-img.png"
                                        alt="hub-img" title="hub-img">
                                    <?php endif; ?>
                                </div>
                                <div class="hub-content">
                                    <h6><?php the_title(); ?></h6>
                                    <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                endif;
                ?>
                </div>
            </div>
        </section>



        <section class="faq-wrap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="global-title">
                            <?php the_field( 'faq_content', 47 ); ?>
                            <?php $faq_button = get_field( 'faq_button', 47 ); ?>
                            <?php if ( $faq_button ) : ?>
                            <a class="btn secondary-btn" href="<?php echo esc_url( $faq_button['url'] ); ?>"
                                target="<?php echo esc_attr( $faq_button['target'] ); ?>"><?php echo esc_html( $faq_button['title'] ); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-12">
                        <div class="all-faq">
                            <div class="container">
                                <div class="accordion" id="accordionExample">

                                    <?php $count = 1; ?>
                                    <?php if ( have_rows( 'faq', 47 ) ) : ?>
                                    <?php while ( have_rows( 'faq', 47 ) ) : the_row(); ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button <?php if($count != '1'){ echo 'collapsed'; } ?>"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?php echo $count; ?>" aria-expanded="true"
                                                aria-controls="collapseOne<?php echo $count; ?>">
                                                <?php the_sub_field( 'title', 47 ); ?>
                                            </button>
                                        </h2>
                                        <div id="collapseOne<?php echo $count; ?>"
                                            class="accordion-collapse collapse <?php if($count == '1'){ echo 'show'; } ?>"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <?php the_sub_field( 'content', 47 ); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $count = $count + 1; ?>
                                    <?php endwhile; ?>
                                    <?php else : ?>
                                    <?php // No rows found ?>
                                    <?php endif; ?>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script src="dist/js/custom.min.js"></script>
    <?php 
    get_footer();
    ?>