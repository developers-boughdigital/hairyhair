<?php 

// thumbnail URL fallback
                                            $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'medium' ) : wc_placeholder_img_src();
                                            // stock and price
                                            $in_stock = $product->is_in_stock();
                                            $product_type = get_field( 'product_type', $products->get_id() );
                                            $badge_class = ( 'Prescriptive' === $product_type )
                                                            ? 'badge-green-bg'
                                                            : 'badge-purple-bg';
                                            $qty      = $product->managing_stock() ? $product->get_stock_quantity() : '';
                                            $price    = $product->get_price_html();
                                            // excerpt
                                            $excerpt  = wp_trim_words( get_the_excerpt(), 12, '…' );
                                    ?>
                                            <div class="col-lg-3 col-md-6 col-12">
                                                <div class="product-slide">
                                                    <div class="product-image">
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
                                                                // In stock → link to the product page
                                                                printf(
                                                                    '<a href="%s" class="btn dark-btn" aria-label="%s"><span>%s</span></a>',
                                                                    esc_url( get_permalink( $product->get_id() ) ),
                                                                    esc_attr( sprintf( __( 'View “%s” details', 'woocommerce' ), $product->get_name() ) ),
                                                                    esc_html__( 'Add To Cart', 'woocommerce' )
                                                                );
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