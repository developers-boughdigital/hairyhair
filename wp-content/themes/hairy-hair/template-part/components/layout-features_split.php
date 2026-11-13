<?php
/**
 * Component: Features Split (Why Choose)
 *
 * ACF Flexible Content layout name: features_split
 *
 * Fields:
 *   title        (text)
 *   features     (repeater)
 *     └ icon     (image)
 *     └ title    (text)
 *     └ content  (textarea)
 *   bg_color     (color)  default: #ffffff
 *   title_color  (color)  default: #122B51
 */

$title       = get_sub_field( 'title' );
$features    = get_sub_field( 'features' );
$bg_color    = get_sub_field( 'bg_color' ) ?: '#ffffff';
$title_color = get_sub_field( 'title_color' ) ?: '#122B51';
?>
<section class="component-features-split"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:80px 0;">
    <div class="container">
        <div class="row align-items-start">

            <!-- Left: Big Heading -->
            <div class="col-lg-5 col-md-12 fs-heading-col pe-lg-5 mb-5 mb-lg-0">
                <?php if ( $title ) : ?>
                    <h2 class="fs-main-title fw-bold"
                        style="color:<?php echo esc_attr( $title_color ); ?>;">
                        <?php echo esc_html( $title ); ?>
                    </h2>
                <?php endif; ?>
            </div>

            <!-- Right: Feature rows -->
            <div class="col-lg-7 col-md-12 fs-features-col">
                <?php if ( $features ) :
                    $total = count( $features );
                    foreach ( $features as $i => $feat ) :
                        $is_last = ( $i === $total - 1 );
                ?>
                <div class="fs-feature-item d-flex align-items-start gap-3<?php echo $is_last ? '' : ' fs-feature-divider'; ?>">

                    <?php if ( ! empty( $feat['icon'] ) ) : ?>
                        <div class="fs-feature-icon flex-shrink-0">
                            <img src="<?php echo esc_url( $feat['icon']['url'] ); ?>"
                                 alt="<?php echo esc_attr( $feat['icon']['alt'] ); ?>"
                                 style="width:44px;height:44px;object-fit:contain;" />
                        </div>
                    <?php endif; ?>

                    <div class="fs-feature-text">
                        <?php if ( ! empty( $feat['title'] ) ) : ?>
                            <h5 class="fs-feature-title fw-bold mb-1">
                                <?php echo esc_html( $feat['title'] ); ?>
                            </h5>
                        <?php endif; ?>

                        <?php if ( ! empty( $feat['content'] ) ) : ?>
                            <p class="fs-feature-desc mb-0">
                                <?php echo esc_html( $feat['content'] ); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; endif; ?>
            </div>

        </div>
    </div>
</section>
