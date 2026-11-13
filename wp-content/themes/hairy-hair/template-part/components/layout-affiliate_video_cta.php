<?php
/**
 * Component: Affiliate – Video + CTA Strip
 *
 * ACF Flexible Content layout name: affiliate_video_cta
 *
 * Matches the "See How You Can Earn Through Hairy Hair Affiliate Program" reference design:
 *   - Centred section title
 *   - Full-width video thumbnail with centred play-button overlay
 *     (clicking opens the video URL; supports YouTube & Vimeo)
 *   - Dark navy CTA strip directly below the thumbnail:
 *       left  → bold title + short description
 *       right → CTA button
 *
 * Fields:
 *   section_title      (text)
 *   bg_color           (color_picker)   default: #ffffff
 *   padding            (text)           default: 60px 0 0
 *   video_url          (text)           YouTube / Vimeo URL
 *   thumbnail          (image)
 *   cta_bg_color       (color_picker)   default: #102040
 *   cta_text_color     (color_picker)   default: #ffffff
 *   cta_title          (text)
 *   cta_description    (textarea)
 *   cta_button         (link)
 *   cta_button_style   (select)         yellow-btn | secondary-btn | dark-btn | outline-btn
 */

$section_title    = get_sub_field( 'section_title' );
$bg_color         = get_sub_field( 'bg_color' )         ?: '#ffffff';
$padding          = get_sub_field( 'padding' )           ?: '60px 0 0';
$video_url        = get_sub_field( 'video_url' );
$thumbnail        = get_sub_field( 'thumbnail' );
$cta_bg_color     = get_sub_field( 'cta_bg_color' )     ?: '#102040';
$cta_text_color   = get_sub_field( 'cta_text_color' )   ?: '#ffffff';
$cta_title        = get_sub_field( 'cta_title' );
$cta_description  = get_sub_field( 'cta_description' );
$cta_button       = get_sub_field( 'cta_button' );
$cta_button_style = get_sub_field( 'cta_button_style' ) ?: 'yellow-btn';

// Build a clean embed URL for the modal / lightbox
$embed_url = '';
if ( $video_url ) {
    if ( preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $video_url, $m ) ) {
        $embed_url = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&rel=0';
    } elseif ( preg_match( '/vimeo\.com\/(\d+)/', $video_url, $m ) ) {
        $embed_url = 'https://player.vimeo.com/video/' . $m[1] . '?autoplay=1';
    }
}

$has_thumbnail = ! empty( $thumbnail['url'] );
?>
<section class="avc-section"
         style="background-color:<?php echo esc_attr( $bg_color ); ?>;padding:<?php echo esc_attr( $padding ); ?>;">
    <div class="max-width-959px mx-auto">

        <?php if ( $section_title ) : ?>
        <div class="avc-header text-center">
            <h2 class="avc-section-title"><?php echo esc_html( $section_title ); ?></h2>
        </div>
        <?php endif; ?>

        <!-- Video Thumbnail -->
        <?php if ( $has_thumbnail ) : ?>
        <div class="avc-video-wrap<?php echo $embed_url ? ' avc-has-video' : ''; ?>"
             <?php if ( $embed_url ) : ?>
             data-embed="<?php echo esc_attr( $embed_url ); ?>"
             role="button"
             tabindex="0"
             aria-label="<?php echo esc_attr( $section_title ?: __( 'Play video', 'hairy-hair' ) ); ?>"
             <?php endif; ?>>

            <img src="<?php echo esc_url( $thumbnail['url'] ); ?>"
                 alt="<?php echo esc_attr( $thumbnail['alt'] ?: $section_title ); ?>"
                 class="avc-thumbnail img-fluid w-100" />

            <?php if ( $embed_url ) : ?>
            <div class="avc-play-btn" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </div>
            <?php endif; ?>

        </div>

        <!-- Inline Video Modal (hidden until play is clicked) -->
        <?php if ( $embed_url ) : ?>
        <div class="avc-modal" id="avc-modal-<?php echo esc_attr( get_the_ID() . '_' . get_row_index() ); ?>" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( $section_title ?: __( 'Video', 'hairy-hair' ) ); ?>">
            <div class="avc-modal-backdrop"></div>
            <div class="avc-modal-inner">
                <button class="avc-modal-close" aria-label="<?php esc_attr_e( 'Close video', 'hairy-hair' ); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
                <div class="avc-modal-video-wrap">
                    <iframe src="" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- CTA Strip -->
        <?php if ( $cta_title || $cta_button ) : ?>
        <div class="avc-cta-strip"
             style="background-color:<?php echo esc_attr( $cta_bg_color ); ?>;color:<?php echo esc_attr( $cta_text_color ); ?>;">
            <div class="avc-cta-left">
                <?php if ( $cta_title ) : ?>
                    <h3 class="avc-cta-title" style="color:<?php echo esc_attr( $cta_text_color ); ?>;">
                        <?php echo esc_html( $cta_title ); ?>
                    </h3>
                <?php endif; ?>
                <?php if ( $cta_description ) : ?>
                    <p class="avc-cta-desc" style="color:<?php echo esc_attr( $cta_text_color ); ?>;">
                        <?php echo esc_html( $cta_description ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if ( $cta_button ) : ?>
            <div class="avc-cta-right">
                <a class="btn secondary-btn <?php echo esc_attr( $cta_button_style ); ?> avc-cta-btn"
                   href="<?php echo esc_url( $cta_button['url'] ); ?>"
                   <?php if ( $cta_button['target'] ) : ?>target="<?php echo esc_attr( $cta_button['target'] ); ?>"<?php endif; ?>>
                    <?php echo esc_html( $cta_button['title'] ); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div><!-- /.container -->
</section>

<?php if ( $has_thumbnail && $embed_url ) : ?>
<script>
(function () {
    var section = document.currentScript ? document.currentScript.previousElementSibling : null;
    // fallback: find last avc-section
    if ( ! section ) { section = document.querySelector( '.avc-section' ); }

    var wrap   = section ? section.querySelector( '.avc-video-wrap' ) : null;
    var modal  = section ? section.querySelector( '.avc-modal' ) : null;
    var iframe = modal  ? modal.querySelector( 'iframe' ) : null;
    var backdrop = modal ? modal.querySelector( '.avc-modal-backdrop' ) : null;
    var closeBtn = modal ? modal.querySelector( '.avc-modal-close' ) : null;

    if ( ! wrap || ! modal || ! iframe ) { return; }

    var embedSrc = wrap.getAttribute( 'data-embed' );

    function openModal() {
        iframe.src = embedSrc;
        modal.classList.add( 'avc-modal--open' );
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove( 'avc-modal--open' );
        iframe.src = '';
        document.body.style.overflow = '';
    }

    wrap.addEventListener( 'click', openModal );
    wrap.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); openModal(); }
    } );
    if ( backdrop ) { backdrop.addEventListener( 'click', closeModal ); }
    if ( closeBtn ) { closeBtn.addEventListener( 'click', closeModal ); }
    document.addEventListener( 'keydown', function ( e ) {
        if ( e.key === 'Escape' && modal.classList.contains( 'avc-modal--open' ) ) { closeModal(); }
    } );
}());
</script>
<?php endif; ?>
