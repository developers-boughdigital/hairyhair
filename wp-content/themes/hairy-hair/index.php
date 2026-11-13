<?php
get_header();
// Get the ID of the page set as the Posts page
$blog_page_id = get_option('page_for_posts');

$banner_title = get_field('banner_title', $blog_page_id);
$banner_description = get_field('banner_description', $blog_page_id);
$banner_button = get_field('banner_button', $blog_page_id);
?>
<style>
form.search-form {
    margin-bottom: 30px;
    display: flex;
    flex-direction: row;
}

.search-form input {
    width: 100%;
    border: 1px solid #d3d4d4;
    border-radius: 12px;
    margin-right: 20px;
    padding: 10px 15px;
}
</style>
<div class="main-page">
    <section class="main-banner-content faq-banner">
        <div class="container">
            <div class="ban-left-content text-center">
                <div class="content-wrapper">
                    <nav aria-label="breadcrumb">
                        <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">

                            <?php
                            if (function_exists('bcn_display')) {
                                bcn_display();
                            }
                            ?>

                        </div>
                    </nav>
                    <?php if ($banner_title): ?>
                    <h1><?php echo esc_html($banner_title); ?></h1>
                    <?php endif; ?>
                    <?php if ($banner_description): ?>
                    <?php echo wp_kses_post($banner_description); ?>
                    <?php endif; ?>
                    <?php if ($banner_button): ?>
                    <div class="contact-btn">
                        <a class="btn secondary-btn" href="<?php echo esc_url($banner_button['url']); ?>"
                            target="<?php echo esc_attr($banner_button['target']); ?>">
                            <?php echo esc_html($banner_button['title']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <section class="form-search-section">
        <div class="container">
            <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                <input type="search" class="search-field" placeholder="Search..."
                    value="<?php echo get_search_query(); ?>" name="s" />
                <button type="submit" class="search-submit btn secondary-btn">Search</button>
            </form>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2> Editorial Spotlight</h2>


            </div>

            <div class="row">
                <?php
                // Query the 3 most‐recent posts in the "feeling-confident" category
                $spotlight_args = [
                    'posts_per_page' => 3,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish'
                ];
                $recent_loop = new WP_Query($spotlight_args);

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
        </div>
    </section>
    <section class="popular-article">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Popular Articles</h2>
                <p>Stay on top of trending stories, viral moments, and the biggest streaming buzz—updated in real-time
                    based on what’s making waves.</p>
                <a href="#">
                    <!-- <img src="assets/arrow-down.png" alt="arrow"> -->
                    See All
                </a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows">
                <?php
                // Query the 10 most–commented (popular) posts
                $popular_args = [
                    'posts_per_page' => 10,
                    'orderby' => 'comment_count',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $popular_loop = new WP_Query($popular_args);

                if ($popular_loop->have_posts()) :
                    while ($popular_loop->have_posts()) : $popular_loop->the_post();
                        ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('square-300', ['alt' => get_the_title()]); ?>
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
                            <p><?php echo wp_trim_words(get_the_excerpt(), 20, '…'); ?></p>
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
        $in_content_11 = do_shortcode( '[hh_campaign_widget slot="In Content 11"]' ); 
        if($in_content_11 != '') {
            echo "<div style='text-align: center; margin-bottom: 100px;'>".$in_content_11.'</div>';
        }
    ?>

    <section class="popular-article">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h2>Latest News & Articles</h2>
                    <p>Stay updated with the latest haircare tips, expert insights, and real results from Hairy Hair—all in one place.</p>
                </div>
                <a href="#">See All</a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows">
                <?php
                // Query the 10 most recent posts
                $latest_args = [
                    'posts_per_page' => 10,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $latest_loop = new WP_Query($latest_args);

                if ($latest_loop->have_posts()) :
                    while ($latest_loop->have_posts()) : $latest_loop->the_post();
                        ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>">
                                <img src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')); ?>"
                                    alt="<?php echo the_title_attribute(['echo' => false]); ?>">
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
                            <p><?php echo wp_trim_words(get_the_excerpt(), 20, '…'); ?></p>
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

    <section class="category-wrap">
        <div class="container">
            <div class="global-title text-center">
                <h2>Category-Specific</h2>
                <p>Stay on top of trending stories, viral moments, and the biggest streaming buzz—updated in real-time
                    based on what’s making waves.</p>
            </div>
        </div>
    </section>

    <section class="white-radius-bg category-specific">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Hair Loss Causes</h2>
                <a href="/category/hair-loss-causes/">See All</a>
            </div>
            <div class="row">
                <?php
                    // Query the 3 most‐recent posts in the "feeling-confident" category
                $recent_args = [
                    'posts_per_page' => 3,
                    'category_name' => 'hair-loss-causes',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $recent_loop = new WP_Query($recent_args);

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
        </div>
    </section>


    <section class="popular-article">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Ingredients</h2>
                <a href="/category/ingredients/">See All</a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows ingredients ingredients">

                <?php
            // Query the single most‐recent post in the "ingredients" category
                $latest_args = [
                    'posts_per_page' => 10,
                    'category_name' => 'ingredients',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $latest_loop = new WP_Query($latest_args);

                if ($latest_loop->have_posts()) :
                    while ($latest_loop->have_posts()) : $latest_loop->the_post();
                        ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <img src="<?php
                                        echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/testi-man.png';
                                        ?>" alt="blog-1">
                            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                            </div>
                        </div>
                        <div class="blog-desc">
                            <a class="title-blog-main" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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

    <section class="white-radius-bg category-specific">
        <div class="wrapper blog-list">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Treatments</h2>
                    <a href="/category/treatment/">See All</a>
                </div>
                <div class="row">

                    <?php
// Query the 4 most‐recent posts in the “treatment” category
                    $recent_args = [
                        'posts_per_page' => 4,
                        'category_name' => 'treatment',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ];
                    $recent_loop = new WP_Query($recent_args);

                    if ($recent_loop->have_posts()) :
                        while ($recent_loop->have_posts()) : $recent_loop->the_post();
                            ?>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="all-blogs">
                            <div class="each-blog">
                                <div class="each-blog-left">
                                    <img src="<?php
                                            echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/about-img-1.jpg';
                                            ?>" alt="blog image">
                                    <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                        <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                    </div>
                                </div>
                                <div class="each-blog-right">
                                    <div class="each-blog-top">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                </div>
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
        </div>
    </section>

    <?php 
        $in_content_12 = do_shortcode( '[hh_campaign_widget slot="In Content 12"]' ); 
        if($in_content_12 != '') {
            echo "<div style='text-align: center; margin-bottom: 70px;'>".$in_content_12.'</div>';
        }
    ?>

    <section class="popular-article">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Looking Good</h2>
                <a href="/category/looking-good/">See All</a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows ingredients">
                <?php
                // Query the 10 most‐recent posts in the "looking-good" category
                $recent_args = [
                    'posts_per_page' => 10,
                    'category_name' => 'looking-good',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $recent_loop = new WP_Query($recent_args);

                if ($recent_loop->have_posts()) :
                    while ($recent_loop->have_posts()) : $recent_loop->the_post();
                        ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <img src="<?php
                                        echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/testi-man.png';
                                        ?>" alt="blog-1">
                            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                            </div>
                        </div>
                        <div class="blog-desc">
                            <a class="title-blog-main" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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

    <section class="white-radius-bg category-specific">
        <div class="wrapper blog-list">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Grooming</h2>
                    <a href="/category/grooming/">See All</a>
                </div>
                <div class="row">
                    <?php
// Query the 4 most‐recent posts in the “grooming” category
                    $recent_args = [
                        'posts_per_page' => 4,
                        'category_name' => 'grooming',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ];
                    $recent_loop = new WP_Query($recent_args);

                    if ($recent_loop->have_posts()) :
                        while ($recent_loop->have_posts()) : $recent_loop->the_post();
                            ?>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="all-blogs">
                            <div class="each-blog">
                                <div class="each-blog-left">
                                    <img src="<?php
                                                echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/about-img-1.jpg';
                                                ?>" alt="blog image">
                                    <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                        <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                    </div>
                                </div>
                                <div class="each-blog-right">
                                    <div class="each-blog-top">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                </div>
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
        </div>
    </section>

    <section>
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Feeling Confident</h2>
                <a class="btn secondary-btn" href="/category/feeling-confident/">See All</a>
            </div>
            <div class="row">
                <?php
// Query the 3 most‐recent posts in the "feeling-confident" category
                $recent_args = [
                    'posts_per_page' => 3,
                    'category_name' => 'feeling-confident',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $recent_loop = new WP_Query($recent_args);

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
        </div>
    </section>



    <?php 
        $in_content_13 = do_shortcode( '[hh_campaign_widget slot="In Content 13"]' ); 
        if($in_content_13 != '') {
            echo "<div style='text-align: center; margin-bottom: 70px;'>".$in_content_13.'</div>';
        }
    ?>

    <section class="white-radius-bg category-specific">
        <div class="wrapper blog-list">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Myths & Facts</h2>
                    <a href="/category/myths-facts/">See All</a>
                </div>
                <div class="row">
                    <?php
// Query the 4 most‐recent posts in the “myths-facts” category
                    $recent_args = [
                        'posts_per_page' => 4,
                        'category_name' => 'myths-facts',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ];
                    $recent_loop = new WP_Query($recent_args);

                    if ($recent_loop->have_posts()) :
                        while ($recent_loop->have_posts()) : $recent_loop->the_post();
                            ?>

                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="all-blogs">
                            <div class="each-blog">
                                <div class="each-blog-left">
                                    <img src="<?php
                                                echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/about-img-1.jpg';
                                                ?>" alt="blog image">
                                    <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                        <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                    </div>
                                </div>
                                <div class="each-blog-right">
                                    <div class="each-blog-top">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                </div>
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
        </div>
    </section>

    <section class="popular-article right-fluid-side">
        <div class="container">
            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <h2>Science</h2>
                <a href="/category/science/">See All</a>
            </div>
        </div>
        <div class="container">
            <div class="all-popular-article bottom-arrows ingredients">
                <?php
// Query the 10 most‐recent posts in the “science” category
                $recent_args = [
                    'posts_per_page' => 10,
                    'category_name' => 'science',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'post_status' => 'publish',
                ];
                $recent_loop = new WP_Query($recent_args);

                if ($recent_loop->have_posts()) :
                    while ($recent_loop->have_posts()) : $recent_loop->the_post();
                        ?>
                <div class="each-article">
                    <div class="latest-blog">
                        <div class="blog-img">
                            <img src="<?php
                                        echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/testi-man.png';
                                        ?>" alt="blog-1">
                            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                            </div>
                        </div>
                        <div class="blog-desc">
                            <a class="title-blog-main" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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

    <section class="white-radius-bg category-specific">
        <div class="wrapper blog-list pb-0">
            <div class="container">
                <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                    <h2>Symptoms</h2>
                    <a href="/category/symptoms/">See All</a>
                </div>
                <div class="row">
                    <?php
                    // Query the 4 most‐recent posts in the "symptoms" category
                    $recent_args = [
                        'posts_per_page' => 4,
                        'category_name' => 'symptoms',
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                    ];
                    $recent_loop = new WP_Query($recent_args);

                    if ($recent_loop->have_posts()) :
                        while ($recent_loop->have_posts()) : $recent_loop->the_post();
                            ?>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="all-blogs">
                            <div class="each-blog">
                                <div class="each-blog-left">
                                    <img src="<?php
                                                echo has_post_thumbnail() ? esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium_large')) : get_template_directory_uri() . '/assets/about-img-1.jpg';
                                                ?>" alt="blog image">
                                    <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                                        <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                    </div>
                                </div>
                                <div class="each-blog-right">
                                    <div class="each-blog-top">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    endif;
                    ?>

                </div>
                <section class="white-radius-bg">
                    <div class="wrapper  recent-news pb-0 ">
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
                                            <img src="<?php the_post_thumbnail_url('full'); ?>"
                                                alt="<?php the_title(); ?>">
                                            <?php else : ?>
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png"
                                                alt="blog-1">
                                            <?php endif; ?>
                                            <div
                                                class="button-learn d-flex flex-wrap justify-content-center align-center">
                                                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
                                            </div>
                                        </div>
                                        <div class="blog-desc">
                                            <a class="title-blog-main"
                                                href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            <p><?php the_excerpt(); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>


    <section class="faq-wrap">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 col-md-5 col-12">
                    <div class="global-title">
                        <?php the_field('faq_content', 574); ?>
                        <?php $faq_button = get_field('faq_button', 574); ?>
                        <?php if ($faq_button) : ?>
                        <a class="btn secondary-btn" href="<?php echo esc_url($faq_button['url']); ?>"
                            target="<?php echo esc_attr($faq_button['target']); ?>"><?php echo esc_html($faq_button['title']); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-7 col-md-7 col-12">
                    <div class="all-faq">
                        <div class="container">
                            <div class="accordion" id="accordionExample">
                                <?php $count = 1; ?>
                                <?php if (have_rows('faq', 574)) : ?>
                                <?php while (have_rows('faq', 574)) : the_row(); ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?php
                                                if ($count != '1') {
                                                    echo 'collapsed';
                                                }
                                                ?>" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseOne<?php echo $count; ?>" aria-expanded="true"
                                            aria-controls="collapseOne<?php echo $count; ?>">
                                            <?php the_sub_field('title'); ?>
                                        </button>
                                    </h2>
                                    <div id="collapseOne<?php echo $count; ?>" class="accordion-collapse collapse <?php
                                            if ($count == '1') {
                                                echo 'show';
                                            }
                                            ?>" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <?php the_sub_field('content'); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $count = $count + 1; ?>
                                <?php endwhile; ?>
                                <?php else : ?>
                                <?php // No rows found   ?>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 
    <section class="newsletter mb-0  center-text">
        <div class="container">

            <div class="bg-primary " >
                <div class="news-wrap">
                    <?php the_field('newsletter_title', 574); ?>
                    <ul class="tags">
                        <?php if (have_rows('newsletter_tags', 574)) : ?>
                            <?php while (have_rows('newsletter_tags', 574)) : the_row(); ?>
                                <li>
                                    <a href="<?php the_sub_field('link'); ?>"><?php the_sub_field('title'); ?></a>
                                </li>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <?php // No rows found  ?>
                        <?php endif; ?>
                    </ul>

                    <?php $newsletter_button = get_field('newsletter_button', 574); ?>
                    <?php if ($newsletter_button) : ?>
                        <a class="btn secondary-btn d-flex m-auto" href="<?php echo esc_url($newsletter_button['url']); ?>" target="<?php echo esc_attr($newsletter_button['target']); ?>"><?php echo esc_html($newsletter_button['title']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section> -->

</div>

<?php get_footer(); ?>