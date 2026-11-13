<?php
/**
 * Component: Cards Grid
 * Fields: title, subtitle, columns, bg_color, cards[] { icon, title, description, button }
 */

$title    = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$columns  = (int) ( get_sub_field( 'columns' ) ?: 3 );
$bg_color = get_sub_field( 'bg_color' ) ?: '#f8f8f8';

// Bootstrap column class based on chosen columns (2/3/4)
$bs_cols  = 12 / $columns;
$col_class = 'col-lg-' . $bs_cols . ' col-md-6 col-12';
?>
<section class="component-cards-grid" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:60px 0;">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="global-title text-center">
            <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
            <?php if ( $subtitle ) : ?><p><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <?php if ( have_rows( 'cards' ) ) : ?>
                <?php while ( have_rows( 'cards' ) ) : the_row(); ?>
                <?php
                $icon  = get_sub_field( 'icon' );
                $ctitle = get_sub_field( 'title' );
                $desc  = get_sub_field( 'description' );
                $btn   = get_sub_field( 'button' );
                ?>
                <div class="<?php echo esc_attr( $col_class ); ?>">
                    <div class="component-card each-service">

                        <?php if ( $icon ) : ?>
                        <div class="service-img">
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>

                        <?php if ( $ctitle ) : ?>
                            <h5><?php echo esc_html( $ctitle ); ?></h5>
                        <?php endif; ?>

                        <?php if ( $desc ) : ?>
                            <p><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>

                        <?php if ( $btn ) : ?>
                        <div class="d-flex justify-content-end">
                            <a class="btn dark-btn"
                               href="<?php echo esc_url( $btn['url'] ); ?>"
                               target="<?php echo esc_attr( $btn['target'] ); ?>">
                                <?php echo esc_html( $btn['title'] ); ?>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

    </div>
</section>
