<?php
/**
 * Component: HG – Product + Before/After
 *
 * ACF Flexible Content layout name: hg_product_before_after
 *
 * Sub-fields:
 *   product_image      (image)
 *   product_content    (wysiwyg)
 *   product_button     (link)
 *   before_after_image (image)   – optional
 *   ba_info_1          (wysiwyg) – optional
 *   ba_info_2          (wysiwyg) – optional
 *   ba_info_3          (wysiwyg) – optional
 *   ba_info_4          (wysiwyg) – optional
 */

$product_image      = get_sub_field( 'product_image' );
$product_content    = get_sub_field( 'product_content' );
$product_button     = get_sub_field( 'product_button' );
$before_after_image = get_sub_field( 'before_after_image' );
$ba_info_1          = get_sub_field( 'ba_info_1' );
$ba_info_2          = get_sub_field( 'ba_info_2' );
$ba_info_3          = get_sub_field( 'ba_info_3' );
$ba_info_4          = get_sub_field( 'ba_info_4' );

// Only render if there is something to show
if ( ! $product_image && ! $product_content && ! $product_button && ! $before_after_image
    && ! $ba_info_1 && ! $ba_info_2 && ! $ba_info_3 && ! $ba_info_4 ) {
    return;
}
?>
<style>
.hiding-sec { padding: 45px 0; margin: 24px 16px 0 16px; }
.hiding-sec .row { max-width: 1200px; display: flex; flex-direction: row; align-items: center; justify-content: center; margin: 0 auto; }
.hiding-sec h2 { font-size: 32px; line-height: 32px; }
.hiding-sec .product-content p { display: flex; flex-direction: row; font-size: 14px; line-height: 1.4; align-items: center; flex-wrap: wrap; }
.hiding-sec .right-content ul { gap: 10px; }
.hiding-sec .right-content ul li { font-size: 12px; letter-spacing: 0; }
.hiding-sec .left-img { width: 100%; height: 100%; }
.hiding-sec .left-img img { display: block; object-fit: cover; width: 100%; height: 100%; }
.hiding-sec .right-content { display: flex; flex-direction: column; justify-content: center; height: 100%; }
.hiding-sec .before-after { display: flex; margin-top: 20px; }
.hiding-sec .before-after-image { margin-right: 37px; width: 270px; height: 176px; flex-shrink: 0; }
.hiding-sec .before-after-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
.hiding-sec .before-after-info { display: flex; flex-direction: column; justify-content: center; }
.hiding-sec .before-after-info .ba-item p { font-size: 12px; margin: 0; line-height: 17px; }
.hiding-sec .before-after-info .ba-item { margin-bottom: 17px; }
.hiding-sec .before-after-info .ba-item:last-child { margin-bottom: 0; }
@media screen and (max-width: 768px) {
    .hiding-sec .before-after { display: block; }
    .hiding-sec .before-after .before-after-info { margin-top: 17px; }
    .hiding-sec .before-after-image { width: 100%; height: auto; }
    .hiding-sec .product-content p span { margin-left: 0; margin-top: 10px; }
}
</style>
<section class="hiding-sec">
    <div class="container">
        <div class="row">

            <!-- Left Image -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="left-img">
                    <?php if ( $product_image ) : ?>
                    <img src="<?php echo esc_url( $product_image['url'] ); ?>"
                         alt="<?php echo esc_attr( $product_image['alt'] ); ?>" />
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="right-content">

                    <?php if ( $product_content ) : ?>
                    <div class="product-content">
                        <?php echo wp_kses_post( $product_content ); ?>

                        <?php if ( $product_button ) : ?>
                        <div class="tackle-btn mt-3">
                            <a class="large-btn btn secondary-btn"
                               href="<?php echo esc_url( $product_button['url'] ); ?>"
                               target="<?php echo esc_attr( $product_button['target'] ?: '_self' ); ?>">
                                <span><?php echo esc_html( $product_button['title'] ); ?></span>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ( $before_after_image || $ba_info_1 || $ba_info_2 || $ba_info_3 || $ba_info_4 ) : ?>
                    <div class="before-after">
                        <?php if ( $before_after_image ) : ?>
                        <div class="before-after-image">
                            <img src="<?php echo esc_url( $before_after_image['url'] ); ?>"
                                 alt="<?php echo esc_attr( $before_after_image['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>

                        <div class="before-after-info">
                            <?php if ( $ba_info_1 ) : ?>
                            <div class="ba-item"><?php echo wp_kses_post( $ba_info_1 ); ?></div>
                            <?php endif; ?>
                            <?php if ( $ba_info_2 ) : ?>
                            <div class="ba-item"><?php echo wp_kses_post( $ba_info_2 ); ?></div>
                            <?php endif; ?>
                            <?php if ( $ba_info_3 ) : ?>
                            <div class="ba-item"><?php echo wp_kses_post( $ba_info_3 ); ?></div>
                            <?php endif; ?>
                            <?php if ( $ba_info_4 ) : ?>
                            <div class="ba-item"><?php echo wp_kses_post( $ba_info_4 ); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>
</section>
