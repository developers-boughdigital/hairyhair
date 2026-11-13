<?php
/**
 * Component: HG – Hero Banner
 *
 * ACF Flexible Content layout name: hg_hero_banner
 *
 * Sub-fields:
 *   title            (text)
 *   description      (wysiwyg)
 *   button_primary   (link)
 *   button_secondary (link)   – optional, rendered as outline-btn
 *   image            (image)
 */

$title            = get_sub_field( 'title' );
$description      = get_sub_field( 'description' );
$button_primary   = get_sub_field( 'button_primary' );
$button_secondary = get_sub_field( 'button_secondary' );
$image            = get_sub_field( 'image' );
?>
<style>
.hairphill-main-ban h1 { font-size: 44px; margin: 10px 0; }
.hairphill-main-ban .contact-btn { margin-top: 100px; }
.hairphill-main-ban.main-banner-content .ban-left-content { max-width: 570px; }
.hairphill-main-ban.main-banner-content p { font-size: 18px; margin-bottom: 20px; }
</style>
<section class="main-banner-content hairphill-main-ban">
    <div class="container-fluid">
        <div class="row align-items-start">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="ban-left-content">
                    <div class="content-wrapper">
                        <nav aria-label="breadcrumb">
                            <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                <?php if ( function_exists( 'bcn_display' ) ) { bcn_display(); } ?>
                            </div>
                        </nav>

                        <?php if ( $title ) : ?>
                        <h1><?php echo esc_html( $title ); ?></h1>
                        <?php endif; ?>

                        <?php if ( $description ) : ?>
                        <div class="banner-desc"><?php echo wp_kses_post( $description ); ?></div>
                        <?php endif; ?>

                        <?php if ( $button_primary || $button_secondary ) : ?>
                        <div class="contact-btn">
                            <?php if ( $button_primary ) : ?>
                            <a class="large-btn btn secondary-btn me-3"
                               href="<?php echo esc_url( $button_primary['url'] ); ?>"
                               target="<?php echo esc_attr( $button_primary['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $button_primary['title'] ); ?>
                            </a>
                            <?php endif; ?>
                            <?php if ( $button_secondary ) : ?>
                            <a class="large-btn btn outline-btn"
                               href="<?php echo esc_url( $button_secondary['url'] ); ?>"
                               target="<?php echo esc_attr( $button_secondary['target'] ?: '_self' ); ?>">
                                <?php echo esc_html( $button_secondary['title'] ); ?>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <div class="ban-right-img">
                    <?php if ( $image ) : ?>
                    <img src="<?php echo esc_url( $image['url'] ); ?>"
                         alt="<?php echo esc_attr( $image['alt'] ); ?>" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
