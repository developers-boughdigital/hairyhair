<?php
/**
 * Component: Text Columns
 * 2, 3 or 4 side-by-side columns each with an optional icon, title, content and button.
 * Fields: title, columns, bg_color, column_items[] { icon, title, content, button }
 */

$section_title = get_sub_field( 'title' );
$columns       = (int) ( get_sub_field( 'columns' ) ?: 3 );
$bg_color      = get_sub_field( 'bg_color' ) ?: '#ffffff';

$bs_cols   = 12 / $columns;
$col_class = 'col-lg-' . $bs_cols . ' col-md-6 col-12';
?>
<section class="component-text-columns" style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:60px 0;">
    <div class="container">

        <?php if ( $section_title ) : ?>
        <div class="global-title text-center">
            <h2><?php echo esc_html( $section_title ); ?></h2>
        </div>
        <?php endif; ?>

        <div class="row">
            <?php if ( have_rows( 'column_items' ) ) : ?>
                <?php while ( have_rows( 'column_items' ) ) : the_row(); ?>
                <?php
                $icon    = get_sub_field( 'icon' );
                $ctitle  = get_sub_field( 'title' );
                $content = get_sub_field( 'content' );
                $button  = get_sub_field( 'button' );
                ?>
                <div class="<?php echo esc_attr( $col_class ); ?>">
                    <div class="text-column-item">

                        <?php if ( $icon ) : ?>
                        <div class="text-column-icon mb-3">
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $icon['alt'] ); ?>"
                                 style="max-width:56px;max-height:56px;object-fit:contain;" />
                        </div>
                        <?php endif; ?>

                        <?php if ( $ctitle ) : ?>
                            <h4><?php echo esc_html( $ctitle ); ?></h4>
                        <?php endif; ?>

                        <?php if ( $content ) : ?>
                            <div class="text-column-content"><?php echo wp_kses_post( $content ); ?></div>
                        <?php endif; ?>

                        <?php if ( $button ) : ?>
                        <div class="mt-3">
                            <a class="btn dark-btn"
                               href="<?php echo esc_url( $button['url'] ); ?>"
                               target="<?php echo esc_attr( $button['target'] ); ?>">
                                <?php echo esc_html( $button['title'] ); ?>
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
