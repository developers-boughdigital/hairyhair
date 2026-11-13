<?php get_header(); ?>


    <div class="main-page">
        <section class="blog-artical-header mb-0">
            <div class="container">
                <div class="blog-artical-head">
                    <nav aria-label="breadcrumb">
                        <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                            
                            <?php if (function_exists('bcn_display')) {
                            bcn_display();
                            } ?>
                            
                        </div>
                    </nav>
                    <h1 class="heading2"><?php echo the_title();?>
                    </h1>
                </div>
            </div>
        </section>
        <section class="blog-artical-page">
            <div class="container">
                <div class="d-flex blog-artical-inner">
                    <div class="blog-artical-details">
                        <div class="blog-artical-img">
                            <img src="<?php echo get_the_post_thumbnail_url();?>" alt="<?php echo the_title();?>"/>
                        </div>
                        <div class="author-box">
                            <div class="author-details">
                            <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
                                <div class="author-name">
                                    <p>Author</p>
                                    <h6><?php the_author();?></h6>
                                </div>
                            </div>
                            <?php $verified_by = get_field('verified_by'); ?>
                                <?php if ($verified_by): ?>
                            <div class="author-details">
                                <div class="author-name">
                                    <p>Verified by</p>
                                    <h6><?php echo esc_html($verified_by['display_name']); ?></h6>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="author-details">
                                <div class="author-name">
                                    <p>Published on</p>
                                    <h6><?php echo the_date();?></h6>
                                </div>
                            </div>
                            <?php $reading_time = get_field('reading_time'); ?>
                                <?php if ($reading_time): ?>
                            <div class="author-details">            
                                <div class="author-name">
                                    <p>Est. Reading Time</p>
                                    <h6><?php echo esc_html($reading_time); ?></h6>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="blog-artical-content">
                            <?php echo the_content();?>
                        </div>
                    </div>
                    <div class="blog-artical-post">
                        <div class="sticky-right-bar">
                            <div class="left-side">
                                <div class="table-cont-heading">
                                    <h4>In This Article</h4><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M13.9084 4.53538L9.07768 13.4526L15.3925 11.5756L15.862 13.1757L6.82468 15.862L4.13839 6.82473L5.73122 6.35127L7.61223 12.6588L12.443 3.7415L13.9084 4.53538Z" fill="black"></path> </svg>
                                </div>
                                <h3>Overview</h3>
                                <ol id="toc-list" class="blog_details_left_ul">
                                
                                </ol>
                            </div>
                            <div class="post-articles">
                                <div class="recent-post-title">
                                    <h4>Most Trending Articles</h4>
                                </div>
                                <div class="recent-posts-loop">
                                <?php
                                    // Query the single “most trending” post (here approximated by highest comment count)
                                    $trending_args = [
                                        'posts_per_page' => 3,
                                        'orderby'        => 'comment_count',
                                        'order'          => 'DESC',
                                        'post_status'    => 'publish',
                                    ];
                                    $trending_loop = new WP_Query( $trending_args );

                                    if ( $trending_loop->have_posts() ) :
                                        while ( $trending_loop->have_posts() ) : $trending_loop->the_post();
                                    ?>
                                        <div class="post-item">
                                        <a href="<?php the_permalink(); ?>"
                                            title="post-item-img"
                                            class="post-item-img">
                                            <?php if ( has_post_thumbnail() ) : ?>
                                            <?php the_post_thumbnail( 'thumbnail', [
                                                'alt'   => get_the_title(),
                                                'class' => 'post-thumb--small'
                                            ] ); ?>
                                            <?php else : ?>
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/post-item-01.png"
                                                alt="post-item-img"
                                                class="post-thumb--small">
                                            <?php endif; ?>
                                        </a>
                                        <figcaption>
                                            <span><?php echo get_the_date( 'F j, Y' ); ?></span>
                                            <a href="<?php the_permalink(); ?>" title="post-item-link">
                                            <?php the_title(); ?>
                                            </a>
                                        </figcaption>
                                        </div>

                                    <?php
                                        endwhile;
                                        wp_reset_postdata();
                                    endif;
                                    ?>

                                </div>
                            </div>

                            

                            <?php 
                                $sidebar_promo = do_shortcode( '[hh_campaign_widget slot="Sidebar Promo"]' ); 
                                if($sidebar_promo != '') {
                                    echo "<div style='text-align: center; margin-bottom: 70px;'>".$sidebar_promo.'</div>';
                                }
                            ?> 
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="newsletter mb-0  center-text d-none">
            <div class="container">
            
                <div class="bg-primary " >
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
                <a class="btn secondary-btn d-flex m-auto" href="<?php echo esc_url( $newsletter_button['url'] ); ?>" target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><?php echo esc_html( $newsletter_button['title'] ); ?></a>
            <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <?php get_footer(); ?>