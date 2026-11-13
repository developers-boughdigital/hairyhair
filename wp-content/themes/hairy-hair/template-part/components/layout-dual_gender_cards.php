<?php
/**
 * Component: Dual Gender CTA Cards
 *
 * ACF Flexible Content layout name: dual_gender_cards
 * Field group:                      group_dual_gender_cards
 * Flexible content field:           dual_gender_card_sections
 *
 * Fields (per card in the repeater):
 *   label              (text)
 *   description        (textarea)
 *   button             (link)
 *   button_style       (select)   default: secondary-btn
 *   icon               (image)
 *   product_image      (image)
 *   bg_color           (color)    solid fallback  default: #C8D8ED
 *   bg_gradient        (text)     CSS gradient — overrides bg_color when set
 *   text_color         (color)    default: #122B51
 *   bg_image           (image)
 *   product_col_class  (text)     extra classes on product image column
 *   content_col_class  (text)     extra classes on text content column
 */

$dgc_title = get_sub_field( 'dgc_title' );
$cards     = get_sub_field( 'cards' );
?>
<section class="component-dual-gender-cards">
    <div class="container px-0">

        <?php if ( $dgc_title ) : ?>
            <h2 class="global-title text-center"><?php echo esc_html( $dgc_title ); ?></h2>
        <?php endif; ?>

        <div class="row dgc-row-gap-mobile">

            <?php if ( $cards ) :
                foreach ( $cards as $card_idx => $card ) :
                    $label             = $card['label']             ?? '';
                    $description       = $card['description']       ?? '';
                    $button            = $card['button']            ?? null;
                    $button_style      = ! empty( $card['button_style'] )      ? $card['button_style']      : 'secondary-btn';
                    $bg_color          = ! empty( $card['bg_color'] )          ? $card['bg_color']          : '#C8D8ED';
                    $bg_gradient       = ! empty( $card['bg_gradient'] )       ? $card['bg_gradient']       : '';
                    $text_color        = ! empty( $card['text_color'] )        ? $card['text_color']        : '#122B51';
                    $icon              = $card['icon']              ?? null;
                    $product_image     = $card['product_image']     ?? null;
                    $bg_image          = $card['bg_image']          ?? null;
                    $product_col_class = ! empty( $card['product_col_class'] ) ? ' ' . $card['product_col_class'] : '';
                    $content_col_class = ! empty( $card['content_col_class'] ) ? ' ' . $card['content_col_class'] : '';

                    // Gradient takes priority; fall back to solid bg_color
                    if ( $bg_gradient ) {
                        $card_style = 'background:' . esc_attr( $bg_gradient ) . ';';
                    } else {
                        $card_style = 'background-color:' . esc_attr( $bg_color ) . ';';
                    }

                    // Card 0 (left): product image LEFT, text content RIGHT
                    // Card 1 (right): text content LEFT, product image RIGHT
                    $product_order = ( $card_idx === 0 ) ? 'order-0' : 'order-1';
                    $content_order = ( $card_idx === 0 ) ? 'order-1' : 'order-0';
            ?>
            <div class="col-lg-6 col-md-6 col-12">
                <div class="dgc-card position-relative overflow-hidden" style="<?php echo $card_style; ?>">

                    <?php if ( $bg_image ) : ?>
                        <img src="<?php echo esc_url( $bg_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $bg_image['alt'] ); ?>"
                             class="dgc-bg-img" />
                    <?php endif; ?>

                    <div class="dgc-card-inner">

                        <!-- Product image column -->
                        <div class="col-lg-4 col-md-3 col-3 <?php echo esc_attr( $product_order . $product_col_class ); ?>">
                            <?php if ( $product_image ) : ?>
                                <img src="<?php echo esc_url( $product_image['url'] ); ?>"
                                     alt="<?php echo esc_attr( $product_image['alt'] ); ?>" />
                            <?php endif; ?>
                        </div>

                        <!-- Text content column -->
                        <div class="col-lg-7 col-md-8 col-9 dgc-pro-content <?php echo esc_attr( $content_order . $content_col_class ); ?>"
                             style="color:<?php echo esc_attr( $text_color ); ?>;">

                            <?php if ( $icon ) : ?>
                                <div class="dgc-icon mb-3">
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                         alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                </div>
                            <?php endif; ?>

                            <?php if ( $label ) : ?>
                                <h3 class="dgc-label fw-bold mb-3"
                                    style="color:<?php echo esc_attr( $text_color ); ?>;">
                                    <?php echo esc_html( $label ); ?>
                                </h3>
                            <?php endif; ?>

                            <?php if ( $description ) : ?>
                                <p class="dgc-desc mb-4"
                                   style="color:<?php echo esc_attr( $text_color ); ?>;">
                                    <?php echo esc_html( $description ); ?>
                                </p>
                            <?php endif; ?>

                            <?php if ( $button ) : ?>
                                <a class="btn <?php echo esc_attr( $button_style ); ?>"
                                   href="<?php echo esc_url( $button['url'] ); ?>"
                                   target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                                    <?php echo esc_html( $button['title'] ); ?>
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>
            </div>
            <?php endforeach; endif; ?>

        </div>
    </div>
</section>
