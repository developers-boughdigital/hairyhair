<?php
/**
 * Component: Support Features (3-Col Image Cards)
 * Matches design: centered heading + subtitle, 3 cards each with
 * a large preview image in a framed card, bold title and description below.
 *
 * Fields: title, subtitle, bg_color, padding,
 *         cards[] { image, title, description }
 */

$title    = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$bg_color = get_sub_field( 'bg_color' ) ?: '#ffffff';
$padding  = get_sub_field( 'padding' )  ?: '80px 0';
?>
<section class="component-support-features" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="global-title text-center support-features-header">
            <?php if ( $title ) : ?>
                <h2><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="support-features-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( have_rows( 'cards' ) ) : ?>
        <div class="row support-features-row">
            <?php while ( have_rows( 'cards' ) ) : the_row(); ?>
            <?php
            $image = get_sub_field( 'image' );
            $ctitle = get_sub_field( 'title' );
            $desc   = get_sub_field( 'description' );
            ?>
            <div class="col-lg-4 col-md-6 col-12 support-feature-col">
                <div class="support-feature-card">

                    <?php if ( $image ) : ?>
                    <div class="support-feature-image-wrap">
                        <img
                            src="<?php echo esc_url( $image['url'] ); ?>"
                            alt="<?php echo esc_attr( $image['alt'] ?: $ctitle ); ?>"
                            loading="lazy"
                        />
                    </div>
                    <?php endif; ?>

                    <?php if ( $ctitle ) : ?>
                        <h3 class="support-feature-title"><?php echo esc_html( $ctitle ); ?></h3>
                    <?php endif; ?>

                    <?php if ( $desc ) : ?>
                        <p class="support-feature-desc"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
