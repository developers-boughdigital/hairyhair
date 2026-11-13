<?php /* Template Name: Hair Pill */ ?>
<?php get_header(); ?>

    <div class="main-page prp-treatment-detail">
        <section class="main-banner-content hairphill-main-ban">
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


        <section class="why-choose">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="men-phill">
                            <?php the_field( 'men’s_pills_content' ); ?>
                            <?php $men_s_pills_button = get_field( 'men’s_pills_button' ); ?>
<?php if ( $men_s_pills_button ) : ?>
	<a class="btn dark-btn" href="<?php echo esc_url( $men_s_pills_button['url'] ); ?>" target="<?php echo esc_attr( $men_s_pills_button['target'] ); ?>"><span><?php echo esc_html( $men_s_pills_button['title'] ); ?></span></a>
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="men-phill women-phill-bg">
                            <?php the_field( 'women’s_pills_content' ); ?>
                            <?php $women_s_pills_button = get_field( 'women’s_pills_button' ); ?>
<?php if ( $women_s_pills_button ) : ?>
	<a class="btn dark-btn" href="<?php echo esc_url( $women_s_pills_button['url'] ); ?>" target="<?php echo esc_attr( $women_s_pills_button['target'] ); ?>"><span><?php echo esc_html( $women_s_pills_button['title'] ); ?></span></a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="hair-hold">
                            <div class="lose-hair">
                                <?php $hair_hold_image_1 = get_field( 'hair_hold_image_1' ); ?>
<?php if ( $hair_hold_image_1 ) : ?>
	<img src="<?php echo esc_url( $hair_hold_image_1['url'] ); ?>" alt="<?php echo esc_attr( $hair_hold_image_1['alt'] ); ?>" />
<?php endif; ?>
                            </div>
                            <div class="lose-content">
                                <?php the_field( 'hair_hold_content' ); ?>

                                <?php $hair_hold_button = get_field( 'hair_hold_button' ); ?>
<?php if ( $hair_hold_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $hair_hold_button['url'] ); ?>" target="<?php echo esc_attr( $hair_hold_button['target'] ); ?>"><span><?php echo esc_html( $hair_hold_button['title'] ); ?></span></a>
<?php endif; ?>
                            </div>
                            <div class="lose-hair">
                                <?php $hair_hold_image_2 = get_field( 'hair_hold_image_2' ); ?>
<?php if ( $hair_hold_image_2 ) : ?>
	<img src="<?php echo esc_url( $hair_hold_image_2['url'] ); ?>" alt="<?php echo esc_attr( $hair_hold_image_2['alt'] ); ?>" />
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </section>


        <section class="about-looks">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                        <div class="left-side">
                            <?php the_field( 'about_looks_content' ); ?>
                            <?php $about_looks_image = get_field( 'about_looks_image' ); ?>
<?php if ( $about_looks_image ) : ?>
	<img src="<?php echo esc_url( $about_looks_image['url'] ); ?>" alt="<?php echo esc_attr( $about_looks_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
                        <div class="right-side-content">

                        <?php if ( have_rows( 'about_looks' ) ) : ?>
	<?php while ( have_rows( 'about_looks' ) ) : the_row(); ?>
                            <div class="social-box">
                                <h6><?php the_sub_field( 'title' ); ?></h6>
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
        </section>

        

        <?php 
            $in_content_6 = do_shortcode( '[hh_campaign_widget slot="In Content 6"]' ); 
            if($in_content_6 != '') {
                echo "<div style='text-align: center; margin-bottom: 70px;'>".$in_content_6.'</div>';
            }
        ?> 


        <section class="how-do-tackle">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="how-left">
                            <?php the_field( 'how_do_tackle_content' ); ?>
                            <div class="tackle-btn">
                                <?php $how_do_tackle_button = get_field( 'how_do_tackle_button' ); ?>
<?php if ( $how_do_tackle_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $how_do_tackle_button['url'] ); ?>" target="<?php echo esc_attr( $how_do_tackle_button['target'] ); ?>"><span><?php echo esc_html( $how_do_tackle_button['title'] ); ?></span></a>
<?php endif; ?>
                            </div> 
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="how-right">
                            <?php $count = 1; ?>
                            <?php if ( have_rows( 'how_do_tackle' ) ) : ?>
	<?php while ( have_rows( 'how_do_tackle' ) ) : the_row(); ?>
                            <div class="strength-box">
                                <span><?php echo $count; ?></span>
                                <h6><?php the_sub_field( 'title' ); ?></h6>
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
        </section>


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
            </div>
        </section>


        <section class="why-choose-hair">
            <div class="container">
                <div class="global-title text-center">
                    <?php the_field( 'why_choose_hairy_title' ); ?>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="why-left">
                            <?php $why_choose_hairy_image = get_field( 'why_choose_hairy_image' ); ?>
<?php if ( $why_choose_hairy_image ) : ?>
	<img src="<?php echo esc_url( $why_choose_hairy_image['url'] ); ?>" alt="<?php echo esc_attr( $why_choose_hairy_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="why-right-content">

                        <?php if ( have_rows( 'why_choose_hairy' ) ) : ?>
	<?php while ( have_rows( 'why_choose_hairy' ) ) : the_row(); ?>
                            <div class="growth-box">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/checkmark.png" alt="checkmark" title="checkmark">
                                <h6><?php the_sub_field( 'title' ); ?></h6>
                                <p><?php the_sub_field( 'description' ); ?></p>
                            </div>
                            <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>
                           
                        </div>
                    </div>
                </div>
               
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hair-counter" id="counter">
                           <?php if ( have_rows( 'counter' ) ) : ?>
                        <?php while ( have_rows( 'counter' ) ) : the_row(); ?>
                        <div class="item">
                            <?php $icon = get_sub_field( 'icon' ); ?>
                            <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                            <?php endif; ?>
                            <h3 data-number="<?php echo get_sub_field( 'count_number' ); ?>"><span><?php the_sub_field( 'count_symbol' ); ?></span></h3>
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


        <div class="marketing-sec-main hair-quiz-main pb-54 which-pills">
            <div class="container">
                <div class="marketing-wrap">
                    <div class="marketing-left-content">
                        <?php the_field( 'take_quiz_content' ); ?>
                        <?php $take_quiz_button = get_field( 'take_quiz_button' ); ?>
<?php if ( $take_quiz_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $take_quiz_button['url'] ); ?>" target="<?php echo esc_attr( $take_quiz_button['target'] ); ?>"><?php echo esc_html( $take_quiz_button['title'] ); ?></a>
<?php endif; ?>
                    </div>
                    <div class="marketing-right-img">

                    <?php $take_quiz_image = get_field( 'take_quiz_image' ); ?>
<?php if ( $take_quiz_image ) : ?>
	<img class="lg-show" src="<?php echo esc_url( $take_quiz_image['url'] ); ?>" alt="<?php echo esc_attr( $take_quiz_image['alt'] ); ?>" />
<?php endif; ?>

<?php $take_quiz_image_mobile = get_field( 'take_quiz_image_mobile' ); ?>
<?php if ( $take_quiz_image_mobile ) : ?>
	<img class="mb-show" src="<?php echo esc_url( $take_quiz_image_mobile['url'] ); ?>" alt="<?php echo esc_attr( $take_quiz_image_mobile['alt'] ); ?>" />
<?php endif; ?>
                       
                    </div>
                </div>
            </div>
        </div>

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
                <div class="d-flex flex-wrap justify-content-center">
                    <a href="/resources" class="btn secondary-btn">View All Resources</a>
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

    

    <script src="<?php echo get_template_directory_uri(); ?>/dist/js/custom.min.js"></script>
<?php get_footer(); ?>

