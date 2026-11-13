<?php
/**
 * Component: HG – Proven Treatment Stages
 *
 * ACF Flexible Content layout name: hg_proven_stages
 *
 * Sub-fields:
 *   title       (text)
 *   description (textarea)
 *   stages      (repeater)
 *     stage_image     (image)
 *     stage_title     (text)
 *     stage_sub_title (text)
 *     stage_details   (repeater) → detail (text)
 *     stage_button    (link)
 */

$title       = get_sub_field( 'title' );
$description = get_sub_field( 'description' );

if ( ! $title && ! $description && ! have_rows( 'stages' ) ) {
    return;
}
?>

<section class="proven-treatment-stage">
    <div class="container">
        <?php if ( $title ) : ?>
        <h2 class="proven-treatment-title"><?php echo esc_html( $title ); ?></h2>
        <?php endif; ?>
        <?php if ( $description ) : ?>
        <p class="proven-treatment-desc"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>

        <div class="proven-stages">
            <?php if ( have_rows( 'stages' ) ) : ?>
                <?php while ( have_rows( 'stages' ) ) : the_row(); ?>
                    <?php
                    $stage_image    = get_sub_field( 'stage_image' );
                    $stage_title    = get_sub_field( 'stage_title' );
                    $stage_subtitle = get_sub_field( 'stage_sub_title' );
                    $stage_button   = get_sub_field( 'stage_button' );
                    ?>
                    <div class="stage">
                        <?php if ( $stage_image ) : ?>
                        <img src="<?php echo esc_url( $stage_image['url'] ); ?>"
                             alt="<?php echo esc_attr( $stage_image['alt'] ); ?>"
                             class="stage-img" />
                        <?php endif; ?>

                        <?php if ( $stage_title ) : ?>
                        <h3 class="stage-title"><?php echo esc_html( $stage_title ); ?></h3>
                        <?php endif; ?>

                        <?php if ( $stage_subtitle ) : ?>
                        <p class="stage-sub-title"><?php echo esc_html( $stage_subtitle ); ?></p>
                        <?php endif; ?>

                        <?php if ( have_rows( 'stage_details' ) ) : ?>
                        <ul class="stage-details">
                            <?php while ( have_rows( 'stage_details' ) ) : the_row(); ?>
                                <?php $detail = get_sub_field( 'detail' ); ?>
                                <?php if ( $detail ) : ?>
                                <li class="check-list">
                                    <span><?php echo esc_html( $detail ); ?></span>
                                </li>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        </ul>
                        <?php endif; ?>

                        <?php if ( $stage_button ) : ?>
                        <a href="<?php echo esc_url( $stage_button['url'] ); ?>"
                           target="<?php echo esc_attr( $stage_button['target'] ?: '_self' ); ?>"
                           class="btn secondary-btn">
                            <?php echo esc_html( $stage_button['title'] ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
