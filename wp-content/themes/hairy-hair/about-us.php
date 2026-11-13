<?php /* Template Name: About */ ?>
<?php get_header(); ?>

<style>
.page-template-about-us .member-img {
    width: 330px;
    height: 487px;
}

.page-template-about-us .member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>

<div class="main-page prp-treatment-detail">
    <section class="main-banner-content aboutus-main-ban">
        <div class="container-fluid">
            <div class="row align-items-start">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="ban-left-content">
                        <div class="content-wrapper">
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
                                <a class="btn secondary-btn" href="<?php echo esc_url( $banner_button['url'] ); ?>"
                                    target="<?php echo esc_attr( $banner_button['target'] ); ?>"><?php echo esc_html( $banner_button['title'] ); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="ban-right-img">
                        <?php $banner_image = get_field( 'banner_image' ); ?>
                        <?php if ( $banner_image ) : ?>
                        <img src="<?php echo esc_url( $banner_image['url'] ); ?>"
                            alt="<?php echo esc_attr( $banner_image['alt'] ); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="why-hairy-hair">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="about-list-img">
                        <div class="first-img">
                            <?php $about_image_1 = get_field( 'about_image_1' ); ?>
                            <?php if ( $about_image_1 ) : ?>
                            <img src="<?php echo esc_url( $about_image_1['url'] ); ?>"
                                alt="<?php echo esc_attr( $about_image_1['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="second-img">
                            <?php $about_image_2 = get_field( 'about_image_2' ); ?>
                            <?php if ( $about_image_2 ) : ?>
                            <img src="<?php echo esc_url( $about_image_2['url'] ); ?>"
                                alt="<?php echo esc_attr( $about_image_2['alt'] ); ?>" />
                            <?php endif; ?>
                            <?php $about_image_3 = get_field( 'about_image_3' ); ?>
                            <?php if ( $about_image_3 ) : ?>
                            <img src="<?php echo esc_url( $about_image_3['url'] ); ?>"
                                alt="<?php echo esc_attr( $about_image_3['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="why-right-content">
                        <?php the_field( 'about_content' ); ?>
                        <div class="contact-btn">
                            <?php $about_button = get_field( 'about_button' ); ?>
                            <?php if ( $about_button ) : ?>
                            <a class="btn secondary-btn" href="<?php echo esc_url( $about_button['url'] ); ?>"
                                target="<?php echo esc_attr( $about_button['target'] ); ?>"><?php echo esc_html( $about_button['title'] ); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="good-values">
                <h2><?php the_field( 'good_values_title' ); ?></h2>
                <div class="row">

                    <?php if ( have_rows( 'good_values' ) ) : ?>
                    <?php while ( have_rows( 'good_values' ) ) : the_row(); ?>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <div class="value-box-main">
                            <div class="icon">
                                <?php $icon = get_sub_field( 'icon' ); ?>
                                <?php if ( $icon ) : ?>
                                <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                    alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="content">
                                <h6><?php the_sub_field( 'title' ); ?></h6>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <?php // No rows found ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>


    <div class="bg-white radius-30">
        <section class="product">
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
        $product = wc_get_product( $post->ID );
        if ( ! $product ) { continue; }

        // Include the template part
        include locate_template('template-part/product-block.php');

    endforeach;

    wp_reset_postdata();

endif;
?>

                </div>
            </div>
            <div class="contact-btn text-center">
                <a class="btn secondary-btn" href="/shop" type="submit">Shop All Product</a>
            </div>
        </section>
    </div>


    <?php
    /* ── About Page: Features Split ─────────────────────────────────────────
     * ACF fields (field group: About Page — Features Split, template: about-us.php)
     *   fs_title        (text)
     *   fs_title_color  (color_picker)  default #122B51
     *   fs_bg_color     (color_picker)  default #ffffff
     *   fs_features     (repeater)
     *     └ icon    (image)
     *     └ title   (text)
     *     └ content (textarea)
     */
    $fs_title       = get_field( 'fs_title' );
    $fs_title_color = get_field( 'fs_title_color' ) ?: '#122B51';
    $fs_bg_color    = get_field( 'fs_bg_color' )    ?: '#ffffff00';
    $fs_features    = get_field( 'fs_features' );
    ?>
    <section class="page-with-components">
            <div class="services component-features-split"
             style="background-color:<?php echo esc_attr( $fs_bg_color ); ?>;">
        <div class="container">
            <div class="row align-items-start">

                <div class="col-lg-5 col-md-12 fs-heading-col pe-lg-5 mb-5 mb-lg-0">
                    <?php if ( $fs_title ) : ?>
                        <h2 class="fs-main-title fw-bold"
                            style="color:<?php echo esc_attr( $fs_title_color ); ?>;">
                            <?php echo esc_html( $fs_title ); ?>
                        </h2>
                    <?php endif; ?>
                </div>

                <div class="col-lg-7 col-md-12 fs-features-col">
                    <?php if ( $fs_features ) :
                        $total = count( $fs_features );
                        foreach ( $fs_features as $i => $feat ) :
                            $is_last = ( $i === $total - 1 );
                    ?>
                    <div class="fs-feature-item d-flex align-items-start gap-3<?php echo $is_last ? '' : ' fs-feature-divider'; ?>">

                        <?php if ( ! empty( $feat['icon'] ) ) : ?>
                            <div class="fs-feature-icon flex-shrink-0">
                                <img src="<?php echo esc_url( $feat['icon']['url'] ); ?>"
                                     alt="<?php echo esc_attr( $feat['icon']['alt'] ); ?>" />
                            </div>
                        <?php endif; ?>

                        <div class="fs-feature-text">
                            <?php if ( ! empty( $feat['title'] ) ) : ?>
                                <h5 class="fs-feature-title fw-bold mb-1">
                                    <?php echo esc_html( $feat['title'] ); ?>
                                </h5>
                            <?php endif; ?>
                            <?php if ( ! empty( $feat['content'] ) ) : ?>
                                <div class="fs-feature-desc mb-0">
                                    <?php echo wp_kses_post( $feat['content'] ); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                    <?php endforeach; endif; ?>
                </div>

            </div>
        </div>
    </div>
    </section>


    <div class="treatments">
        <div class="bg-white radius-30">
            <div class="product">
                <div class="container">
                    <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                        <h2><?php the_field( 'treatments_title' ); ?></h2>
                        <?php $treatments_button = get_field( 'treatments_button' ); ?>
                        <?php if ( $treatments_button ) : ?>
                        <a class="btn secondary-btn" href="<?php echo esc_url( $treatments_button['url'] ); ?>"
                            target="<?php echo esc_attr( $treatments_button['target'] ); ?>"><?php echo esc_html( $treatments_button['title'] ); ?></a>
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
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                        alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                    <?php endif; ?>
                                </div>
                                <h5>
                                    <?php the_title(); ?>
                                </h5>
                                <p><?php the_sub_field( 'description' ); ?></p>
                                <div class="d-flex justify-content-end">

                                    <a class="btn dark-btn" href="<?php the_permalink(); ?>">Learn More</a>

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
            </div>

            <div class="team-members">
                <h2>Meet our Team Members</h2>
                <div class="container">
                    <div class="row">

                        <?php if ( have_rows( 'team' ) ) : ?>
                        <?php while ( have_rows( 'team' ) ) : the_row(); ?>
                        <div class="mt-4 col-lg-3 col-md-6 col-sm-12 col-xs-12">
                            <div class="members-box">
                                <div class="member-img">
                                    <?php $image = get_sub_field( 'image' ); ?>
                                    <?php if ( $image ) : ?>
                                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                                        alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="member-info">
                                    <h6><?php the_sub_field( 'name' ); ?></h6>
                                    <span><?php the_sub_field( 'position' ); ?></span>
                                    <p><?php the_sub_field( 'description' ); ?></p>
                                    <ul class="social-links">
                                        <?php $fb = get_sub_field( 'facebook' );
                                        if($fb): ?>
                                        <li><a href="<?php the_sub_field( 'facebook' ); ?>"><img
                                                    src="<?php echo get_template_directory_uri(); ?>/assets/facebook-a.png"
                                                    alt="facebook-a" title="facebook-a"></a></li>
                                        <?php endif; ?>
                                        <?php $x = get_sub_field( 'twitter' );
                                        if($x): ?>
                                        <li><a href="<?php the_sub_field( 'twitter' ); ?>"><img
                                                    src="<?php echo get_template_directory_uri(); ?>/assets/twitter-a.png"
                                                    alt="twitter-a" title="twitter-a"></a></li>
                                        <?php endif; ?>
                                        <?php $ig = get_sub_field( 'instagram' );
                                        if($ig): ?>
                                        
                                        <li><a href="<?php the_sub_field( 'instagram' ); ?>"><img
                                                    src="<?php echo get_template_directory_uri(); ?>/assets/instagram-a.png"
                                                    alt="instagram-a" title="instagram-a"></a></li><?php endif; ?>
                                        <?php $linkedin = get_sub_field( 'linkedin' );
                                        if($linkedin): ?>
                                        <li><a href="<?php the_sub_field( 'linkedin' ); ?>"><img
                                                    src="<?php echo get_template_directory_uri(); ?>/assets/linkedin-a.png"
                                                    alt="linkedin-a" title="linkedin-a"></a></li><?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="marketing-sec-main hair-solution-main pb-54 about-solution">
        <div class="container">
            <div class="marketing-wrap hair-solution-wrap">
                <div class="marketing-left-content">
                    <?php the_field( 'solution_content' ); ?>

                    <?php $solution_button = get_field( 'solution_button' ); ?>
                    <?php if ( $solution_button ) : ?>
                    <a class="btn secondary-btn" href="<?php echo esc_url( $solution_button['url'] ); ?>"
                        target="<?php echo esc_attr( $solution_button['target'] ); ?>"><?php echo esc_html( $solution_button['title'] ); ?></a>
                    <?php endif; ?>
                </div>
                <div class="marketing-right-img">

                    <?php $solution_image = get_field( 'solution_image' ); ?>
                    <?php if ( $solution_image ) : ?>
                    <img class="lg-show" src="<?php echo esc_url( $solution_image['url'] ); ?>"
                        alt="<?php echo esc_attr( $solution_image['alt'] ); ?>" />
                    <?php endif; ?>

                    <?php $solution_image_mobile = get_field( 'solution_image_mobile' ); ?>
                    <?php if ( $solution_image_mobile ) : ?>
                    <img class="mb-show" src="<?php echo esc_url( $solution_image_mobile['url'] ); ?>"
                        alt="<?php echo esc_attr( $solution_image_mobile['alt'] ); ?>" />
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>



    <!-- loved-people section -->
    <section class="loved-by-people prp-loved-by-people">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Loved by people </h2>
                <!-- <a href="#"><img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-down.png" alt="arrow">See All</a> -->
            </div>
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
                                <img src="<?php echo esc_url( $profile_icon['url'] ); ?>"
                                    alt="<?php echo esc_attr( $profile_icon['alt'] ); ?>">
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


    <?php include 'template-part/faq-section.php'; ?>

</div>

<script src="<?php echo get_template_directory_uri(); ?>dist/js/custom.min.js"></script>
<?php get_footer(); ?>