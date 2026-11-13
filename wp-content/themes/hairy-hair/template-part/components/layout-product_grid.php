<?php
/**
 * Component: Product Grid
 *
 * ACF Flexible Content layout name: product_grid
 *
 * Fields:
 *   title        (text)
 *   columns      (select)  2 | 3 | 4   default: 4
 *   limit        (number)  default: 4
 *   category     (text)    WooCommerce product category slug (optional)
 *   product_ids  (text)    Comma-separated product IDs (optional, overrides category)
 *   orderby      (select)  menu_order | date | price | popularity | rand  default: menu_order
 *   bg_color     (color)   default: #ffffff
 *   badge_label  (text)    default: 'Prescriptive'
 *   badge_color  (color)   default: #28a745
 */

$title       = get_sub_field( 'title' );
$columns     = (int) ( get_sub_field( 'columns' ) ?: 4 );
$limit       = (int) ( get_sub_field( 'limit' ) ?: 4 );
$category    = get_sub_field( 'category' );
$product_ids = get_sub_field( 'product_ids' );
$orderby     = get_sub_field( 'orderby' ) ?: 'menu_order';
$bg_color    = get_sub_field( 'bg_color' ) ?: '#ffffff';
$badge_label = get_sub_field( 'badge_label' ) ?: '';
$badge_color = get_sub_field( 'badge_color' ) ?: '#28a745';

// Bootstrap col class
$bs_col  = 12 / $columns;
$col_cls = 'col-lg-' . $bs_col . ' col-md-6 col-sm-6 col-12';

// Build WP_Query args
$args = [
    'post_type'      => 'product',
    'posts_per_page' => $limit,
    'post_status'    => 'publish',
    'orderby'        => $orderby,
    'order'          => 'ASC',
];

if ( $product_ids ) {
    $args['post__in'] = array_map( 'intval', explode( ',', $product_ids ) );
    $args['orderby']  = 'post__in';
} elseif ( $category ) {
    $args['tax_query'] = [ [
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => array_map( 'trim', explode( ',', $category ) ),
    ] ];
}

$products = new WP_Query( $args );
?>
<section class="component-product-grid" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:60px 0;">
    <div class="container">

        <?php if ( $title ) : ?>
            <h2 class="pg-section-title fw-bold mb-4"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>

        <?php if ( $products->have_posts() ) : ?>
        <div class="row g-4">
            <?php while ( $products->have_posts() ) : $products->the_post();
                global $product;
                $product      = wc_get_product( get_the_ID() );
                $thumb_id     = $product->get_image_id();
                $thumb_url    = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();
                $thumb_alt    = $thumb_id ? get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) : get_the_title();
                $short_desc   = $product->get_short_description();
                $price_html   = $product->get_price_html();
                $add_to_cart  = wc_get_cart_url();
                $product_url  = get_permalink();
            ?>
            <div class="<?php echo esc_attr( $col_cls ); ?>">
                <div class="pg-card h-100 d-flex flex-column">

                    <!-- Image -->
                    <a href="<?php echo esc_url( $product_url ); ?>" class="pg-card-img-wrap d-block position-relative">
                        <?php if ( $badge_label ) : ?>
                            <span class="pg-badge"
                                  style="background-color:<?php echo esc_attr( $badge_color ); ?>;">
                                <?php echo esc_html( $badge_label ); ?>
                            </span>
                        <?php endif; ?>
                        <img src="<?php echo esc_url( $thumb_url ); ?>"
                             alt="<?php echo esc_attr( $thumb_alt ); ?>"
                             class="pg-card-img" />
                    </a>

                    <!-- Body -->
                    <div class="pg-card-body flex-grow-1 d-flex flex-column">
                        <a href="<?php echo esc_url( $product_url ); ?>" class="pg-card-title">
                            <?php the_title(); ?>
                        </a>

                        <?php if ( $short_desc ) : ?>
                            <p class="pg-card-desc"><?php echo wp_kses_post( $short_desc ); ?></p>
                        <?php endif; ?>

                        <?php if ( $price_html ) : ?>
                            <div class="pg-card-price mt-auto"><?php echo $price_html; ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Add to Cart -->
                    <div class="pg-card-footer">
                        <?php
                        $atc_url = $product->is_type( 'simple' )
                            ? esc_url( wc_get_cart_url() . '?add-to-cart=' . $product->get_id() )
                            : esc_url( $product_url );
                        ?>
                        <a href="<?php echo $atc_url; ?>"
                           class="btn pg-atc-btn w-100">
                            <?php echo $product->is_type( 'simple' ) ? esc_html__( 'Buy Now', 'hairy-hair' ) : esc_html__( 'Select Options', 'hairy-hair' ); ?>
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <?php endif; ?>

    </div>
</section>
