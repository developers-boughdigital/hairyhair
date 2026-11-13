<?php
/* Template Name: Treatment Comparison Page */
get_header();

// Front-end template markup
$treatments = get_posts(array(
    'post_type'   => 'hair-treatments',
    'numberposts' => -1,
    'post_status' => 'publish',
));
?>
<div class="main-page compare-treat">
    <section class="main-banner-content">
        <div class="container">
            <div class="ban-left-content text-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Education</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Comparison Page</li>
                    </ol>
                </nav>
                <h1>Compare Hair Loss Treatments</h1>
                <p>Explore side-by-side comparisons of top hair loss treatments to find the best solution for your needs.</p>
            </div>
        </div>
    </section>

    <section class="treatments-comparison comparison-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="hair-care-providers-wrap">
                        <div class="search-bar">
                            <?php for ($i=1; $i<=4; $i++): ?>
                                <div class="search-field">
                                    <div class="icon">
                                        <span class="blue-dot<?php echo $i>1?' yellow-dot':'';?><?php echo $i>2?' green-dot':'';?><?php echo $i>3?' orange-dot':'';?>"></span>
                                    </div>
                                    <div class="field-info">
                                        <h6>Treatment <?php echo chr(64+$i); ?></h6>
                                        <select data-index="<?php echo $i; ?>" class="cat-dropdown">
                                            <option value="">Select Treatment</option>
                                            <?php foreach ($treatments as $t): ?>
                                                <option value="<?php echo $t->ID; ?>"><?php echo esc_html(get_the_title($t)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if ($i<4): ?><div class="divider"></div><?php endif; ?>
                            <?php endfor; ?>
                            <div class="contact-btn">
                                <button id="compare-btn" class="btn secondary-btn" disabled>Compare</button>
                            </div>
                        </div>
                    </div>
                    <div id="compare-results">

                    </div>
                    <div class="global-title text-center">
                        <h2>Treatment Comparisons</h2>
                    </div>
                    <?php
// 1. Fetch all hair‐treatments for the filters
$treatments = get_posts([
    'post_type'      => 'hair-treatments',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

// 2. Fetch all comparisons once
$all_comparisons = get_posts([
    'post_type'      => 'comparison',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

if ( $treatments ) : ?>
<div class="compare-tab-list-wrap">
    <div class="comapre-head-title">
        <h6>Compare:</h6>
        <div class="all-tabs">
            <!-- “All” button -->
            <button class="filter-btn is-active" data-filter="all">All Items</button>

            <!-- dynamic treatment buttons -->
            <?php foreach ( $treatments as $t ) :
                $slug  = sanitize_title( $t->post_name );  // e.g. "prp", "laser-therapy"
                $title = esc_html( get_the_title( $t ) );
            ?>
            <button
                class="filter-btn"
                data-filter="<?php echo esc_attr( $slug ); ?>">
                <?php echo $title; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="all-tabs-content">
        <div class="row">
            <?php foreach ( $all_comparisons as $c ) :
                // get the two treatment IDs
                $t1 = get_post_meta( $c->ID, 'treatment_1', true );
                $t2 = get_post_meta( $c->ID, 'treatment_2', true );

                // slugs for filtering
                $slug1 = $t1 ? sanitize_title( get_post( $t1 )->post_name ) : '';
                $slug2 = $t2 ? sanitize_title( get_post( $t2 )->post_name ) : '';

                // thumbnails (or empty string)
                
                $img1 = $t1 && has_post_thumbnail( $t1 )
                    ? get_the_post_thumbnail( $t1, 'large', [ 'class'=>'img-fluid' ] )
                    : '';
                $img2 = $t2 && has_post_thumbnail( $t2 )
                    ? get_the_post_thumbnail( $t2, 'large', [ 'class'=>'img-fluid' ] )
                    : '';
               
                               

                             
            ?>
            <div class="col-lg-6 col-md-6 col-sm-12 <?php echo esc_attr( "$slug1 $slug2" ); ?>">
                <div class="main-compare-box">
                    <div class="two-compare-img">
                        <div class="all-compare-img">
                            <?php
// Get the icon from treatment 1
$icon1 = $t1 ? get_field( 'comparison_icon', $t1 ) : null;
// Get the icon from treatment 2
$icon2 = $t2 ? get_field( 'comparison_icon', $t2 ) : null;
?>

<div class="left-img">
    <?php echo $img1; ?>
    <?php if ( $icon1 ) : ?>
        <div class="top-icon">
            <img
                class="img-fluid"
                src="<?php echo esc_url( $icon1['url'] ); ?>"
                alt="<?php echo esc_attr( $icon1['alt'] ?? '' ); ?>"
            />
        </div>
    <?php endif; ?>
</div>

<div class="left-img">
    <?php echo $img2; ?>
    <?php if ( $icon2 ) : ?>
        <div class="top-icon">
            <img
                class="img-fluid"
                src="<?php echo esc_url( $icon2['url'] ); ?>"
                alt="<?php echo esc_attr( $icon2['alt'] ?? '' ); ?>"
            />
        </div>
    <?php endif; ?>
</div>

                        </div>
                        <div class="vs-img">
                            <img
                                src="<?php echo esc_url( get_template_directory_uri() . '/assets/vs-img.png' ); ?>"
                                alt="vs"
                            >
                        </div>
                    </div>
                    <div class="compare-content">
                        <div class="left-title">
                            <h6><?php echo esc_html( get_the_title( $c ) ); ?></h6>
                            <p><?php echo esc_html( get_the_excerpt( $c ) ); ?></p>
                        </div>
                        <div class="btn-link">
                            <a href="<?php echo esc_url( get_permalink( $c ) ); ?>">Compare More</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
                    
                </div>
            </div>

            <div class="popular-compare-wrap">
                <div class="global-title">
                    <h2>Popular Comparisons</h2>
                </div>
                <div class="popular-comparisons bottom-arrows">

                        <?php
                        $args = array(
                            'post_type' => 'comparison',
                            'posts_per_page' => 5,
                        );

                        $comparison_query = new WP_Query($args);

                        if ($comparison_query->have_posts()) :
                            while ($comparison_query->have_posts()) : $comparison_query->the_post();

                                $treatment_1 = get_field('treatment_1');
                                $treatment_2 = get_field('treatment_2');

                                // Get icons from each treatment's ACF field
                                $icon1 = $treatment_1 ? get_field('comparison_icon', $treatment_1->ID) : null;
                                $icon2 = $treatment_2 ? get_field('comparison_icon', $treatment_2->ID) : null;
                                ?>
                                
                                <div class="treatment-slide">
                                    <div class="main-compare-box">
                                        <div class="two-compare-img">
                                            <div class="all-compare-img">

                                                <?php if ($treatment_1): ?>
                                                    <div class="left-img">
                                                        <img src="<?php echo get_the_post_thumbnail_url($treatment_1->ID, 'full'); ?>" alt="<?php echo esc_attr(get_the_title($treatment_1->ID)); ?>">
                                                        <?php if ($icon1): ?>
                                                            <div class="top-icon">
                                                                <img src="<?php echo esc_url($icon1['url']); ?>" alt="<?php echo esc_attr($icon1['alt'] ?? 'icon'); ?>" class="img-fluid">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($treatment_2): ?>
                                                    <div class="left-img">
                                                        <img src="<?php echo get_the_post_thumbnail_url($treatment_2->ID, 'full'); ?>" alt="<?php echo esc_attr(get_the_title($treatment_2->ID)); ?>">
                                                        <?php if ($icon2): ?>
                                                            <div class="top-icon">
                                                                <img src="<?php echo esc_url($icon2['url']); ?>" alt="<?php echo esc_attr($icon2['alt'] ?? 'icon'); ?>" class="img-fluid">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                            <div class="vs-img">
                                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/vs-img.png" alt="vs-img">
                                            </div>
                                        </div>
                                        <div class="compare-content">
                                            <div class="left-title">
                                                <h6><?php the_title(); ?></h6>
                                                <p>Procedure often combined with transplants and other treatments</p>
                                            </div>
                                            <div class="btn-link">
                                                <a href="<?php the_permalink(); ?>">Compare More</a>
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
                
            <div class="treatment-newsletter">
                <div class="rating-cta">
                    <?php the_field( 'comparison_ratings_content' ); ?>
                    <div class="take-quiz-btn">
                        <?php $comparison_ratings_button_1 = get_field( 'comparison_ratings_button_1' ); ?>
                            <?php if ( $comparison_ratings_button_1 ) : ?>
                                <a class="btn secondary-btn" href="<?php echo esc_url( $comparison_ratings_button_1['url'] ); ?>" target="<?php echo esc_attr( $comparison_ratings_button_1['target'] ); ?>"><?php echo esc_html( $comparison_ratings_button_1['title'] ); ?></a>
                            <?php endif; ?>

                            <?php $comparison_ratings_button_2 = get_field( 'comparison_ratings_button_2' ); ?>
                            <?php if ( $comparison_ratings_button_2 ) : ?>
                                <a class="btn dark-btn" href="<?php echo esc_url( $comparison_ratings_button_2['url'] ); ?>" target="<?php echo esc_attr( $comparison_ratings_button_2['target'] ); ?>"><?php echo esc_html( $comparison_ratings_button_2['title'] ); ?></a>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include 'template-part/faq-section.php'; ?>
    <?php include 'template-part/newsletter.php'; ?>
    
   
</div>

<!-- Put this after loading jQuery, e.g. in footer.php just before </body> -->
<script>
jQuery(function($){
    $('.filter-btn').on('click', function(e){
        e.preventDefault();
        var filter = $(this).data('filter');

        // 1) mark the active button
        $('.filter-btn').removeClass('is-active');
        $(this).addClass('is-active');

        // 2) show/hide boxes
        $('.all-tabs-content .row > div[class*="col-"]').each(function(){
            var $box = $(this);
            if ( filter === 'all' || $box.hasClass(filter) ) {
                $box.fadeIn(200);
            } else {
                $box.hide();
            }
        });
    });

    // 3) on load, trigger the default filter
    $('.filter-btn.is-active').first().trigger('click');
});
</script>

<?php
get_footer();

?>