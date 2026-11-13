<?php
/**
 * Component: HG – Newsletter Strip
 *
 * ACF Flexible Content layout name: hg_newsletter
 *
 * Sub-fields:
 *   content       (wysiwyg)
 *   button        (link)
 *   image_desktop (image)
 *   image_mobile  (image)
 */

$content       = get_sub_field( 'content' );
$button        = get_sub_field( 'button' );
$image_desktop = get_sub_field( 'image_desktop' );
$image_mobile  = get_sub_field( 'image_mobile' );

if ( ! $content && ! $button && ! $image_desktop && ! $image_mobile ) {
    return;
}
?>
<style>
.newsletter-grid { overflow: hidden; }
.newsletter-grid .bg-primary { background: #0B1D38; border-radius: 16px; padding: 60px; margin: 0 16px 75px; }
.newsletter-grid .right-img img { display: block; width: 100%; object-fit: cover; }
</style>
<section class="newsletter-grid mb-0">
    <div class="container">
        <div class="bg-primary">
            <div class="row">
                <div class="col-lg-7 col-md-12">
                    <div class="d-flex flex-wrap align-items-center h-100">
                        <div>
                            <?php if ( $content ) : ?>
                            <?php echo wp_kses_post( $content ); ?>
                            <?php endif; ?>

                            <?php if ( $button ) : ?>
                            <a class="btn secondary-btn"
                               href="<?php echo esc_url( $button['url'] ); ?>"
                               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $button['title'] ); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12">
                    <div class="right-img">
                        <?php if ( $image_desktop ) : ?>
                        <div class="desk-show">
                            <img src="<?php echo esc_url( $image_desktop['url'] ); ?>"
                                 alt="<?php echo esc_attr( $image_desktop['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>
                        <?php if ( $image_mobile ) : ?>
                        <div class="mb-show">
                            <img src="<?php echo esc_url( $image_mobile['url'] ); ?>"
                                 alt="<?php echo esc_attr( $image_mobile['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
