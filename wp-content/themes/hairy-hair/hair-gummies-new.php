<?php /* Template Name: Hair Gummies V2 */ ?>

<?php get_header(); ?>

<style>
.outline-btn {
    background: transparent;
    color: #fff;
    border: 1px solid #febc5e;
}

.outline-btn:hover {
    border: 1px solid #febc5e;
}

.info-btn {
    display: inline-block;
    background: #C7E9FF;
    color: #000;
    font-weight: bold;
    border-radius: 5px;
    font-size: 14px;
    line-height: 20px;
    padding: 5px;
    margin: 10px!important;
}

/* All these just extra style over the old style */
.hair-gummies-new h1 {
    font-size: 44px;
    margin: 10px 0;
}

.hair-gummies-new .contact-btn {
    margin-top: 100px;
}

/* .hair-gummies-new .large-btn {
    padding: 18px 30px 18px;
} */

.hair-gummies-new .main-banner-content.hairphill-main-ban .ban-left-content {
    max-width: 570px;
}

.hair-gummies-new .main-banner-content p {
    font-size: 18px;
    margin-bottom: 20px;
}

.hair-gummies-new .hiding-sec {
    padding: 45px 0;
    margin: 24px 16px 0 16px;
}

.hair-gummies-new .hiding-sec .row {
    max-width: 1200px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.hair-gummies-new h2 {
    font-size: 32px;
    line-height: 32px;
}

.hair-gummies-new .product-content p {
    display: flex;
    flex-direction: row;
    font-size: 14px;
    line-height: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.hair-gummies-new .hiding-sec .right-content ul {
    gap: 10px;
}

.hair-gummies-new .hiding-sec .right-content ul li {
    font-size: 12px;
    letter-spacing: 0;
}

.hair-gummies-new .hiding-sec .left-img {
    width: 100%;
    height: 100%;
}

.hair-gummies-new .hiding-sec .left-img img {
    display: block;
    object-fit: cover;
    width: 100%;
    height: 100%;
}


.hair-gummies-new .right-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    vertical-align: middle;
    height: 100%;
}

.hair-gummies-new .before-after {
    display: flex;
    margin-top: 20px;
}

.hair-gummies-new .before-after-image {
    margin-right: 37px;
    width: 270px;
    height: 176px;
}

.hair-gummies-new .before-after-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.hair-gummies-new .before-after-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hair-gummies-new .before-after-info .ba-item p {
    font-size: 12px;
    margin: 0;
    line-height: 17px;
}

.hair-gummies-new .before-after-info .ba-item {
    margin-bottom: 17px;
}

.hair-gummies-new .before-after-info .ba-item:last-child {
    margin-bottom: 0px;
}

@media screen and (max-width: 768px) {
    .hair-gummies-new .before-after {
        display: block;
    }

    .hair-gummies-new .before-after .before-after-info {
        margin-top: 17px;
    }

    .hair-gummies-new .before-after-image {
        width: 100%;
        height: auto;
    }

    .hair-gummies-new .product-content p span {
        margin-left: 0;
        margin-top: 10px;
    }
}


/* How it works */
.hair-gummies-new .steps-to-fllow .step-animation .step {
    display: block;
    min-height: auto;
    max-width: 240px;
    padding: 27px 15px 55px;
}

.hair-gummies-new .steps-to-fllow .step-animation .step .step-footer {
    display: flex;
    justify-content: space-between;
    padding: 10px;
}

.hair-gummies-new .steps-to-fllow .step-footer .step-icon {
    width: 36px;
    height: 36px;
}

.hair-gummies-new .steps-to-fllow .step-footer .step-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.hair-gummies-new .steps-to-fllow .step-footer .step-footer-wrap {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hair-gummies-new .steps-to-fllow .step-footer .step-footer-wrap h6,
.hair-gummies-new .steps-to-fllow .step-footer .step-footer-wrap p {
    color: #3D7ECE;
    font-size: 14px;
    margin: 0;
}

.hair-gummies-new .steps-to-fllow .step-footer .step-footer-wrap h6 {
    font-weight: 600;
}

.hair-gummies-new .steps-to-fllow .step-animation .step h5 {
    margin-top: 27px;
    margin-bottom: 16px;
    font-size: 14px;
}

.hair-gummies-new .steps-to-fllow .step-animation .step p {
    font-size: 10px;
    letter-spacing: 0px;
    line-height: 20px;
}

.hair-gummies-new #how-it-works .global-title {
    margin-bottom: 0;
}

.hair-gummies-new #how-it-works {
    margin: 75px 0;
}

.hair-gummies-new #how-it-works h2 {
    font-size: 32px;
    margin-bottom: 37px
}

/* proven-treatment-stage */
.hair-gummies-new .proven-treatment-stage {
    text-align: center;
    background-color: #ffffff;
    padding: 65px 0;
    margin: 0 16px;
    border-radius: 16px;
}

.hair-gummies-new .proven-treatment-desc {
    font-family: 'Altone';
    font-size: 14px;
    line-height: 23px;
    color: #0B1D38;
    max-width: 550px;
    margin: 0 auto;
    margin-bottom: 30px;
}

.hair-gummies-new .proven-stages {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: space-between;
}

.hair-gummies-new .proven-stages .stage {
    padding: 27px 32px;
    background-color: #EEF2F6;
    border-radius: 12px;
    width: calc(( 100% / 3 ) - 26px );
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hair-gummies-new .proven-stages .stage:last-child {
    margin-right: 0;
}

.hair-gummies-new .proven-stages .stage img.stage-img {
    display: block;
    width: 100%;
    height: 217px;
    object-fit: cover;
    border-radius: 12px;
}

.hair-gummies-new .proven-stages .stage h3.stage-title {
    font-size: 24px;
    margin-top: 27px;
    margin-bottom: 0;
    text-align: left;
}

.hair-gummies-new .proven-stages .stage p.stage-sub-title {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0;
    margin-bottom: 26px;
    text-align: left;
}

.hair-gummies-new .proven-stages .stage li.check-list {
    text-align: left;
    padding-left: 30px;
    position: relative;
    margin-bottom: 13px;
    line-height: 15px;
}

.hair-gummies-new .proven-stages .stage li.check-list:before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 18px;
    height: 18px;
    background-image: url('<?php echo get_template_directory_uri(); ?>/assets/blue-tick-icon.svg');
    background-size: contain;
    background-repeat: no-repeat;
}

.hair-gummies-new .proven-stages .stage li.check-list span {
    font-size: 12px;
    letter-spacing: 0;
    font-weight: 400;
}

.hair-gummies-new .proven-stages .stage a.btn.secondary-btn {
    margin-top: 13px;
    width: 100%;
}

@media screen and (max-width: 1024px) {

    .hair-gummies-new .proven-stages .stage {
        margin-bottom: 26px;
        width: calc(( 100% / 2) - 13px );
    }
}

@media screen and (max-width: 768px) {
    .hair-gummies-new .proven-stages .stage {
        width: 100%;
        margin-right: 0;
    }
}

/* why-choose-hair */
.hair-gummies-new .why-choose-hair {
    background: none;
    padding: 65px 0;
    margin: 0;
}

.treatments-effect {
    padding: 0 0 35px;
}

.hair-gummies-new .treatment-slide {
    background: #fff;
    padding: 15px;
    border-radius: 8px;
}

.hair-gummies-new .treatments-effect .treatment-slider.slick-slider.bottom-arrows .slick-prev,
.hair-gummies-new .treatments-effect .treatment-slider.slick-slider.bottom-arrows .slick-next {
    display: none !important;
}

.hair-gummies-new .treatments-effect .global-title {
    max-width: 485px;
}

.hair-gummies-new .treatments-effect .global-title p {
    font-size: 14px;
}

.hair-gummies-new .treatments-effect .treatment-slider .slick-list .treatment-slide img {
    width: 100%;
    border-radius: 8px;
    height: 200px;
    object-fit: cover;
}

.hair-gummies-new .treatments-effect .treatment-slider .slick-list .treatment-slide .comparing-desc {
    max-width: 307px;
    margin: 0 auto;
}

.hair-gummies-new .treatments-effect .treatment-slider .slick-list .treatment-slide .comparing-desc * {
    text-align: center;
    margin-top: 43px;
    font-size: 12px;
    line-height: 20px;
    letter-spacing: 0;
}

/* why-choose-us */
.hair-gummies-new .why-choose-us {
    background: #0B1D38;
    margin: 0 16px;
    padding: 65px 0;
    border-radius: 16px;
    margin-bottom: 50px;
}

.hair-gummies-new .why-choose-us .global-title {
    margin-bottom: 0;
}

.hair-gummies-new .why-choose-us h2 {
    color: #fff;
    margin-bottom: 0;
}

.hair-gummies-new .why-choose-us .why-right-content {
    text-align: center;
    display: flex;
    flex-wrap: wrap;

}

.hair-gummies-new .why-choose-us .why-right-content .growth-box {
    width: calc(100% / 3);
    height: auto;
    margin-top: 60px;
}

.hair-gummies-new .why-choose-us .why-right-content .growth-box h6 {
    font-size: 24px;
    line-height: normal;
    letter-spacing: 0;
    color: #fff;
    font-weight: 600;
    margin-top: 16px;
}

.hair-gummies-new .why-choose-us #counter .item {
    margin-top: 30px;
}

.hair-gummies-new .why-choose-us .why-right-content .growth-box p {
    color: #fff;
    font-size: 12px;
    font-weight: 400;
    max-width: 240px;
    margin: 0 auto;
    margin-top: 8px;
}

@media screen and (max-width: 1024px) {
    .hair-gummies-new .why-choose-us .why-right-content .growth-box {
        width: calc(100% / 2);
    }
}

@media screen and (max-width:768px) {
    .hair-gummies-new .why-choose-us .why-right-content .growth-box {
        width: calc(100% / 1);
        margin-top: 30px;
    }
}

.hair-gummies-new .why-choose-us .why-us-stages .why-us-stages-wrapper a {
    margin-top: 30px;
}

#hairjourney {
    background: #fff;
    margin: 0 16px;
    border-radius: 16px;
    padding: 80px;
    text-align: center;
    margin-bottom: 75px;
}

#hairjourney.steps-to-fllow .steps-container {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
}

#hairjourney.steps-to-fllow .step-animation .journey-step {
    background: white;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    border-radius: 16px;
    padding: 30px 20px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .8s ease, transform .8s ease;

}

.hair-gummies-new #hairjourney.steps-to-fllow .step-animation .journey-step.show {
    opacity: 1;
    transform: translateY(0);
}

#hairjourney.steps-to-fllow .step-animation .arrow {
    transition: left 4s 
ease, opacity .3s 
ease;
}
#hairjourney.steps-to-fllow .step-animation .timeline::after {
    left: 0;
}

.hair-gummies-new #hairjourney.steps-to-fllow .step-animation .journey-step h5 {
    font-size: 24px;
    margin-bottom: 6px;
}

.hair-gummies-new #hairjourney.steps-to-fllow .step-animation .journey-step p {
    font-size: 12px;
    max-width: 225px;
    margin: 0 auto;
}

.hair-gummies-new #hairjourney.steps-to-fllow .step-animation .journey-timeline {
    position: absolute;
    top: 50%;
    bottom: auto;
    left: 0;
    height: 2px;
    width: 0;
    background-image: linear-gradient(to right, #0a2540 33%, transparent 0);
    background-size: 16px 2px;
    background-repeat: repeat-x;
    z-index: 1;
    transition: width 4s ease;
}

.hair-gummies-new #hairjourney.steps-to-fllow .step-animation .journey-timeline::after {
    position: absolute;
    height: 15px;
    width: 15px;
    border-radius: 100%;
    content: "";
    background: #3d506a;
    top: auto;
    margin-top: -6px;
    left: -6px;
}

</style>
<div class="main-page prp-treatment-detail hair-gummies-new">
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
                                <a class="large-btn btn secondary-btn me-3" href="<?php echo esc_url( $banner_button['url'] ); ?>"
                                    target="<?php echo esc_attr( $banner_button['target'] ); ?>"><?php echo esc_html( $banner_button['title'] ); ?></a>
                                <?php endif; ?>
                                <?php $banner_button_two = get_field( 'banner_button_two' ); ?>
                                <?php if ( $banner_button_two ) : ?>
                                <a class="large-btn btn outline-btn" href="<?php echo esc_url( $banner_button_two['url'] ); ?>"
                                    target="<?php echo esc_attr( $banner_button_two['target'] ); ?>"><?php echo esc_html( $banner_button_two['title'] ); ?></a>
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

    <?php 
    // Get ACF fields
    $hg_product_image_1           = get_field('hg_product_image_1'); 
    $hg_product_content_1         = get_field('hg_product_content_1');
    $hg_product_content_button_1  = get_field('hg_product_content_button_1');
    $before_and_after             = get_field('before_and_after');
    $ba_info_1                    = get_field('ba_info_1');
    $ba_info_2                    = get_field('ba_info_2');
    $ba_info_3                    = get_field('ba_info_3');
    $ba_info_4                    = get_field('ba_info_4');

    // Check if any content exists before showing the section
    if ( 
        $hg_product_image_1 || 
        $hg_product_content_1 || 
        $hg_product_content_button_1 || 
        $before_and_after ||
        $ba_info_1 || 
        $ba_info_2 || 
        $ba_info_3 || 
        $ba_info_4 
    ) : 
    ?>
    <section class="hiding-sec">
        <div class="container">
            <div class="row">
                <!-- Left Image -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="left-img">
                        <?php if ( !empty($hg_product_image_1['url']) ) : ?>
                        <img src="<?php echo esc_url($hg_product_image_1['url']); ?>"
                            alt="<?php echo esc_attr($hg_product_image_1['alt'] ?? ''); ?>" />
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Content -->
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="right-content">
                        <div class="product-content">
                            <?php 
                        // Display product content (allowing basic HTML)
                        if ( $hg_product_content_1 ) {
                            echo wp_kses_post($hg_product_content_1);
                        }
                        ?>

                            <!-- Button -->
                            <?php if ( $hg_product_content_button_1 ) : ?>
                            <div class="tackle-btn mt-3">
                                <a class="large-btn btn secondary-btn"
                                    href="<?php echo esc_url($hg_product_content_button_1['url']); ?>"
                                    target="<?php echo esc_attr($hg_product_content_button_1['target']); ?>">
                                    <span><?php echo esc_html($hg_product_content_button_1['title']); ?></span>
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Optional Before and After Info -->
                        <?php if ( $before_and_after || $ba_info_1 || $ba_info_2 || $ba_info_3 || $ba_info_4 ) : ?>
                        <div class="before-after">
                            <?php if($before_and_after): ?>
                            <div class="before-after-image">
                                <img src="<?php echo esc_url($before_and_after['url']); ?>" alt="">
                            </div>
                            <?php endif; ?>
                            <div class="before-after-info">
                                <?php if ($ba_info_1) : ?>
                                <div class="ba-item"><?php echo wp_kses_post($ba_info_1); ?></div>
                                <?php endif; ?>
                                <?php if ($ba_info_2) : ?>
                                <div class="ba-item"><?php echo wp_kses_post($ba_info_2); ?></div>
                                <?php endif; ?>
                                <?php if ($ba_info_3) : ?>
                                <div class="ba-item"><?php echo wp_kses_post($ba_info_3); ?></div>
                                <?php endif; ?>
                                <?php if ($ba_info_4) : ?>
                                <div class="ba-item"><?php echo wp_kses_post($ba_info_4); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <section id="how-it-works" class="steps-to-fllow">
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
                        <div class="step-footer">
                            <div class="step-icon">
                                <?php $icon = get_sub_field( 'icon' ); ?>
                                <?php if ( $icon ) : ?>
                                <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                    alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="step-footer-wrap">
                                <h6>STEP <?php echo $count; ?></h6>
                                <p><?php the_sub_field( 'step_time' ); ?></p>
                            </div>
                        </div>
                        <h5><?php the_sub_field( 'title' ); ?></h5>
                        <p>
                            <?php the_sub_field( 'description' ); ?>
                        </p>
                    </div>

                </div>
                <?php $count = $count + 1; ?>
                <?php endwhile; ?>
                <?php else : ?>
                <?php // No rows found ?>
                <?php endif; ?>
            </div>
        </div>
        <?php $steps_to_flow_button = get_field( 'steps_to_flow_button' ); ?>
        <div class="d-flex flex-wrap justify-content-center">
            <?php if ( $steps_to_flow_button ) : ?>
            <a class="btn secondary-btn" href="<?php echo esc_url( $steps_to_flow_button['url'] ); ?>"
                target="<?php echo esc_attr( $steps_to_flow_button['target'] ); ?>"><span><?php echo esc_html( $steps_to_flow_button['title'] ); ?></span></a>
            <?php endif; ?>
        </div>
    </section>

    <?php
$proven_treatment_title = get_field('proven_treatment_title');
$proven_treatment_description = get_field('proven_treatment_description');
$proven_treatment_stages = get_field('proven_treatment_stages');

if($proven_treatment_title || $proven_treatment_description || $proven_treatment_stages): ?>
    <section class="proven-treatment-stage">
        <div class="container">
            <h2 class="proven-treatment-title"><?php echo esc_html($proven_treatment_title); ?></h2>
            <p class="proven-treatment-desc"><?php echo esc_html($proven_treatment_description); ?></p>
            <div class="proven-stages">
                <?php 
                // Check if the repeater field has rows
                if( have_rows('proven_treatment_stages') ):
                    while( have_rows('proven_treatment_stages') ): the_row();
                        // Get sub-fields for each stage
                        $stage_image = get_sub_field('stage_image');
                        $stage_title = get_sub_field('stage_title');
                        $stage_sub_title = get_sub_field('stage_sub_title');
                        $stage_button = get_sub_field('stage_button');
                        $stage_details = get_sub_field('stage_details'); // Nested repeater
                    ?>
                <div class="stage">
                    <?php if($stage_image): ?>
                    <img src="<?php echo esc_url($stage_image['url']); ?>"
                        alt="<?php echo esc_attr($stage_image['alt']); ?>" class="stage-img">
                    <?php endif; ?>
                    <h3 class="stage-title"><?php echo esc_html($stage_title); ?></h3>
                    <p class="stage-sub-title"><?php echo esc_html($stage_sub_title); ?></p>
                    <ul class="stage-details">
                        <?php 
                            // Loop through nested repeater (stage_details)
                            if($stage_details): 
                                foreach($stage_details as $detail): ?>
                        <li class="check-list"><span><?php echo esc_html($detail['detail']); ?></span></li>
                        <?php endforeach; 
                            endif; ?>
                    </ul>

                    <?php if($stage_button): ?>
                    <a href="<?php echo esc_url($stage_button['url']); ?>" class="btn secondary-btn">
                        <?php echo esc_html($stage_button['title']); ?>
                    </a>
                    <?php endif; ?>

                </div>
                <?php endwhile;
                endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <?php
// Get all relevant fields
$hg_treatments_contents = get_field('hg_treatments_contents');
$hg_treatments_button   = get_field('hg_treatments_button');
$treatment_compare_repeater = have_rows('treatment_compare_repeater');

// Only display the section if any field has a value
if ( $hg_treatments_contents || $hg_treatments_button || $has_rows ) :
?>
    <section class="why-choose-hair">
        <div class="treatments-effect mt-5">
            <div class="container">
                <?php if ( $hg_treatments_contents ) : ?>
                <div class="global-title justify-content-between align-items-center">
                    <?php echo $hg_treatments_contents; ?>
                </div>
                <?php endif; ?>

                <?php if ( $treatment_compare_repeater ) : ?>
                <div class="treatment-slider bottom-arrows">
                    <?php while ( have_rows( 'treatment_compare_repeater' ) ) : the_row(); ?>
                    <div class="treatment-slide">
                        <?php $comparing_image = get_sub_field( 'comparing_image' ); ?>
                        <?php if ( $comparing_image ) : ?>
                        <img src="<?php echo esc_url( $comparing_image['url'] ); ?>"
                            alt="<?php echo esc_attr( $comparing_image['alt'] ); ?>" />
                        <?php endif; ?>
                        <?php $comparing_description = get_sub_field( 'comparing_description' ); ?>
                        <?php if ( $comparing_description ) : ?>
                        <div class="comparing-desc"><?php echo $comparing_description; ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( $hg_treatments_button ) : ?>
        <div class="button-wrap">
            <div class="d-flex flex-wrap justify-content-center">
                <a class="btn secondary-btn mt-0" href="<?php echo esc_url( $hg_treatments_button['url'] ); ?>"
                    target="<?php echo esc_attr( $hg_treatments_button['target'] ); ?>">
                    <?php echo esc_html( $hg_treatments_button['title'] ); ?>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php
// Get all relevant ACF fields first
$why_choose_hairy_title   = get_field('why_choose_hairy_title');
$why_choose_hairy_image   = get_field('why_choose_hairy_image');
$why_choose_hairy_button  = get_field('why_choose_hairy_button');
$why_choose_hairy_repeater = have_rows('why_choose_hairy');
$counter_repeater         = have_rows('counter');

// Only show section if at least one field has content
if ( $why_choose_hairy_title || $why_choose_hairy_image || $why_choose_hairy_button || $why_choose_hairy_repeater || $counter_repeater ) :
?>
    <section class="why-choose-us">
        <div class="container">

            <?php if ( $why_choose_hairy_title ) : ?>
            <div class="global-title text-center">
                <?php echo $why_choose_hairy_title; ?>
            </div>
            <?php endif; ?>

            <div class="why-us-stages">
                
                <div class="why-us-stages-wrapper">
                    <!-- <?php //if ( $why_choose_hairy_image ) : ?>
                    <div class="why-left">
                        <img src="<?php //echo esc_url( $why_choose_hairy_image['url'] ); ?>"
                            alt="<?php //echo esc_attr( $why_choose_hairy_image['alt'] ); ?>" />
                    </div>
                    <?php //endif; ?> -->
                    <div class="why-right-content">
                        <?php if ( $why_choose_hairy_repeater ) : ?>
                        <?php while ( have_rows('why_choose_hairy') ) : the_row(); ?>
                        <div class="growth-box">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/checkmark.png" alt="checkmark"
                                title="checkmark">
                            <h6><?php the_sub_field('title'); ?></h6>
                            <p><?php the_sub_field('description'); ?></p>
                        </div>
                        <?php endwhile; ?>
                        <?php endif; ?>
                    </div>

                    <?php if ( $why_choose_hairy_button ) : ?>
                    <div class="d-flex flex-wrap justify-content-center">
                        <a class="btn secondary-btn" href="<?php echo esc_url( $why_choose_hairy_button['url'] ); ?>"
                            target="<?php echo esc_attr( $why_choose_hairy_button['target'] ); ?>">
                            <span><?php echo esc_html( $why_choose_hairy_button['title'] ); ?></span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- <?php if ( $counter_repeater ) : ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="hair-counter" id="counter">
                        <?php while ( have_rows('counter') ) : the_row(); ?>
                        <div class="item">
                            <?php $icon = get_sub_field('icon'); ?>
                            <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                            <?php endif; ?>
                            <h3 data-number="<?php echo get_sub_field('count_number'); ?>">
                                <span><?php the_sub_field('count_symbol'); ?></span>
                            </h3>
                            <p><?php the_sub_field('count_title'); ?></p>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?> -->

        </div>
    </section>
    <?php endif; ?>

    <?php 
    $in_content_1 = do_shortcode( '[hh_campaign_widget slot="In Content 1"]' ); 
        if($in_content_1 != '') {
        echo "<div style='text-align: center; margin-bottom: 70px;'>".$in_content_1.'</div>';
        }
    ?>

    <?php
    $hair_journey_title = get_field('hair_journey_title');
    $has_journey_steps = have_rows('hair_journey_repeater');

    if ( $hair_journey_title || $has_journey_steps ) : 
    ?>
    <section id="hairjourney" class="steps-to-fllow">
        <div class="container">
            <?php if ( $hair_journey_title ) : ?>
            <div class="global-title text-center">
                <h2 class="global-title text-center"><?php echo esc_html( $hair_journey_title ); ?></h2>
            </div>
            <?php endif; ?>
        </div>

        <?php if ( $has_journey_steps ) : ?>
        <div class="step-animation">
            <div class="steps-container" id="stepsJourney">
                <div class="journey-timeline"></div>
                <div class="arrow" id="journeyArrow"></div>

                <?php 
                $count = 1;
                while( have_rows('hair_journey_repeater') ) : the_row(); 
                    $journey_image = get_sub_field('journey_image');
                    $journey_title = get_sub_field('journey_title');
                    $journey_description = get_sub_field('journey_description');
            ?>
                <div class="journey-step" data-step="<?php echo $count; ?>">
                    <div class="top-card">
                        <?php if ( $journey_image ) : ?>
                        <img src="<?php echo esc_url( $journey_image['url'] ); ?>"
                            alt="<?php echo esc_attr( $journey_image['alt'] ); ?>" />
                        <?php endif; ?>

                        <?php if ( $journey_title ) : ?>
                        <h5><?php echo esc_html( $journey_title ); ?></h5>
                        <?php endif; ?>

                        <?php if ( $journey_description ) : ?>
                        <p><?php echo esc_html( $journey_description ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php $count++; endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

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
                                <a class="btn secondary-btn" href="<?php echo esc_url( $newsletter_button['url'] ); ?>"
                                    target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><?php echo esc_html( $newsletter_button['title'] ); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12">
                        <div class="right-img">
                            <div class="desk-show">
                                <?php $newsletter_image = get_field( 'newsletter_image' ); ?>
                                <?php if ( $newsletter_image ) : ?>
                                <img src="<?php echo esc_url( $newsletter_image['url'] ); ?>"
                                    alt="<?php echo esc_attr( $newsletter_image['alt'] ); ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="mb-show">
                                <?php $newsletter_image_mobile = get_field( 'newsletter_image_mobile' ); ?>
                                <?php if ( $newsletter_image_mobile ) : ?>
                                <img src="<?php echo esc_url( $newsletter_image_mobile['url'] ); ?>"
                                    alt="<?php echo esc_attr( $newsletter_image_mobile['alt'] ); ?>" />
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
<script>
// 3 STEPS home page animation
const $stepsSection = $('#stepsJourney');
const $arrow = $('#journeyArrow');
const $steps = $('.journey-step');
const $timeline = $('.journey-timeline');

if (window.matchMedia("(min-width: 1140px)").matches) {
    let animated = false;

    function isInViewport($el) {
        const elementTop = $el.offset().top;
        const elementBottom = elementTop + $el.outerHeight();
        const viewportTop = $(window).scrollTop();
        const viewportBottom = viewportTop + $(window).height();
        return elementBottom > viewportTop + ($el.outerHeight() / 2) &&
            elementTop < viewportBottom - ($el.outerHeight() / 2);
    }

    $(window).on('scroll', function() {
        if (!animated && isInViewport($stepsSection)) {
            $arrow.css({
                opacity: 1,
                left: '98%'
            });

            $timeline.css({
                width: '98%'
            });

            $steps.each(function(index) {
                const $step = $(this);
                setTimeout(() => {
                    $step.addClass('show');
                }, index * 1000);
            });

            animated = true;
        }
    });
} else {
    $steps.addClass('show');
}
</script>
<?php get_footer(); ?>