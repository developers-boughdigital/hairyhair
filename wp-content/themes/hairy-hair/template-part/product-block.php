<?php 
// Ensure session is started before any output (called from header, safe here)
if ( session_status() === PHP_SESSION_NONE ) {
    session_start();
}
$acess_to_add_prescriptive_products = $_SESSION['acess_to_add_prescriptive_products'] ?? false;

// thumbnail URL fallback
$thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'medium' ) : wc_placeholder_img_src();
// stock and price
$in_stock = $product->is_in_stock();
$product_type = get_field( 'product_type', $product->get_id() );
$badge_class = ( 'Prescriptive' === $product_type )
                ? 'badge-green-bg'
                : 'badge-purple-bg';
$qty      = $product->managing_stock() ? $product->get_stock_quantity() : '';
$price    = $product->get_price_html();
// excerpt
$excerpt  = wp_trim_words( get_the_excerpt(), 12, '…' );
?>
<div class="col-lg-3 col-md-6 col-12" 
    data-price="<?php echo esc_attr( $product->get_price() ); ?>"
  data-rating="<?php echo esc_attr( round( $product->get_average_rating() ) ); ?>"
  data-prescription="<?php echo esc_attr( strtolower($product_type) ); ?>">
    
    <div class="product-slide">
        <div class="product-image">
            <?php //echo $product->get_id(); ?>
            <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>">
            </a>
            <?php 
                if($product_type != 'Normal' && $product_type != '' ){
                ?>
                <div class="product-badge <?php echo esc_attr( $badge_class ); ?>">
                <span><?php echo esc_html( $product_type ); ?></span>
            </div>
                <?php
                }
            ?>
            
        </div>
        <div class="product-detail">
            <h5><a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"><?php the_title(); ?></a></h5>
            <p><?php echo esc_html( $excerpt ); ?></p>
            <div class="bottom-detail">
                <?php if ( $qty !== '' ) : ?>
                    <p>In Stock <span>(<?php echo intval( $qty ); ?>)</span></p>
                <?php endif; ?>
                <h6>From <?php echo $price; ?></h6>
            </div>

            <?php   
                    if ( $product->is_in_stock() ) {
                        if(0 && $product_type == 'Prescriptive' && !$acess_to_add_prescriptive_products) {
                            ?>
                            <a class="btn dark-btn" href="<?php echo home_url(); ?>/hair-treatment-quiz" data-product-url="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
                                <span>
                                    <?php echo esc_html__( QUIZ_BUTTON, 'woocommerce' ); ?>
                                </span>
                            </a>
                            <?php
                        } else {
                            // In stock → link to the product page
                            printf(
                                '<a href="%s" class="btn dark-btn" aria-label="%s"><span>%s</span></a>',
                                esc_url( custom_add_to_cart_url( $product) ),
                                esc_attr( sprintf( __( 'View “%s” details', 'woocommerce' ), $product->get_name() ) ),
                                esc_html__( 'Buy Now', 'woocommerce' )
                            );
                        }
                    } else {
                        // Out of stock → disabled button
                        echo '<button class="btn dark-btn" disabled><span>'
                            . esc_html__( 'Out of Stock', 'woocommerce' )
                            . '</span></button>'; 
                    }  
            ?>
        </div>
    </div>
</div>