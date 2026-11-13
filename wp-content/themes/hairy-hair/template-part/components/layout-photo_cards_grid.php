<?php
/**
 * Component: Photo Cards Grid (Full-Bleed)
 * Matches design: centred heading + subtitle, then N columns each with a
 * full-bleed rounded photo at the top, bold title and description below.
 *
 * Fields: title, subtitle, columns, bg_color, padding,
 *         cards[] { image, title, description }
 */

$section_title = get_sub_field( 'title' );
$subtitle      = get_sub_field( 'subtitle' );
$columns       = (int) ( get_sub_field( 'columns' ) ?: 3 );
$bg_color      = get_sub_field( 'bg_color' ) ?: '#ffffff';
$padding       = get_sub_field( 'padding' )  ?: '80px 0';

$bs_col = 12 / $columns;
$col_class = 'col-lg-' . $bs_col . ' col-md-6 col-12';
?>
<section class="component-photo-cards-grid" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="container">

        <?php if ( $section_title || $subtitle ) : ?>
        <div class="global-title text-center photo-cards-header">
            <?php if ( $section_title ) : ?>
                <h2><?php echo esc_html( $section_title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="photo-cards-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( have_rows( 'cards' ) ) : ?>
        <div class="row photo-cards-row">
            <?php while ( have_rows( 'cards' ) ) : the_row(); ?>
            <?php
            $image  = get_sub_field( 'image' );
            $ctitle = get_sub_field( 'title' );
            $desc   = get_sub_field( 'description' );
            ?>
            <div class="<?php echo esc_attr( $col_class ); ?> photo-card-col">
                <div class="photo-card">

                    <?php if ( $image ) : ?>
                    <div class="photo-card-image-wrap">
                        <img
                            src="<?php echo esc_url( $image['url'] ); ?>"
                            alt="<?php echo esc_attr( $image['alt'] ?: $ctitle ); ?>"
                            loading="lazy"
                        />
                    </div>
                    <?php endif; ?>

                    <div class="photo-card-body">
                        <?php if ( $ctitle ) : ?>
                            <h3 class="photo-card-title"><?php echo esc_html( $ctitle ); ?></h3>
                        <?php endif; ?>

                        <?php if ( $desc ) : ?>
                            <p class="photo-card-desc"><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
