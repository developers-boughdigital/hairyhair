<?php
/**
 * Component: HG – Treatments Comparison Grid
 *
 * ACF Flexible Content layout name: hg_treatments_effect
 *
 * Sub-fields:
 *   title       (text)     – section heading
 *   description (textarea) – section description
 *   button      (link)
 *   slides      (repeater)
 *     before_image          (image)
 *     after_image           (image)
 *     comparing_description (textarea) – testimonial quote
 *     author                (text)     – e.g. "— Mark, 45"
 */

$title       = get_sub_field( 'title' );
$description = get_sub_field( 'description' );
$button      = get_sub_field( 'button' );

if ( ! $title && ! $description && ! $button && ! have_rows( 'slides' ) ) {
    return;
}
?>
<section class="component-hgte">
    <div class="container">

        <?php if ( $title || $description || $button ) : ?>
        <div class="global-title text-center white-title hgte-header d-flex justify-content-between align-items-center flex-wrap">
            <div class="hgte-header-text text-left">
                <?php if ( $title ) : ?>
                    <h2><?php echo esc_html( $title ); ?></h2>
                <?php endif; ?>
                <?php if ( $description ) : ?>
                    <p><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( $button ) : ?>
            <a class="btn secondary-btn hgte-header-btn"
               href="<?php echo esc_url( $button['url'] ); ?>"
               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                <?php echo esc_html( $button['title'] ); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( have_rows( 'slides' ) ) : ?>
        <div class="hgte-grid row">
            <?php while ( have_rows( 'slides' ) ) : the_row();
                $before_img = get_sub_field( 'before_image' );
                $after_img  = get_sub_field( 'after_image' );
                $desc       = get_sub_field( 'comparing_description' );
                $author     = get_sub_field( 'author' );
            ?>
            <div class="col-lg-4 col-md-6 col-12 hgte-col">
                <div class="hgte-card">

                    <div class="hgte-images">
                        <?php if ( $before_img ) : ?>
                        <div class="hgte-img-wrap">
                            <img src="<?php echo esc_url( $before_img['url'] ); ?>"
                                 alt="<?php echo esc_attr( $before_img['alt'] ); ?>" />
                            <span class="hgte-label">Before</span>
                        </div>
                        <?php endif; ?>

                        <?php if ( $after_img ) : ?>
                        <div class="hgte-img-wrap">
                            <img src="<?php echo esc_url( $after_img['url'] ); ?>"
                                 alt="<?php echo esc_attr( $after_img['alt'] ); ?>" />
                            <span class="hgte-label hgte-label--after">After</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( $desc || $author ) : ?>
                    <div class="hgte-body">
                        <?php if ( $desc ) : ?>
                        <p class="hgte-desc"><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>
                        <?php if ( $author ) : ?>
                        <p class="hgte-author"><?php echo esc_html( $author ); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
