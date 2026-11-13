<?php
/**
 * Component: Stats + CTA Bar
 *
 * ACF Flexible Content layout name: stats_cta_bar
 *
 * Fields:
 *   title       (text)
 *   stats       (repeater)
 *     └ icon    (image)
 *     └ number  (text)   e.g. "10k+"
 *     └ label   (text)   e.g. "Pills Delivered"
 *   button      (link)
 *   button_style (text)  default: 'secondary-btn'
 *   bg_color    (color)  default: #122B51
 *   text_color  (color)  default: #C7E8FF
 */

$title        = get_sub_field( 'title' );
$stats        = get_sub_field( 'stats' );
$button       = get_sub_field( 'button' );
$button_style = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$bg_color     = get_sub_field( 'bg_color' ) ?: '#122B51';
$text_color   = get_sub_field( 'text_color' ) ?: '#C7E8FF';
?>
<section class="component-stats-cta-bar"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;
                color:<?php echo esc_attr( $text_color ); ?>;
                padding:80px 0;">
    <div class="container">

        <?php if ( $title ) : ?>
            <h2 class="scb-title text-center fw-bold mb-5"
                style="color:<?php echo esc_attr( $text_color ); ?>;">
                <?php echo esc_html( $title ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( $stats ) : ?>
        <div class="row justify-content-between scb-stats-row mb-5">
            <?php foreach ( $stats as $index => $stat ) :
                $is_last = ( $index === count( $stats ) - 1 );
            ?>
            <div class="col-lg-3 col-md-6 col-6 scb-stat-item<?php echo $is_last ? '' : ' scb-stat-divider'; ?>
                        py-3 d-flex flex-row align-items-center justify-content-center gap-3">

                <?php if ( ! empty( $stat['icon'] ) ) : ?>
                    <div class="scb-stat-icon flex-shrink-0">
                        <img src="<?php echo esc_url( $stat['icon']['url'] ); ?>"
                             alt="<?php echo esc_attr( $stat['icon']['alt'] ); ?>"
                             style="width:64px;height:64px;object-fit:contain;" />
                    </div>
                <?php endif; ?>

                <div class="scb-stat-text">
                    <?php if ( ! empty( $stat['number'] ) ) : ?>
                        <div class="scb-stat-number fw-bold"
                             style="font-size:2.5rem;line-height:1.1;color:<?php echo esc_attr( $text_color ); ?>;">
                            <?php echo esc_html( $stat['number'] ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $stat['label'] ) ) : ?>
                        <div class="scb-stat-label mt-1"
                             style="color:<?php echo esc_attr( $text_color ); ?>;opacity:0.75;font-size:0.95rem;">
                            <?php echo esc_html( $stat['label'] ); ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ( $button ) : ?>
        <div class="text-center mt-2">
            <a class="btn <?php echo esc_attr( $button_style ); ?>"
               href="<?php echo esc_url( $button['url'] ); ?>"
               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                <?php echo esc_html( $button['title'] ); ?>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
