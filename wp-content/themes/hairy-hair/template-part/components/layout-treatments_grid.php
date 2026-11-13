<?php
/**
 * Component: Treatments Grid
 *
 * ACF Flexible Content layout name: treatments_grid
 *
 * Fields:
 *   title           (text)        e.g. "Our Treatments"
 *   button          (link)        header CTA button
 *   bg_color        (color)       section background, default: #ffffff
 *   padding         (text)        CSS padding, default: '60px 0'
 *   treatments      (repeater)
 *     └ treatment   (post_object) linked treatment post
 *     └ icon        (image)       treatment icon
 *     └ description (textarea)    short description
 */

$title    = get_sub_field( 'title' );
$button   = get_sub_field( 'button' );
$bg_color = get_sub_field( 'bg_color' ) ?: '#ffffff';
$padding  = get_sub_field( 'padding' ) ?: '60px 0';
?>
<section class="treatments" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">
        <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
            <?php if ( $title ) : ?>
                <h2><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>

            <?php if ( $button ) : ?>
                <a class="btn secondary-btn"
                   href="<?php echo esc_url( $button['url'] ); ?>"
                   target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                    <?php echo esc_html( $button['title'] ); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="row">
            <?php if ( have_rows( 'treatments' ) ) : ?>
                <?php while ( have_rows( 'treatments' ) ) : the_row(); ?>
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
</section>
