<?php /* Template Name: Privacy */ ?>
<?php get_header(); ?>


    <div class="main-page">

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

        <section class="privacy-body-content">
            <div class="container">

            <?php if ( have_rows( 'privacy' ) ) : ?>
                <?php while ( have_rows( 'privacy' ) ) : the_row(); ?>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                        <div class="intro-title">
                            <h5><?php the_sub_field( 'title' ); ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-6 col-sm-12 col-xs-12">
                        <div class="intro-content">
                            <?php the_sub_field( 'content' ); ?>
                        </div>
                    </div>
                </div>
                <div class="terms-space"></div>
                <?php endwhile; ?>
<?php else : ?>
	<?php // No rows found ?>
<?php endif; ?>


                
            </div>
        </section>


<?php get_footer(); ?>