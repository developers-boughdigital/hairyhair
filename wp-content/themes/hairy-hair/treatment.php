<?php /* Template Name: Treatment */ ?>
<?php get_header(); ?>
<div class="main-page">
    <section class="main-banner-content mb-0">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="featured-treatments">
        <div class="container">
            <div class="global-title text-center">
                <h2>Featured Treatments</h2>
            </div>
            <div class="row">
                <?php if ( have_rows( 'featured_treatments' ) ) : ?>
                <?php while ( have_rows( 'featured_treatments' ) ) : the_row(); ?>
                <?php $treatmenttt = get_sub_field( 'treatment_' ); ?>
                
                <?php $post = $treatmenttt; ?>
                <?php setup_postdata( $post ); ?>
                <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                    <div class="treatment-card <?php the_sub_field( 'background_color' ); ?>">
                        <div class="card-content">
                            <h3><?php the_title(); ?></h3>
                            <p><?php the_sub_field( 'description' ); ?></p>
                            <a href="<?php echo get_permalink($post->ID); ?>" class="btn-home treatment-btn"><span>Learn More</span></a>
                        </div>
                        <div class="card-image">
                            <?php $image = get_sub_field( 'image' ); ?>
                            <?php if ( $image ) : ?>
                            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                            <?php endif; ?>
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
    <!-- our categories -->
    <section class="our-categories">
        <div class="container">
            <div class="global-title text-center">
                <h2>Our Categories</h2>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="tab-main-cat">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <?php $count = 1; ?>
                                <?php if ( have_rows( 'our_categories' ) ) : ?>
                                <?php while ( have_rows( 'our_categories' ) ) : the_row(); ?>
                                <button class="nav-link <?php if($count == '1'){ echo 'active'; } ?>" id="nav-medication-tab<?php echo $count; ?>" data-bs-toggle="tab" data-bs-target="#medication<?php echo $count; ?>" type="button" role="tab" aria-controls="medication<?php echo $count; ?>" aria-selected="true"><?php the_sub_field( 'title' ); ?></button>
                                <?php $count = $count + 1; ?>
                                <?php endwhile; ?>
                                <?php else : ?>
                                <?php // No rows found ?>
                                <?php endif; ?>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <?php $count = 1; ?>
                            <?php if ( have_rows( 'our_categories' ) ) : ?>
                            <?php while ( have_rows( 'our_categories' ) ) : the_row(); ?>
                            <div class="tab-pane fade  <?php if($count == '1'){ echo 'active show'; } ?>" id="medication<?php echo $count; ?>" role="tabpanel" aria-labelledby="medication<?php echo $count; ?>">
                                <div class="madication-box">
                                    <div class="madication-img">
                                        <?php $tab_image = get_sub_field( 'tab_image' ); ?>
                                        <?php if ( $tab_image ) : ?>
                                        <img src="<?php echo esc_url( $tab_image['url'] ); ?>" alt="<?php echo esc_attr( $tab_image['alt'] ); ?>" />
                                        <?php endif; ?>
                                    </div>
                                    <div class="medication-content">
                                        <?php the_sub_field( 'tab_content' ); ?>
                                    </div>
                                </div>
                                <div class="row">

                                
                                    <?php if ( have_rows( 'tab_treatment' ) ) : ?>
                                    <?php while ( have_rows( 'tab_treatment' ) ) : the_row(); ?>
                                    <?php $treatment = get_sub_field( 'treatment' ); ?>
                                    
                                    <?php $postt = $treatment; ?>
                                    <?php setup_postdata( $postt ); 
                                   
                                    ?>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                                        <div class="each-treatment bg-grey-ee">
                                            <div class="each-treatment-img">
                                                <?php $icon = get_sub_field( 'icon' ); ?>
                                                <?php if ( $icon ) : ?>
                                                <img src="<?php echo esc_url( $icon['url'] ); ?>" alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                                <?php endif; ?>
                                            </div>
                                            <h5>
                                            <?php echo get_the_title($postt->ID); ?>
                                            </h5>
                                            <p><?php the_sub_field( 'description' ); ?></p>
                                            <div class="d-flex justify-content-end">
                                                <a href="<?php echo get_permalink($postt->ID); ?>" class="btn dark-btn">
                                                    Learn More
                                                </a>
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
    </section>
    <?php include 'template-part/faq-section.php'; ?>
    <?php include 'template-part/newsletter.php'; ?>
</div>

<?php get_footer(); ?>