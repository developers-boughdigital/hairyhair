<?php /* Template Name: Gender Hair Pill */ ?>
<?php get_header(); ?>

    <div class="main-page prp-treatment-detail">
        <section class="main-banner-content womenpill-main-ban">
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


        <section class="hiding-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12 col-sm-12 col-xs-12">
                        <div class="left-img">
                            <?php $hiding_image = get_field( 'hiding_image' ); ?>
<?php if ( $hiding_image ) : ?>
	<img src="<?php echo esc_url( $hiding_image['url'] ); ?>" alt="<?php echo esc_attr( $hiding_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12 col-sm-12 col-xs-12 align-self-center">
                        <div class="right-content">
                            <?php the_field( 'hiding_content' ); ?>
                            <div class="contact-btn">
                                <?php $hiding_button = get_field( 'hiding_button' ); ?>
<?php if ( $hiding_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $hiding_button['url'] ); ?>" target="<?php echo esc_attr( $hiding_button['target'] ); ?>"><?php echo esc_html( $hiding_button['title'] ); ?></a>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="cause-cure">
            <div class="container">
                <div class="global-title text-center">
                    <?php the_field( 'cause_cure_title' ); ?>
                </div>
                <div class="row">

                <?php if ( have_rows( 'cause_cure' ) ) : ?>
	<?php while ( have_rows( 'cause_cure' ) ) : the_row(); ?>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="hormonal-box">
                            <?php $icon = get_sub_field( 'icon' ); ?>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
		<?php endif; ?>
                            <h6><?php the_sub_field( 'title' ); ?></h6>
                            <p><?php the_sub_field( 'description' ); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>
                    
                </div>
                <div class="contact-btn text-center">
                    <?php $cause_cure_button = get_field( 'cause_cure_button' ); ?>
<?php if ( $cause_cure_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $cause_cure_button['url'] ); ?>" target="<?php echo esc_attr( $cause_cure_button['target'] ); ?>"><?php echo esc_html( $cause_cure_button['title'] ); ?></a>
<?php endif; ?>
                </div>
            </div>
        </section>


        <section class="why-choose-hair">
            <div class="container">
                <div class="global-title text-center">
                    <h2><?php the_field( 'benefits_title' ); ?></h2>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="why-left">
                            <?php $benefits_image = get_field( 'benefits_image' ); ?>
<?php if ( $benefits_image ) : ?>
	<img src="<?php echo esc_url( $benefits_image['url'] ); ?>" alt="<?php echo esc_attr( $benefits_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="why-right-content">

                        

<?php if ( have_rows( 'benefits' ) ) : ?>
	<?php while ( have_rows( 'benefits' ) ) : the_row(); ?>
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
                <div class="contact-btn text-center">
                    <?php $benefits_button = get_field( 'benefits_button' ); ?>
<?php if ( $benefits_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $benefits_button['url'] ); ?>" target="<?php echo esc_attr( $benefits_button['target'] ); ?>"><?php echo esc_html( $benefits_button['title'] ); ?></a>
<?php endif; ?>
                </div>
            </div>
        </section>

        <?php 
            $in_content_7 = do_shortcode( '[hh_campaign_widget slot="In Content 7"]' ); 
            if($in_content_7 != '') {
                echo "<div style='text-align: center; margin-top: 100px;'>".$in_content_7.'</div>';
            }
        ?> 


        <section class="steps-to-fllow womens-step">
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


        <section class="standard-subscription">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="left-img">
                            <?php $standard_subscription_image = get_field( 'standard_subscription_image' ); ?>
<?php if ( $standard_subscription_image ) : ?>
	<img src="<?php echo esc_url( $standard_subscription_image['url'] ); ?>" alt="<?php echo esc_attr( $standard_subscription_image['alt'] ); ?>" />
<?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="right-content">
                            <h2><?php the_field( 'standard_subscription_title' ); ?></h2>
                            <h6><?php the_field( 'standard_subscription_duration' ); ?></h6>
                            <span>From <?php the_field( 'standard_subscription_price' ); ?>/ <h5>Monthly</h5></span>
                            <?php the_field( 'standard_subscription_content' ); ?>
                            <div class="contact-btn">
                                <?php $standard_subscription_button = get_field( 'standard_subscription_button' ); ?>
<?php if ( $standard_subscription_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $standard_subscription_button['url'] ); ?>" target="<?php echo esc_attr( $standard_subscription_button['target'] ); ?>"><?php echo esc_html( $standard_subscription_button['title'] ); ?></a>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="customized-treat">
            <div class="container">
                <div class="global-title text-center">
                    <h2><?php the_field( 'customized_plans_title' ); ?></h2>
                </div>
                <div class="row">

                <?php if ( have_rows( 'customized_plans' ) ) : ?>
	<?php while ( have_rows( 'customized_plans' ) ) : the_row(); ?>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="inner-box">
                            <?php $icon = get_sub_field( 'icon' ); ?>
		<?php if ( $icon ) : ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
		<?php endif; ?>
                            <h6><?php the_sub_field( 'title' ); ?></h6>
                            <p><?php the_sub_field( 'description' ); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>
                    
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