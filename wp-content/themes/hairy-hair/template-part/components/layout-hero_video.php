<?php
/**
 * Component: Hero – Video Embed
 * Section with a YouTube / Vimeo embed alongside headline and content.
 * Fields: title, content, video_url, layout, button, button_style, bg_color
 */

$title        = get_sub_field( 'title' );
$content      = get_sub_field( 'content' );
$video_url    = get_sub_field( 'video_url' );
$layout       = get_sub_field( 'layout' ) ?: 'video-right';
$button       = get_sub_field( 'button' );
$button_style = get_sub_field( 'button_style' ) ?: 'secondary-btn';
$bg_color     = get_sub_field( 'bg_color' ) ?: '#f8f8f8';

// Build the embed URL (YouTube & Vimeo)
$embed_url = '';
if ( $video_url ) {
    if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $m ) ) {
        $embed_url = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
    } elseif ( preg_match( '/vimeo\.com\/(\d+)/', $video_url, $m ) ) {
        $embed_url = 'https://player.vimeo.com/video/' . $m[1];
    }
}

$is_stacked = ( $layout === 'video-below' );
$text_order  = ( $layout === 'video-left' ) ? 'order-2' : 'order-1';
$video_order = ( $layout === 'video-left' ) ? 'order-1' : 'order-2';
?>
<section class="component-hero-video"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:60px 0;">
    <div class="container">

        <?php if ( $is_stacked ) : ?>
        <!-- Stacked: text above, video below -->
        <div class="row">
            <div class="col-12 text-center mb-4">
                <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
                <?php if ( $content ) : ?><div class="hero-video-content"><?php echo wp_kses_post( $content ); ?></div><?php endif; ?>
                <?php if ( $button ) : ?>
                <a class="btn <?php echo esc_attr( $button_style ); ?> mt-3"
                   href="<?php echo esc_url( $button['url'] ); ?>"
                   target="<?php echo esc_attr( $button['target'] ); ?>">
                    <span><?php echo esc_html( $button['title'] ); ?></span>
                </a>
                <?php endif; ?>
            </div>
            <?php if ( $embed_url ) : ?>
            <div class="col-12">
                <div class="component-video-wrapper">
                    <iframe src="<?php echo esc_url( $embed_url ); ?>"
                            title="<?php echo esc_attr( $title ); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php else : ?>
        <!-- Side by side -->
        <div class="row align-items-center">
            <div class="col-lg-5 col-md-12 <?php echo esc_attr( $text_order ); ?>">
                <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
                <?php if ( $content ) : ?><div class="hero-video-content"><?php echo wp_kses_post( $content ); ?></div><?php endif; ?>
                <?php if ( $button ) : ?>
                <a class="btn <?php echo esc_attr( $button_style ); ?> mt-3"
                   href="<?php echo esc_url( $button['url'] ); ?>"
                   target="<?php echo esc_attr( $button['target'] ); ?>">
                    <span><?php echo esc_html( $button['title'] ); ?></span>
                </a>
                <?php endif; ?>
            </div>
            <?php if ( $embed_url ) : ?>
            <div class="col-lg-7 col-md-12 <?php echo esc_attr( $video_order ); ?>">
                <div class="component-video-wrapper">
                    <iframe src="<?php echo esc_url( $embed_url ); ?>"
                            title="<?php echo esc_attr( $title ); ?>"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</section>
