<?php /* Template Name: How It Works */ ?>
<?php get_header(); ?>

    <div class="main-page how-works">
        <section class="main-banner-content">
            <div class="container">
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
                    <div class="timeline" style="width: 98%;"></div>
                    <div class="arrow" id="flowArrow" style="opacity: 1; left: 98%;"></div>

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
	<a class="btn secondary-btn m-auto d-flex justify-content-center" href="<?php echo esc_url( $steps_to_flow_button['url'] ); ?>" target="<?php echo esc_attr( $steps_to_flow_button['target'] ); ?>"><span><?php echo esc_html( $steps_to_flow_button['title'] ); ?></span></a>
<?php endif; ?>
            </div>
        </section>

        <section class="white-radius-bg facts-wrapper">
            <div class="wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-9 col-md-8 col-12">
                            <div class="global-title">
                                <?php the_field( 'facts_title_content' ); ?>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="d-flex flex-wrap plas-button">
                          
                                <?php $facts_button = get_field( 'facts_button' ); ?>
<?php if ( $facts_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $facts_button['url'] ); ?>" target="<?php echo esc_attr( $facts_button['target'] ); ?>"><span><?php echo esc_html( $facts_button['title'] ); ?></span></a>
<?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <h6><?php the_field( 'facts_sub_title' ); ?></h6>
                    <div class="row">

                    <?php if ( have_rows( 'facts' ) ) : ?>
	<?php while ( have_rows( 'facts' ) ) : the_row(); ?>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="fact-wrapper">
                                <div class="fact-img">
                                    <?php $image = get_sub_field( 'image' ); ?>
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
		<?php endif; ?>
                                </div>
                                <div class="fact-desc">
                                    <?php the_sub_field( 'content' ); ?>
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

        <section class="qualities">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12">
                        <div class="quality-img">
                            <?php $qualities_image = get_field( 'qualities_image' ); ?>
<?php if ( $qualities_image ) : ?>
	<img src="<?php echo esc_url( $qualities_image['url'] ); ?>" alt="<?php echo esc_attr( $qualities_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <div class="global-title">
                            <?php the_field( 'qualities_content' ); ?>

                            <?php $qualities_button = get_field( 'qualities_button' ); ?>
<?php if ( $qualities_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $qualities_button['url'] ); ?>" target="<?php echo esc_attr( $qualities_button['target'] ); ?>"><?php echo esc_html( $qualities_button['title'] ); ?></a>
<?php endif; ?>

                            
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="benifits mb-0">
            <div class="bg-sky-blue">
                <div class="container">
                    <div class="global-title justify-content-center text-center">
                        <h2><?php the_field( 'benefits_title' ); ?></h2>
                    </div>
                    <div class="all-benifits">
                        <div class="row">

                        <?php if ( have_rows( 'benefits' ) ) : ?>
	<?php while ( have_rows( 'benefits' ) ) : the_row(); ?>
                            <div class="col-lg-4 col-md-6 col-12">
                                <div class="each-benifit">
                                    <div class="benifit-img">
                                        <?php $icon = get_sub_field( 'icon' ); ?>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
		<?php endif; ?>
                                    </div>
                                    <div class="benifit-wrap">
                                        <h6><?php the_sub_field( 'title' ); ?></h6>
                                        <p><?php the_sub_field( 'description' ); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>

                           
                        </div>
                        <div class="d-flex flex-wrap justify-content-center mt-54">
                            <?php $benefits_button = get_field( 'benefits_button' ); ?>
<?php if ( $benefits_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $benefits_button['url'] ); ?>" target="<?php echo esc_attr( $benefits_button['target'] ); ?>"><?php echo esc_html( $benefits_button['title'] ); ?></a>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="white-radius-bg facts-wrapper mb-0">
            <div class="wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-md-12">
                            <div class="quality-img">
                                <?php $fight_hair_loss_image = get_field( 'fight_hair_loss_image' ); ?>
<?php if ( $fight_hair_loss_image ) : ?>
	<img src="<?php echo esc_url( $fight_hair_loss_image['url'] ); ?>" alt="<?php echo esc_attr( $fight_hair_loss_image['alt'] ); ?>" />
<?php endif; ?>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-12">
                            <div class="global-title">
                                <?php the_field( 'fight_hair_loss_content' ); ?>
    
                                <div class="global-list">

                                    <?php $fight_hair_loss_button = get_field( 'fight_hair_loss_button' ); ?>
<?php if ( $fight_hair_loss_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $fight_hair_loss_button['url'] ); ?>" target="<?php echo esc_attr( $fight_hair_loss_button['target'] ); ?>"><?php echo esc_html( $fight_hair_loss_button['title'] ); ?></a>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>   
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

        <!-- loved-people section -->
        <section class="loved-by-people prp-loved-by-people">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Loved by people </h2>
                    <!-- <a href="#" class="btn secondary-btn"><img src="<?php echo get_template_directory_uri(); ?>/assets/arrow-down.png" alt="arrow">See All</a> -->
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

        <?php include 'template-part/faq-section.php'; ?>

    </div>


    <script src="<?php echo get_template_directory_uri(); ?>dist/js/custom.min.js"></script>
<?php get_footer(); ?>