<?php
/**
 * Template Name: Pricing Page
 */

defined( 'ABSPATH' ) || exit;
get_header(); ?>

<div class="main-page pricing-wrap">
        <section class="main-banner-content cms-main-banner mb-0">
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

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="container">
                    <div class="global-title">
                        <?php the_content(); ?>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="all-treatments-list">
                                <h4>
                                    Prescription Treatments
                                </h4>

                                <div class="all-prescription-card">
    <?php if ( have_rows( 'prescription_treatments' ) ) : ?>
    <?php while ( have_rows( 'prescription_treatments' ) ) : the_row(); ?>
        <?php $best_value = get_sub_field( 'best_value' ); ?>
        <div class="each-prescription-card <?php the_sub_field( 'border_color' ); ?>">

            <?php if ( $best_value && in_array('Best Value', $best_value) ) : ?>
                <div class="card-tag">
                    Best Value
                </div>
            <?php endif; ?>

            <div class="left-side">
                <h5><?php the_sub_field( 'title' ); ?></h5>
                <p><?php the_sub_field( 'description' ); ?></p>
            </div>
            <div class="right-side">
                <?php the_sub_field( 'price' ); ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>


                                  
                                </div>

                                <?php the_field( 'prescription_treatment_content' ); ?>
     
                                <?php $prescription_treatment_button = get_field( 'prescription_treatment_button' ); ?>
<?php if ( $prescription_treatment_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $prescription_treatment_button['url'] ); ?>" target="<?php echo esc_attr( $prescription_treatment_button['target'] ); ?>"><?php echo esc_html( $prescription_treatment_button['title'] ); ?></a>
<?php endif; ?>
                            </div>                            
                        </div>
                        <div class="col-lg-6">
                            <div class="all-treatments-list">
                                <h4>
                                    Non-Prescription Products                                
                                </h4>

                                <div class="all-prescription-card">
<?php if ( have_rows( 'non-prescription_products' ) ) : ?>
    <?php while ( have_rows( 'non-prescription_products' ) ) : the_row(); ?>
        <?php $best_value = get_sub_field( 'best_value' ); ?>
        <div class="each-prescription-card">
            
            <?php if ( $best_value && in_array('Best Value', $best_value) ) : ?>
                <div class="card-tag">
                    Best Value
                </div>
            <?php endif; ?>

            <div class="left-side">
                <h5><?php the_sub_field( 'title' ); ?></h5>
                <p><?php the_sub_field( 'description' ); ?></p>
            </div>
            <div class="right-side">
                <?php the_sub_field( 'price' ); ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else : ?>
    <?php // No rows found ?>
<?php endif; ?>

                            
                                </div>

                                <?php the_field( 'non-prescription_product_content' ); ?>
                     
                                <?php $non_prescription_product_button = get_field( 'non-prescription_product_button' ); ?>
<?php if ( $non_prescription_product_button ) : ?>
	<a class="btn secondary-btn" href="<?php echo esc_url( $non_prescription_product_button['url'] ); ?>" target="<?php echo esc_attr( $non_prescription_product_button['target'] ); ?>"><?php echo esc_html( $non_prescription_product_button['title'] ); ?></a>
<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php get_footer(); ?>
