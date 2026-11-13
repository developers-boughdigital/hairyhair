<?php get_header(); ?>


<?php
$category = get_queried_object(); // Current category term object
$banner_title = get_field('banner_title', 'category_' . $category->term_id);
$banner_description = get_field('banner_description', 'category_' . $category->term_id);
$banner_button = get_field('banner_button', 'category_' . $category->term_id);
$banner_image = get_field('banner_image', 'category_' . $category->term_id);
?>

<div class="main-page">
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
                            <?php if ($banner_title): ?>
                            <h1><?php echo esc_html($banner_title); ?></h1>
                            <?php endif; ?>
                            <?php if ($banner_description): ?>
                            <?php echo wp_kses_post($banner_description); ?>
                            <?php endif; ?>
                            <div class="contact-btn">

                                <?php if ($banner_button): ?>
                                <a class="btn secondary-btn" href="<?php echo esc_url($banner_button['url']); ?>"
                                    target="<?php echo esc_attr($banner_button['target']); ?>">
                                    <?php echo esc_html($banner_button['title']); ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <div class="ban-right-img">
                        <?php if ($banner_image): ?>
                        <img src="<?php echo esc_url($banner_image['url']); ?>"
                            alt="<?php echo esc_attr($banner_image['alt']); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="blog-list">
        <div class="container">
            <div class="row">
                <?php
                    // Query the 3 most‐recent posts in the "feeling-confident" category
                    $args = array(
                        'post_type' => 'post',
                        'posts_per_page' => 3, // Only get 1 post
                        'category_name' => $category->slug, // Make sure we get posts from the current category by slug
                        'orderby' => 'date', // Most recent first
                        'order' => 'DESC'
                    );
                $recent_loop = new WP_Query($args);

                if ($recent_loop->have_posts()) :
                    while ($recent_loop->have_posts()) : $recent_loop->the_post();
                        ?>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <img src="<?php
                                        echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/testi-man.png';
                                        ?>" alt="blog-1">
                            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                <a href="<?php the_permalink(); ?>" class="btn dark-btn" tabindex="0">Learn More</a>
                            </div>
                        </div>
                        <div class="blog-desc">
                            <a class="title-blog-main" href="<?php the_permalink(); ?>"
                                tabindex="0"><?php the_title(); ?></a>
                        </div>
                    </div>
                </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>
    </section>

    <section class="secondary-links bg-dark">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Explore More Topics</h2>
                <p>Discover expert insights across our blog. Dive deeper into each area to find tailored advice, helpful
                    tips, and the latest updates to support your hair health journey.</p>
            </div>
        </div>
        <div class="container">
            <div class="all-category bottom-arrows">
                <?php
$current_cat = get_queried_object(); // current category object
$categories = get_categories([
    'exclude' => [$current_cat->term_id],
    'hide_empty' => true
]);

foreach ($categories as $category) :
    $icon = get_field('icon', 'category_' . $category->term_id); // ACF image field
    $icon_url = $icon['url'] ?? '';
    $icon_alt = $icon['alt'] ?? $category->name;
    $link = get_category_link($category->term_id);
    ?>
                <div class="each-category">
                    <a href="<?php echo esc_url($link); ?>">
                        <?php if ($icon_url): ?>
                        <img src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($icon_alt); ?>" width="80"
                            height="80" />
                        <?php else: ?>
                        <!-- Optional: fallback SVG or image -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none">
                            <circle cx="18" cy="18" r="18" fill="#ccc" />
                        </svg>
                        <?php endif; ?>
                        <p><?php echo esc_html($category->name); ?></p>
                    </a>
                </div>
                <?php endforeach; ?>

            </div>
        </div>
    </section>

    <section class="popular-article">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Popular News & Articles</h2>
                <a href="#" class="btn secondary-btn">See All</a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows">
                <?php
                    // Query the 5 most–commented (popular) posts
                    $popular_args = [
                        'posts_per_page' => 10,
                        'orderby'        => 'comment_count',
                        'order'          => 'DESC',
                        'post_status'    => 'publish',
                    ];
                    $popular_loop = new WP_Query( $popular_args );

                    if ( $popular_loop->have_posts() ) :
                        while ( $popular_loop->have_posts() ) : $popular_loop->the_post();
                    ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium_large', [ 'alt' => get_the_title() ] ); ?>
                            </a>
                            <?php else : ?>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png" alt="blog-1">
                            <?php endif; ?>

                            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                            </div>
                        </div>

                        <div class="blog-desc">
                            <a class="title-blog-main" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            <p><?php echo wp_trim_words( get_the_excerpt(), 20, '…' ); ?></p>


                        </div>
                    </div>
                </div>
                <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>

            </div>
        </div>
    </section>



    <?php 
        $in_content_14 = do_shortcode( '[hh_campaign_widget slot="In Content 14"]' ); 
        if($in_content_14 != '') {
            echo "<div style='text-align: center; margin-bottom: 100px;'>".$in_content_14.'</div>';
        }
    ?>
    <section class="white-radius-bg">

        <div class="wrapper  recent-news">
            <div class="container">
                <div class="mb-4">
                    <div class="global-title">
                        <h2>Recent News</h2>
                    </div>
                    <div class="news-tags">

                        <div class="left-side">
                            <?php
                        // Display all categories for filtering
                        $categories = get_categories();
                        foreach ($categories as $category) {
                            echo '<a href="#" class="category-filter" data-category-id="' . $category->term_id . '">' . $category->name . '</a>';
                        }
                        ?>

                        </div>
                        <div class="right-side">
                            <div class="sort-container">
                                <span class="sort-label">Sort by:</span>
                                <select class="form-select" aria-label="Sort by select" id="sort-filter">
                                    <option value="date" selected>Most Recent</option>
                                    <option value="oldest">Oldest</option>
                                    <option value="popular">Most Popular</option>
                                    <option value="highest_rated">Highest Rated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="latest-posts" class="row">
                    <?php
            // Initial post loop based on the current category
            if (have_posts()) :
                while (have_posts()) : the_post();
                    ?>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="latest-blog">
                            <div class="blog-img">
                                <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title(); ?>">
                                <?php else : ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png"
                                    alt="blog-1">
                                <?php endif; ?>
                                <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                    <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                </div>
                            </div>
                            <div class="blog-desc">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <p><?php the_excerpt(); ?></p>

                                <div class="tags">
                                    <?php the_category(' '); ?>
                                </div>
                                <div class="writer">
                                    <div class="left-side">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png"
                                            alt="test1">
                                        <p><?php the_author(); ?></p>
                                    </div>
                                    <div class="right-side">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/clock.png"
                                            alt="clock">
                                        <p><?php the_time('F j, Y'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="newsletter mb-0  center-text d-none">
        <div class="container">

            <div class="bg-primary ">
                <div class="news-wrap">
                    <?php the_field( 'newsletter_title', 'option' ); ?>
                    <ul class="tags">
                        <?php if ( have_rows( 'newsletter_tags', 'option' ) ) : ?>
                        <?php while ( have_rows( 'newsletter_tags', 'option' ) ) : the_row(); ?>
                        <li>
                            <a href="<?php the_sub_field( 'link' ); ?>"><?php the_sub_field( 'title' ); ?></a>
                        </li>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <?php // No rows found ?>
                        <?php endif; ?>
                    </ul>

                    <?php $newsletter_button = get_field( 'newsletter_button', 'option' ); ?>
                    <?php if ( $newsletter_button ) : ?>
                    <a class="btn secondary-btn d-flex m-auto"
                        href="<?php echo esc_url( $newsletter_button['url'] ); ?>"
                        target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><?php echo esc_html( $newsletter_button['title'] ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>


<?php get_footer(); ?>