<?php
/**
 * Component: Posts Grid (Resource Hub)
 *
 * ACF Flexible Content layout name: posts_grid
 *
 * Fields:
 *   title         (text)    Section heading
 *   subtitle      (textarea) Optional intro text below the heading
 *   columns       (select)  2 | 3 | 4   default: 3
 *   limit         (number)  default: 3
 *   category      (text)    Post category slug(s), comma-separated. Leave empty for all.
 *   post_ids      (text)    Comma-separated post IDs (overrides category when set)
 *   orderby       (select)  date | title | rand | menu_order  default: date
 *   show_excerpt  (true_false) default: 1
 *   show_date     (true_false) default: 0
 *   show_category (true_false) default: 0
 *   read_more_text (text)   default: 'Read More'
 *   bg_color      (color)   default: #ffffff
 *   padding       (text)    default: 60px 0
 *   text_align    (select)  left | center   default: left
 */

$title          = get_sub_field( 'title' );
$subtitle       = get_sub_field( 'subtitle' );
$columns        = (int) ( get_sub_field( 'columns' ) ?: 3 );
$limit          = (int) ( get_sub_field( 'limit' ) ?: 3 );
$category       = get_sub_field( 'category' );
$post_ids_raw   = get_sub_field( 'post_ids' );
$orderby        = get_sub_field( 'orderby' ) ?: 'date';
$show_excerpt   = get_sub_field( 'show_excerpt' );
$show_date      = get_sub_field( 'show_date' );
$show_category  = get_sub_field( 'show_category' );
$read_more_text = get_sub_field( 'read_more_text' ) ?: 'Read More';
$bg_color       = get_sub_field( 'bg_color' ) ?:  'transparent';
$padding        = get_sub_field( 'padding' ) ?: '60px 0';
$text_align     = get_sub_field( 'text_align' ) ?: 'left';

// Bootstrap column sizes
$bs_col   = 12 / $columns;
$col_cls  = 'col-lg-' . $bs_col . ' col-md-6 col-12';

// WP_Query args
$args = [
    'post_type'      => 'post',
    'posts_per_page' => $limit,
    'post_status'    => 'publish',
    'orderby'        => $orderby,
    'order'          => ( $orderby === 'date' ) ? 'DESC' : 'ASC',
];

if ( $post_ids_raw ) {
    $args['post__in'] = array_map( 'intval', explode( ',', $post_ids_raw ) );
    $args['orderby']  = 'post__in';
} elseif ( $category ) {
    $args['category_name'] = $category;
}

$posts_query = new WP_Query( $args );
?>
<section class="component-posts-grid"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $title ) : ?>
        <div class="pg-header global-title text-<?php echo esc_attr( $text_align ); ?>">
            <?php if ( $title ) : ?>
                <h2 class="pg-title "><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
         
        </div>
        <?php endif; ?>

        <?php if ( $posts_query->have_posts() ) : ?>
        <div class="row g-4">
            <?php while ( $posts_query->have_posts() ) : $posts_query->the_post(); ?>
            <div class="<?php echo esc_attr( $col_cls ); ?>">
                <article class="pg-card">

                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" class="pg-card-img-link" tabindex="-1">
                        <div class="pg-card-img-wrap">
                            <?php the_post_thumbnail( 'large', [ 'class' => 'pg-card-img' ] ); ?>
                        </div>
                    </a>
                    <?php endif; ?>

                    <div class="pg-card-body">

                        <?php if ( $show_category ) :
                            $cats = get_the_category();
                            if ( $cats ) : ?>
                            <span class="pg-card-cat"><?php echo esc_html( $cats[0]->name ); ?></span>
                        <?php endif; endif; ?>

                        <h3 class="pg-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <?php if ( $show_excerpt ) : ?>
                        <p class="pg-card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></p>
                        <?php endif; ?>

                        <?php if ( $show_date ) : ?>
                        <span class="pg-card-date"><?php echo get_the_date(); ?></span>
                        <?php endif; ?>

                    </div><!-- .pg-card-body -->

                </article>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php else : ?>
            <p class="text-muted"><?php esc_html_e( 'No posts found.', 'hairy-hair' ); ?></p>
        <?php endif; ?>

    </div>
</section>
