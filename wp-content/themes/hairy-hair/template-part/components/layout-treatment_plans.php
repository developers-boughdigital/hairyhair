<?php
/**
 * Component: Treatment Plans Cards
 * Layout name: treatment_plans
 *
 * ACF Sub-fields:
 *   tp_title       (text)          – Section heading
 *   tp_subtitle    (textarea)      – Section sub-heading / description
 *   tp_bg_color    (color_picker)  – Section background  default: #ffffff
 *   tp_padding     (text)          – Section padding     default: 80px 0
 *   tp_cards       (repeater)
 *     └ tp_card_image        (image)
 *     └ tp_card_title        (text)
 *     └ tp_card_price        (text)       e.g. "from just $70/month"
 *     └ tp_card_features     (repeater)
 *         └ tp_card_feat_text (text)
 *     └ tp_card_button       (link)
 *     └ tp_card_button_style (select)    secondary-btn | dark-btn | outline-btn
 *     └ tp_card_bg           (color_picker) default: #f4f6f8
 */

$tp_title    = get_sub_field( 'tp_title' );
$tp_subtitle = get_sub_field( 'tp_subtitle' );
$tp_bg_color = get_sub_field( 'tp_bg_color' ) ?: '#ffffff';
$tp_padding  = get_sub_field( 'tp_padding' )  ?: '80px 0';
?>

<section class="component-treatment-plans"
         style="background-color:<?php echo esc_attr( $tp_bg_color ); ?>;padding:<?php echo esc_attr( $tp_padding ); ?>;">
    <div class="container">

        <?php if ( $tp_title || $tp_subtitle ) : ?>
        <div class="global-title text-center tp-section-header">
            <?php if ( $tp_title ) : ?>
                <h2><?php echo esc_html( $tp_title ); ?></h2>
            <?php endif; ?>
            <?php if ( $tp_subtitle ) : ?>
                <p><?php echo esc_html( $tp_subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row tp-cards-row">
            <?php if ( have_rows( 'tp_cards' ) ) : ?>
                <?php while ( have_rows( 'tp_cards' ) ) : the_row(); ?>
                    <?php
                    $card_image   = get_sub_field( 'tp_card_image' );
                    $card_title   = get_sub_field( 'tp_card_title' );
                    $card_price   = get_sub_field( 'tp_card_price' );
                    $card_button  = get_sub_field( 'tp_card_button' );
                    $btn_style    = get_sub_field( 'tp_card_button_style' ) ?: 'secondary-btn';
                    $card_bg      = get_sub_field( 'tp_card_bg' ) ?: '#f4f6f8';
                    ?>
                    <div class="col-lg-4 col-md-6 col-12 tp-card-col">
                        <div class="tp-card" style="background-color:<?php echo esc_attr( $card_bg ); ?>;">

                            <?php if ( $card_image ) : ?>
                            <div class="tp-card-img">
                                <img src="<?php echo esc_url( $card_image['url'] ); ?>"
                                     alt="<?php echo esc_attr( $card_image['alt'] ); ?>"
                                     loading="lazy" />
                            </div>
                            <?php endif; ?>

                            <div class="tp-card-body">

                                <?php if ( $card_title ) : ?>
                                    <h4 class="tp-card-title"><?php echo esc_html( $card_title ); ?></h4>
                                <?php endif; ?>

                                <?php if ( $card_price ) : ?>
                                    <p class="tp-card-price"><?php echo esc_html( $card_price ); ?></p>
                                <?php endif; ?>

                                <?php if ( have_rows( 'tp_card_features' ) ) : ?>
                                <ul class="tp-card-features">
                                    <?php while ( have_rows( 'tp_card_features' ) ) : the_row(); ?>
                                        <?php $feat = get_sub_field( 'tp_card_feat_text' ); ?>
                                        <?php if ( $feat ) : ?>
                                        <li>
                                            <span class="tp-check-icon" aria-hidden="true">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <circle cx="10" cy="10" r="10" fill="#3E7BD2"/>
                                                    <path d="M5.5 10.5L8.5 13.5L14.5 7.5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <span><?php echo esc_html( $feat ); ?></span>
                                        </li>
                                        <?php endif; ?>
                                    <?php endwhile; ?>
                                </ul>
                                <?php endif; ?>

                                <?php if ( $card_button ) : ?>
                                <div class="tp-card-cta">
                                    <a class="btn <?php echo esc_attr( $btn_style ); ?>"
                                       href="<?php echo esc_url( $card_button['url'] ); ?>"
                                       target="<?php echo esc_attr( $card_button['target'] ?: '_self' ); ?>">
                                        <?php echo esc_html( $card_button['title'] ); ?>
                                    </a>
                                </div>
                                <?php endif; ?>

                            </div><!-- .tp-card-body -->
                        </div><!-- .tp-card -->
                    </div><!-- .tp-card-col -->
                <?php endwhile; ?>
            <?php endif; ?>
        </div><!-- .tp-cards-row -->

    </div><!-- .container -->
</section><!-- .component-treatment-plans -->
