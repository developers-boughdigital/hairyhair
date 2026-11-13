<?php /* Template Name: Home */ ?>
<?php get_header(); ?>
<div class="main-page">
    <?php $hero_content = do_shortcode( '[hh_campaign_widget slot="Home Hero"]' ); ?>
    <section class="main-banner-content mb-0" <?php if ($hero_content != '') echo 'style="background-image: none !important;"'; ?>>
        <?php  
            if($hero_content != '') {
                echo "<div class='hero-banner-content-enabled'>".$hero_content."</div>";
            } else {
                ?>
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
                                <h1><?php the_field( 'banner_title' ); ?></h1>
                                <?php the_field( 'banner_description' ); ?>
                                <div class="contact-btn">
                                    
                                    <?php $banner_button = get_field( 'banner_button' ); ?>
                                    <?php if ( $banner_button ) : ?>
                                    <a class="btn secondary-btn" href="<?php echo esc_url( $banner_button['url'] ); ?>" target="<?php echo esc_attr( $banner_button['target'] ); ?>"><?php echo esc_html( $banner_button['title'] ); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                            <div class="ban-right-img">
                                <?php $banner_image = get_field( 'banner_image' ); ?>
                                <?php if ( $banner_image ) : ?>
                                <img src="<?php echo esc_url( $banner_image['url'] ); ?>" alt="<?php echo esc_attr( $banner_image['alt'] ); ?>" />
                                <?php endif; ?>
                                <div class="review-img">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/review-img.png" alt="review-img" title="review-img">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        ?>
    </section>

    <section class="about mb-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-desc">
                        <?php the_field( 'about_description' ); ?>
                        
                        <?php $about_button = get_field( 'about_button' ); ?>
                        <?php if ( $about_button ) : ?>
                        <a class="btn secondary-btn" href="<?php echo esc_url( $about_button['url'] ); ?>" target="<?php echo esc_attr( $about_button['target'] ); ?>"><?php echo esc_html( $about_button['title'] ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="about-img">
                        <?php $about_image_1 = get_field( 'about_image_1' ); ?>
                        <?php if ( $about_image_1 ) : ?>
                        <img src="<?php echo esc_url( $about_image_1['url'] ); ?>" alt="<?php echo esc_attr( $about_image_1['alt'] ); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="about-img">
                        <?php $about_image_2 = get_field( 'about_image_2' ); ?>
                        <?php if ( $about_image_2 ) : ?>
                        <img src="<?php echo esc_url( $about_image_2['url'] ); ?>" alt="<?php echo esc_attr( $about_image_2['alt'] ); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div id="counter">
                        <?php if ( have_rows( 'counter' ) ) : ?>
                        <?php while ( have_rows( 'counter' ) ) : the_row(); ?>
                        <div class="item">
                            <?php $icon = get_sub_field( 'icon' ); ?>
                            <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                            <?php endif; ?>
                            <h3 data-number="<?php echo get_sub_field( 'count' ); ?>"><span><?php the_sub_field( 'count_symbol' ); ?></span></h3>
                            <p><?php the_sub_field( 'count_title' ); ?></p>
                        </div>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container-fluid">
        <div class="bg-white radius-30">
            <section class="product pb-0">
                <div class="container">
                    <h2>
                    Hairy Hair Products
                    </h2>
                    <div class="product-slider-wrapper">
              <?php

// Get the products from ACF Relationship field
$acf_products = get_field('product');

if ( $acf_products && is_array($acf_products) ) :

    foreach ( $acf_products as $post ) :
        setup_postdata( $post );

        // Make sure $product is defined
        $products = wc_get_product( $post->ID );

        // Include the template part
        include locate_template('template-part/product-block.php');

    endforeach;

    wp_reset_postdata();

endif;
?>





                    </div>
                </div>
            </section>

            <?php 
                $in_content_1 = do_shortcode( '[hh_campaign_widget slot="In Content 1"]' ); 
                if($in_content_1 != '') {
                    echo "<div style='text-align: center; margin-bottom: 100px;'>".$in_content_1.'</div>';
                }
            ?> 

            <section class="services">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="left-side">
                                <h2>
                                <?php the_field( 'services_title' ); ?>
                                </h2>
                                
                                <?php $services_button = get_field( 'services_button' ); ?>
                                <?php if ( $services_button ) : ?>
                                <a class="btn secondary-btn" href="<?php echo esc_url( $services_button['url'] ); ?>" target="<?php echo esc_attr( $services_button['target'] ); ?>"><?php echo esc_html( $services_button['title'] ); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="right-side">
                                <?php if ( have_rows( 'services' ) ) : ?>
                                <?php while ( have_rows( 'services' ) ) : the_row(); ?>
                                <div class="each-service">
                                    <div class="service-img">
                                        <?php $icon = get_sub_field( 'icon' ); ?>
                                        <?php if ( $icon ) : ?>
                                        <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                        <?php endif; ?>
                                    </div>
                                    <h6><?php the_sub_field( 'title' ); ?></h6>
                                </div>
                                <?php endwhile; ?>
                                <?php else : ?>
                                <?php // No rows found ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="treatments">
                <div class="container">
                    <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                        <h2><?php the_field( 'treatments_title' ); ?></h2>
                        
                        <?php $treatments_button = get_field( 'treatments_button' ); ?>
                        <?php if ( $treatments_button ) : ?>
                        <a class="btn secondary-btn" href="<?php echo esc_url( $treatments_button['url'] ); ?>" target="<?php echo esc_attr( $treatments_button['target'] ); ?>"><?php echo esc_html( $treatments_button['title'] ); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <?php if ( have_rows( 'treatments' ) ) : ?>
                        <?php while ( have_rows( 'treatments' ) ) : the_row(); ?>
                        <?php $treatment = get_sub_field( 'treatment' ); ?>
                        
                        <?php $post = $treatment; ?>
                        <?php setup_postdata( $post ); ?>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="each-treatment bg-grey-ee">
                                <div class="each-treatment-img">
                                    <?php $icon = get_sub_field( 'icon' ); ?>
                                    <?php if ( $icon ) : ?>
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                    <?php endif; ?>
                                </div>
                                <h5>
                                <?php the_title(); ?>
                                </h5>
                                <p><?php the_sub_field( 'description' ); ?></p>
                                <div class="d-flex justify-content-end">
                                    
                                    <a class="btn dark-btn" href="<?php the_permalink(); ?>" >Learn More</a>
                                    
                                </div>
                            </div>
                        </div>
                        <?php wp_reset_postdata(); ?>
                        
                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <section class="treatments-effect">
                <div class="container">
                    <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                        <h2><?php the_field( 'treatments_results_title' ); ?></h2>
                        <?php $treatments_results_button = get_field( 'treatments_results_button' ); ?>
                        <?php if ( $treatments_results_button ) : ?>
                        <a href="<?php echo esc_url( $treatments_results_button['url'] ); ?>" target="<?php echo esc_attr( $treatments_results_button['target'] ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-down.png" alt="arrow"><?php echo esc_html( $treatments_results_button['title'] ); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="treatment-slider bottom-arrows">
                        <?php if ( have_rows( 'treatments_results' ) ) : ?>
                        <?php while ( have_rows( 'treatments_results' ) ) : the_row(); ?>
                        <div class="treatment-slide">
                            <?php $image = get_sub_field( 'image' ); ?>
                            <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div> 

    <section class="steps-to-fllow">
        <div class="container">
            <div class="global-title text-center">
                <h2>
                <?php the_field( 'steps_to_flow_title' ); ?>
                </h2>
            </div>
        </div>
        <div class="step-animation">
            <div class="steps-container" id="stepsFlow">
                <div class="timeline"></div>
                <div class="arrow" id="flowArrow"></div>
                <?php $count = 1; ?>
                <?php if ( have_rows( 'steps_to_flow' ) ) : ?>
                <?php while ( have_rows( 'steps_to_flow' ) ) : the_row(); ?>
                <div class="step" data-step="<?php echo $count; ?>">
                    <div class="top-card">
                        <div class="step-icon">
                            <?php $icon = get_sub_field( 'icon' ); ?>
                            <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                        <h5><?php the_sub_field( 'title' ); ?></h5>
                        <p>
                            <?php the_sub_field( 'description' ); ?>
                        </p>
                    </div>
                    <div class="step-footer">
                        <h6>STEP <?php echo $count; ?></h6>
                        <p><?php the_sub_field( 'step_time' ); ?></p>
                    </div>
                </div>
                <?php $count = $count + 1; ?>
                <?php endwhile; ?>
                <?php else : ?>
                <?php // No rows found ?>
                <?php endif; ?>
                
            </div>
            
            <?php $steps_to_flow_button = get_field( 'steps_to_flow_button' ); ?>
            <?php if ( $steps_to_flow_button ) : ?>
            <a class="btn secondary-btn m-auto d-flex justify-content-center" href="<?php echo esc_url( $steps_to_flow_button['url'] ); ?>" target="<?php echo esc_attr( $steps_to_flow_button['target'] ); ?>"><?php echo esc_html( $steps_to_flow_button['title'] ); ?></a>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="promises">
        <div class="container">
            <div class="bg-grey-3d">
                <div class="global-title">
                    <h2><?php the_field( 'promises_title' ); ?></h2>
                </div>
                <div class="all-promises">
                    <?php if ( have_rows( 'promises' ) ) : ?>
                    <?php while ( have_rows( 'promises' ) ) : the_row(); ?>
                    <div class="each-promise">
                        <?php $icon = get_sub_field( 'icon' ); ?>
                        <?php if ( $icon ) : ?>
                        <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                        <?php endif; ?>
                        <p><?php the_sub_field( 'title' ); ?></p>
                    </div>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <?php // No rows found ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- <section class="top-providers">
        <div class="container">
            <div class="global-title text-center">
                <h2>Top-Rated Providers</h2>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="provider-mian">
                        <div class="provider-img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-img.png" alt="provider-img" title="provider-img">
                        </div>
                        <div class="provider-bottom">
                            <div class="provider-client-details">
                                <div class="provider-client-img">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client" title="provider-client">
                                </div>
                                <div class="provider-name">
                                    <h6>Rocko Band in Marquee Club</h6>
                                    <ul>
                                        <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star" title="rating-star">5.0 <span>(100 Review)</span></li>
                                    </ul>
                                </div>
                                <div class="provider-distance">
                                    <span>10.3 km</span>
                                </div>
                            </div>
                            <div class="provider-text">
                                <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.</p>
                                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St, NY, USA</span>
                            </div>
                            <div class="profile-btn">
                                <a class="btn dark-btn">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="provider-mian">
                        <div class="provider-img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-img.png" alt="provider-img" title="provider-img">
                        </div>
                        <div class="provider-bottom">
                            <div class="provider-client-details">
                                <div class="provider-client-img">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client" title="provider-client">
                                </div>
                                <div class="provider-name">
                                    <h6>Rocko Band in Marquee Club</h6>
                                    <ul>
                                        <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star" title="rating-star">5.0 <span>(100 Review)</span></li>
                                    </ul>
                                </div>
                                <div class="provider-distance">
                                    <span>10.3 km</span>
                                </div>
                            </div>
                            <div class="provider-text">
                                <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.</p>
                                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St, NY, USA</span>
                            </div>
                            <div class="profile-btn">
                                <a class="btn dark-btn">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="provider-mian">
                        <div class="provider-img">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-img.png" alt="provider-img" title="provider-img">
                        </div>
                        <div class="provider-bottom">
                            <div class="provider-client-details">
                                <div class="provider-client-img">
                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client" title="provider-client">
                                </div>
                                <div class="provider-name">
                                    <h6>Rocko Band in Marquee Club</h6>
                                    <ul>
                                        <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star" title="rating-star">5.0 <span>(100 Review)</span></li>
                                    </ul>
                                </div>
                                <div class="provider-distance">
                                    <span>10.3 km</span>
                                </div>
                            </div>
                            <div class="provider-text">
                                <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.</p>
                                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St, NY, USA</span>
                            </div>
                            <div class="profile-btn">
                                <a class="btn dark-btn">View Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tag-list-main">
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Sydney</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Sydney</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Melbourne</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Melbourne</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Brisbane</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>Barbers in Brisbane</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>PRP Clinics near Brisbane</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>PRP Clinics near Sydney</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>PRP Clinics near Melbourne</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>PRP Clinics near Brisbane</span>
                </div>
                <div class="tag-list-content">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-up.png" alt="arrow-up" title="arrow-up">
                    <span>PRP Clinics near Sydney</span>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <button class="btn secondary-btn">See All Providers</button>
            </div>
        </div>
    </section> -->
    
    <!-- marketing section -->
    <section class="marketing-sec-main pt-0">
        <div class="container">
            <div class="marketing-wrap">
                <div class="marketing-left-content">
                    <?php the_field( 'marketing_content' ); ?>
                </div>
                <div class="marketing-right-img">
                    
                    <?php $marketing_image_desktop = get_field( 'marketing_image_desktop' ); ?>
                    <?php if ( $marketing_image_desktop ) : ?>
                    <img class="desk-show girl-img" src="<?php echo esc_url( $marketing_image_desktop['url'] ); ?>" alt="<?php echo esc_attr( $marketing_image_desktop['alt'] ); ?>" />
                    <?php endif; ?>
                    <?php $marketing_image_mobile = get_field( 'marketing_image_mobile' ); ?>
                    <?php if ( $marketing_image_mobile ) : ?>
                    <img class="mb-show girl-img" src="<?php echo esc_url( $marketing_image_mobile['url'] ); ?>" alt="<?php echo esc_attr( $marketing_image_mobile['alt'] ); ?>" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- loved-people section -->
    <section class="loved-by-people">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Loved by people </h2>
                <!-- <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-down.png" alt="arrow">See All</a> -->
            </div>
        </div>
        <div class="container">
            <div class="loved-by-slider bottom-arrows">
            <?php
                $testimonials = get_field('testimonial'); // IMPORTANT: plural now
                if ( $testimonials ) :
                foreach ( $testimonials as $post ) :
                setup_postdata( $post );
                ?>
                <div class="testi-slide">
                    <?php
                    $video = get_field('video');
                    if ( $video ) : ?>
                    <div class="testi-img">
                        <video width="100%" height="397" controls>
                            <source src="<?php echo esc_url( $video['url'] ); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <?php else : ?>
                    <div class="review-main-wrap">
                        <div class="testi-review">
                            <?php the_field('review'); ?>
                          
                        </div>
                        <?php endif; ?>
                        <div class="testi-client-info">
                            <div class="client-profile">
                                <?php
                                $profile_icon = get_field('profile_icon');
                                if ( $profile_icon ) : ?>
                                <img src="<?php echo esc_url( $profile_icon['url'] ); ?>" alt="<?php echo esc_attr( $profile_icon['alt'] ); ?>">
                                <?php endif; ?>
                            </div>
                            <div class="client-name">
                                <h6><?php the_title(); ?> - <span><?php the_field('location'); ?></span></h6>
                                <ul>
                                    <?php
                                    $stars = get_field('stars');
                                    if ( $stars ) :
                                    for ( $i = 0; $i < $stars; $i++ ) : ?>
                                             <li><?php echo hh_star_svg(); ?></li>
                                    <?php endfor;
                                    endif;
                                    ?>
                                </ul>
                            </div>
                            </div> <!-- testi-client-info -->
                            <?php if ( ! $video ) : ?>
                            </div> <!-- Close review-main-wrap if no video -->
                            <?php endif; ?>
                            </div> <!-- testi-slide -->
                            <?php
                            endforeach;
                            wp_reset_postdata();
                            endif;
                            ?>
            </div>
        </div>
    </section>
    <!-- the ultimate hair hub slider -->
    <section class="hair-care-resource">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>The Ultimate Hair Care Resource Hub</h2>
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
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/hub-img.png" alt="hub-img" title="hub-img">
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

    <?php 
        $in_content_2 = do_shortcode( '[hh_campaign_widget slot="In Content 2"]' ); 
 
        if($in_content_2 != '') {
            echo "<div style='text-align: center; margin-bottom: 100px;'>".$in_content_2.'</div>';
        }
    ?>

    <?php include 'template-part/faq-section.php'; ?>
    <!-- <?php include 'template-part/newsletter.php'; ?> -->
    
    
</div>
<?php get_footer(); ?>
