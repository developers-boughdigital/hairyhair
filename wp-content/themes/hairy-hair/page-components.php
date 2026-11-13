<?php
/**
 * Template Name: Page with Components
 * Description: Standard page content followed by ACF flexible content component sections.
 */

get_header(); ?>

<main class="page-with-components">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

        <?php if ( get_the_content() ) : ?>
        <section class="page-content-area">
            <div class="container">
                <?php the_content(); ?>
            </div>
        </section>
        <?php endif; ?>

        <?php get_template_part( 'template-part/components/flexible-content' ); ?>

    <?php endwhile; endif; ?>
    
</main>

<?php get_footer(); ?>
