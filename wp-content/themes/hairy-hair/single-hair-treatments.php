<?php get_header(); ?>

    <div class="main-page prp-treatment-detail">
        <section class="main-banner-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 align-self-center">
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
                                        <a class="btn secondary-btn" href="<?php echo esc_url( $banner_button['url'] ); ?>" target="<?php echo esc_attr( $banner_button['target'] ); ?>"><?php echo esc_html( $banner_button['title'] ); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="ban-right-img">
                        <?php $banner_image = get_field( 'banner_image' ); ?>
<?php if ( $banner_image ) : ?>
	<img src="<?php echo esc_url( $banner_image['url'] ); ?>" alt="<?php echo esc_attr( $banner_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="prp-therapy">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-12">
                        <div class="global-title">
                            <h2>
                            <?php the_field( 'about_treatments_title' ); ?>
                            </h2>
                        </div>
                        <div class="therapy-image">
                        <?php $about_treatments_image = get_field( 'about_treatments_image' ); ?>
<?php if ( $about_treatments_image ) : ?>
	<img src="<?php echo esc_url( $about_treatments_image['url'] ); ?>" alt="<?php echo esc_attr( $about_treatments_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-12">
                        <div class="all-therapy">

                        <?php if ( have_rows( 'about_treatments' ) ) : ?>
                            <?php while ( have_rows( 'about_treatments' ) ) : the_row(); ?>
                            <div class="each-therapy">
                                <h5><?php the_sub_field( 'title' ); ?></h5>
                                <?php the_sub_field( 'description' ); ?>
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

        <section class="white-radius-bg">
            <div class="wrapper justify-content-center">
                <div class="prp-therapy-wrap">
                    <div class="container">
                        <div class="global-title text-center">
                            <h2><?php the_field( 'why_choose_treatment_title' ); ?></h2>
                        </div>
                    </div>
                    <div class="container">
                        <div class="why-us-slider ">
                            
                            <div class="prp-slider bottom-arrows">

                            <?php if ( have_rows( 'why_choose_treatment' ) ) : ?>
                                <?php while ( have_rows( 'why_choose_treatment' ) ) : the_row(); ?>
                                <div class="prp-slide">
                                <?php $icon = get_sub_field( 'icon' ); ?>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
		<?php endif; ?>
                                    <h3><?php the_sub_field( 'title' ); ?></h3>
                                    <p><?php the_sub_field( 'description' ); ?></p>
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
        </section>

        <section class="how-we-works">
            <div class="container">
                <div class="global-title text-center">
                    <h2><?php the_field( 'how_treatment_works_title' ); ?></h2>
                </div>
            </div>
            <div class="container-fluid">
                <div class="works-steps">
                    <div class="row">
                    <?php $count = 1; ?>
                    <?php if ( have_rows( 'how_treatment_works' ) ) : ?>
                        <?php while ( have_rows( 'how_treatment_works' ) ) : the_row(); ?>
                        <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                            <div class="each-work-step">
                                <div class="top-work">
                                    <div class="work-icon">
                                    <?php $icon = get_sub_field( 'icon' ); ?>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
		<?php endif; ?>
                                    </div>
                                    <div class="right-side">
                                        <h6><?php the_sub_field( 'title' ); ?></h6>
                                        <p><?php the_sub_field( 'description' ); ?></p>
                                    </div>
                                </div>
                                <div class="bottom-work">
                                    <h5>Step  <?php echo $count; ?></h5>
                                    <p><?php the_sub_field( 'step_time_description' ); ?></p>
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
        </section>

        <?php 
            $in_content_8 = do_shortcode( '[hh_campaign_widget slot="In Content 8"]' ); 
            if($in_content_8 != '') {
                echo "<div style='text-align: center; margin-bottom: 100px;'>".$in_content_8.'</div>';
            }
        ?> 

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="container">
                    <div class="real-result">
                        <div class="row">
                            <div class="col-xl-6 col-lg-12 col-md-12 col-12">
                                <div class="global-title">
                                    <h2>Real Results from</br> Therapy - Before and After</h2>
                                </div>
                                <div class="resule-slider bottom-arrows">


                                <?php if ( have_rows( 'therapy_before_and_after' ) ) : ?>
	<?php while ( have_rows( 'therapy_before_and_after' ) ) : the_row(); ?>
                                    <div class="each-result">
                                        <div class="box-shadow">
                                            <div class="quote-img">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/quote.png" alt="quote">
                                            </div>
                                            <p><?php the_sub_field( 'description' ); ?></p>
                                            <div class="result-name">
                                                <div class="person-pic">
                                                    <?php $person_image = get_sub_field( 'person_image' ); ?>
		<?php if ( $person_image ) : ?>
			<img src="<?php echo esc_url( $person_image['url'] ); ?>" alt="<?php echo esc_attr( $person_image['alt'] ); ?>" />
		<?php endif; ?>
                                                </div>
                                                <div class="about-person">
                                                    <?php the_sub_field( 'person_about' ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>


                                    <!-- <div class="each-result">
                                        <div class="box-shadow">
                                            <div class="quote-img">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/quote.png" alt="quote">
                                            </div>
                                            <p>With Hairy Hair it is really easy to communicate, if there are any issues
                                                there is more understanding and solutions than I've experienced
                                                previously. I'm happy with the process and journey so far.</p>
                                            <div class="result-name">
                                                <div class="person-pic">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/person-1.png" alt="person">
                                                </div>
                                                <div class="about-person">
                                                    <b>Alex Smith</b>
                                                    <p>Coaching of Halal Lab</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="each-result">
                                        <div class="box-shadow">
                                            <div class="quote-img">
                                                <img src="<?php echo get_template_directory_uri(); ?>/assets/quote.png" alt="quote">
                                            </div>
                                            <p>With Hairy Hair it is really easy to communicate, if there are any issues
                                                there is more understanding and solutions than I've experienced
                                                previously. I'm happy with the process and journey so far.</p>
                                            <div class="result-name">
                                                <div class="person-pic">
                                                    <img src="<?php echo get_template_directory_uri(); ?>/assets/person-1.png" alt="person">
                                                </div>
                                                <div class="about-person">
                                                    <b>Alex Smith</b>
                                                    <p>Coaching of Halal Lab</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12 col-md-12 col-12">
                                <div class="result-image">
                                    <?php $therapy_before_and_after_image = get_field( 'therapy_before_and_after_image' ); ?>
<?php if ( $therapy_before_and_after_image ) : ?>
	<img src="<?php echo esc_url( $therapy_before_and_after_image['url'] ); ?>" alt="<?php echo esc_attr( $therapy_before_and_after_image['alt'] ); ?>" />
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- <section class="top-providers bg-transparent pt-0">
            <div class="container">
                <div class="global-title text-center">
                    <h2>Let Us Connect You With a PRP Specialist</h2>
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
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client"
                                            title="provider-client">
                                    </div>
                                    <div class="provider-name">
                                        <h6>Rocko Band in Marquee Club</h6>
                                        <ul>
                                            <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star"
                                                    title="rating-star">5.0 <span>(100 Review)</span></li>
                                        </ul>
                                    </div>
                                    <div class="provider-distance">
                                        <span>10.3 km</span>
                                    </div>
                                </div>
                                <div class="provider-text">
                                    <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.
                                    </p>
                                    <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St,
                                        NY, USA</span>
                                </div>
                                <div class="profile-btn">
                                    <a class="btn dark-btn"><span>View Profile</span></a>
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
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client"
                                            title="provider-client">
                                    </div>
                                    <div class="provider-name">
                                        <h6>Rocko Band in Marquee Club</h6>
                                        <ul>
                                            <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star"
                                                    title="rating-star">5.0 <span>(100 Review)</span></li>
                                        </ul>
                                    </div>
                                    <div class="provider-distance">
                                        <span>10.3 km</span>
                                    </div>
                                </div>
                                <div class="provider-text">
                                    <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.
                                    </p>
                                    <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St,
                                        NY, USA</span>
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
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/provider-client.png" alt="provider-client"
                                            title="provider-client">
                                    </div>
                                    <div class="provider-name">
                                        <h6>Rocko Band in Marquee Club</h6>
                                        <ul>
                                            <li><img src="<?php echo get_template_directory_uri(); ?>/assets/rating-star.png" alt="rating-star"
                                                    title="rating-star">5.0 <span>(100 Review)</span></li>
                                        </ul>
                                    </div>
                                    <div class="provider-distance">
                                        <span>10.3 km</span>
                                    </div>
                                </div>
                                <div class="provider-text">
                                    <p>Sed interdum metus at nisi tempor laoreet. Integer gravida orci a justo sodales.
                                    </p>
                                    <span><img src="<?php echo get_template_directory_uri(); ?>/assets/map-icon.png" alt="map-icon" title="map-icon"> 75 Prince St,
                                        NY, USA</span>
                                </div>
                                <div class="profile-btn">
                                    <a class="btn dark-btn">View Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

        <section class="ultimate-guide">
            <div class="container">
            <?php $ultimate_guide_image = get_field( 'ultimate_guide_image' ); ?>
                <div class="guide-wrapper" style="background-image: url('<?php echo esc_url( $ultimate_guide_image['url'] ); ?>');">
                    <div class="global-title">
                        <h2><?php the_field( 'ultimate_guide_title' ); ?></h2>
                        <p><?php the_field( 'ultimate_guide_description' ); ?></p>
                    </div>
                    <div class="check-list">
                        <?php the_field( 'ultimate_guide_content' ); ?>
                    </div>

                   
                    <?php $ultimate_guide_button = get_field( 'ultimate_guide_button' ); ?>
<?php if ( $ultimate_guide_button ) : ?>
	<a class="btn white-btn" href="<?php echo esc_url( $ultimate_guide_button['url'] ); ?>" target="<?php echo esc_attr( $ultimate_guide_button['target'] ); ?>"><span><?php echo esc_html( $ultimate_guide_button['title'] ); ?></span></a>
<?php endif; ?>
                </div>
            </div>
        </section>

        <!-- loved-people section -->
        <section class="loved-by-people prp-loved-slider">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2><?php the_field( 'compare_treatments_title' ); ?></h2>
                    
                    <?php $compare_treatments_button = get_field( 'compare_treatments_button' ); ?>
<?php if ( $compare_treatments_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $compare_treatments_button['url'] ); ?>" target="<?php echo esc_attr( $compare_treatments_button['target'] ); ?>"><?php echo esc_html( $compare_treatments_button['title'] ); ?></a>
<?php endif; ?>
                </div>
                <div class="loved-by-slider bottom-arrows">


                <?php $compare_treatments = get_field('compare_treatments'); ?>
<?php if ($compare_treatments) : ?>
    <?php foreach ($compare_treatments as $post) : setup_postdata($post); ?>

        <div class="testi-slide">
            <div class="testi-img">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('full', array(
                            'alt' => get_the_title(),
                            'title' => get_the_title()
                        )); ?>
                    </a>
                <?php else : ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/default-image.png" alt="<?php the_title(); ?>" title="<?php the_title(); ?>">
                <?php endif; ?>

                <div class="testi-caption">
                    <h6><?php the_title(); ?></h6>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>




                   
                </div>
            </div>
        </section>

        <?php $specialist_image = get_field( 'specialist_image' ); ?>
        <section class="specialist" style="background-image: url('<?php echo esc_url( $specialist_image['url'] ); ?>');">
            <div class="container">
                <div class="bg-white text-center">
                <?php the_field( 'specialist_content' ); ?>
                    <?php $specialist_button = get_field( 'specialist_button' ); ?>
<?php if ( $specialist_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $specialist_button['url'] ); ?>" target="<?php echo esc_attr( $specialist_button['target'] ); ?>"><?php echo esc_html( $specialist_button['title'] ); ?></a>
<?php endif; ?>
                </div>
            </div>
        </section>

        <!-- the ultimate hair hub slider -->
        <section class="hair-care-resource">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Helpful Resources</h2>
                </div>
                <div class="ultimate-hair-care">

          <?php $treatment_guide = get_field( 'treatment_guide' ); ?>
<?php if ( $treatment_guide ) : ?>
	<?php foreach ( $treatment_guide as $post ) :  ?>
		<?php setup_postdata( $post ); ?>
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
                                <?php 
$categories = get_the_category(); 
if (!empty($categories)) {
    echo '<span>' . esc_html($categories[0]->name) . '</span>';
}
?>



                            </div>
                        </div>
                    </a>
                </div>
               <?php endforeach; ?>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>

                
                    

                </div>
            </div>
        </section>

        <!-- loved-people section -->
        <section class="loved-by-people prp-loved-by-people">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Loved by people </h2>
                    <!-- <a class="btn secondary-btn" href="#">See All</a> -->
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

        <section class="cta-wrapper">
            <div class="container-fluid">
                <div class="bg-dark">
                    <h2><?php the_field( 'cta_section_title' ); ?></h2>
                    
<?php $cta_section_button = get_field( 'cta_section_button' ); ?>
<?php if ( $cta_section_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $cta_section_button['url'] ); ?>" target="<?php echo esc_attr( $cta_section_button['target'] ); ?>"><?php echo esc_html( $cta_section_button['title'] ); ?></a>
<?php endif; ?>
                </div>
            </div>
        </section>

      

        <?php include 'template-part/faq-section.php'; ?>

        <section class="newsletter-grid mb-0">
            <div class="container">
                <div class="bg-primary">
                    <div class="row">
                        <div class="col-lg-7 col-md-12">
                            <div class="d-flex flex-wrap align-items-center h-100">
                                <div>
                                <?php the_field( 'newsletter_content' ); ?>

                                    <?php $newsletter_button = get_field( 'newsletter_button' ); ?>
<?php if ( $newsletter_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $newsletter_button['url'] ); ?>" target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><?php echo esc_html( $newsletter_button['title'] ); ?></a>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12">
                            <div class="right-img">
                                <div class="desk-show">
                                <?php $newsletter_image = get_field( 'newsletter_image' ); ?>
<?php if ( $newsletter_image ) : ?>
	<img src="<?php echo esc_url( $newsletter_image['url'] ); ?>" alt="<?php echo esc_attr( $newsletter_image['alt'] ); ?>" />
<?php endif; ?>
                                </div>
                                <div class="mb-show">
                                <?php $newsletter_image_mobile = get_field( 'newsletter_image_mobile' ); ?>
<?php if ( $newsletter_image_mobile ) : ?>
	<img src="<?php echo esc_url( $newsletter_image_mobile['url'] ); ?>" alt="<?php echo esc_attr( $newsletter_image_mobile['alt'] ); ?>" />
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        

    </div>

    <?php get_footer(); ?>