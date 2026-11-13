<?php
/**
 * Component: Image + Text with Treatments Grid
 *
 * ACF Flexible Content layout name: image_text_treatments
 *
 * ── Image + Text block ───────────────────────────────────────────────────────
 *   itf_title          (text)
 *   itf_description    (wysiwyg)
 *   itf_image          (image)
 *   itf_image_position (select)  left | right   default: left
 *   itf_button         (link)
 *   itf_button_style   (select)  secondary-btn | dark-btn | outline-btn
 *   itf_bg_color       (color)   default: #D6E8F7
 *   itf_section_height (text)    default: 500px
 *
 * ── Treatments Grid block ────────────────────────────────────────────────────
 *   tg_title        (text)
 *   tg_button       (link)
 *   tg_bg_color     (color)    default: #ffffff
 *   tg_padding      (text)     default: 60px 0
 *   tg_treatments   (repeater)
 *     └ treatment   (post_object) linked treatment post
 *     └ icon        (image)
 *     └ description (textarea)
 */

/* ── Image + Text ── */
$itf_title          = get_sub_field( 'itf_title' );
$itf_description    = get_sub_field( 'itf_description' );
$itf_image          = get_sub_field( 'itf_image' );
$itf_image_position = get_sub_field( 'itf_image_position' ) ?: 'left';
$itf_button         = get_sub_field( 'itf_button' );
$itf_button_style   = get_sub_field( 'itf_button_style' ) ?: 'secondary-btn';
$itf_bg_color       = get_sub_field( 'itf_bg_color' ) ?: '#D6E8F7';

$img_order  = $itf_image_position === 'left' ? 'order-lg-1 order-1' : 'order-lg-2 order-2';
$text_order = $itf_image_position === 'left' ? 'order-lg-2 order-2' : 'order-lg-1 order-1';

/* ── Treatments Grid ── */
$tg_title    = get_sub_field( 'tg_title' );
$tg_button   = get_sub_field( 'tg_button' );
$tg_bg_color = get_sub_field( 'tg_bg_color' ) ?: '#ffffff';
$tg_padding  = get_sub_field( 'tg_padding' ) ?: '60px 0';
?>

<section  class=" component-image-text-treatments">

    <?php /* ════ Treatments Grid Block ════ */ ?>
    <div class="tg-block treatments" style="padding-bottom:126px;">
        <div class="container">

            <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
                <?php if ( $tg_title ) : ?>
                    <h2><?php echo esc_html( $tg_title ); ?></h2>
                <?php endif; ?>
                <?php if ( $tg_button ) : ?>
                    <a class="btn secondary-btn"
                       href="<?php echo esc_url( $tg_button['url'] ); ?>"
                       target="<?php echo esc_attr( $tg_button['target'] ?: '_self' ); ?>">
                        <?php echo esc_html( $tg_button['title'] ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="row">
                <?php if ( have_rows( 'tg_treatments' ) ) : ?>
                    <?php while ( have_rows( 'tg_treatments' ) ) : the_row(); ?>
                        <?php
                        $treatment = get_sub_field( 'treatment' );
                        if ( ! $treatment ) continue;
                        $post = $treatment;
                        setup_postdata( $post );
                        $icon = get_sub_field( 'icon' );
                        $desc = get_sub_field( 'description' );
                        ?>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="each-treatment bg-grey-ee">
                                <?php if ( $icon ) : ?>
                                <div class="each-treatment-img">
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                         alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                </div>
                                <?php endif; ?>
                                <h5><?php the_title(); ?></h5>
                                <?php if ( $desc ) : ?>
                                    <p><?php echo esc_html( $desc ); ?></p>
                                <?php endif; ?>
                                <div class="d-flex justify-content-end">
                                    <a class="btn dark-btn" href="<?php the_permalink(); ?>">Learn More</a>
                                </div>
                            </div>
                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php /* ════ Image + Text Block ════ */ ?>
    <div class="container itf-block">
        <div class="container-fluid px-0" style="background-color:<?php echo esc_attr( $itf_bg_color ); ?>;    border-radius: 10px;">
            <div class="row g-0 align-items-center">

                <?php if ( $itf_image ) : ?>
                <div class="col-lg-5 col-12 <?php echo esc_attr( $img_order ); ?> itf-img-col">
                    <div class="itf-img-wrap">
                        <img src="<?php echo esc_url( $itf_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $itf_image['alt'] ); ?>"
                             class="itf-img" />
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-lg-7 col-12 <?php echo esc_attr( $text_order ); ?> itf-text-col">
                    <div class="itf-content">

                        <?php if ( $itf_title ) : ?>
                            <h2 class="itf-title"><?php echo esc_html( $itf_title ); ?></h2>
                        <?php endif; ?>

                        <?php if ( $itf_description ) : ?>
                            <div class="itf-desc"><?php echo wp_kses_post( $itf_description ); ?></div>
                        <?php endif; ?>

                        <?php if ( $itf_button ) : ?>
                            <div style="padding-top:72px;" class="itf-btn-wrap">
                                <a class="btn <?php echo esc_attr( $itf_button_style ); ?>"
                                   href="<?php echo esc_url( $itf_button['url'] ); ?>"
                                   target="<?php echo esc_attr( $itf_button['target'] ?: '_self' ); ?>">
                                    <?php echo esc_html( $itf_button['title'] ); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

</section>