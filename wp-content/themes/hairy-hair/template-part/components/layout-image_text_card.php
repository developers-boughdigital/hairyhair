<?php
/**
 * Component: Image Text Card (Grid Cell)
 * Card with a fixed-height image on top, tag, title, description, and button below.
 * Intended to be used as a standalone full-width card (the grid wrapping is implicit in the page).
 * Fields: image, image_height, tag, title, description, button, button_style, card_bg
 */

$image        = get_sub_field( 'image' );
$image_height = (int) ( get_sub_field( 'image_height' ) ?: 280 );
$tag          = get_sub_field( 'tag' );
$title        = get_sub_field( 'title' );
$description  = get_sub_field( 'description' );
$button       = get_sub_field( 'button' );
$button_style = get_sub_field( 'button_style' ) ?: 'dark-btn';
$card_bg      = get_sub_field( 'card_bg' ) ?: '#f8f8f8';
?>
<div class="component-image-text-card" style="background-color:<?php echo esc_attr( $card_bg ); ?>;padding:40px 0;">
    <div class="container">
        <div class="each-treatment" style="background:<?php echo esc_attr( $card_bg ); ?>;border-radius:8px;overflow:hidden;">

            <?php if ( $image ) : ?>
            <div class="each-treatment-img"
                 style="height:<?php echo $image_height; ?>px;overflow:hidden;background:#eee;">
                <img src="<?php echo esc_url( $image['url'] ); ?>"
                     alt="<?php echo esc_attr( $image['alt'] ); ?>"
                     style="width:100%;height:100%;object-fit:cover;" />
            </div>
            <?php endif; ?>

            <div class="p-4">
                <?php if ( $tag ) : ?>
                    <span class="itc-tag"><?php echo esc_html( $tag ); ?></span>
                <?php endif; ?>

                <?php if ( $title ) : ?>
                    <h4 class="mt-2"><?php echo esc_html( $title ); ?></h4>
                <?php endif; ?>

                <?php if ( $description ) : ?>
                    <p><?php echo esc_html( $description ); ?></p>
                <?php endif; ?>

                <?php if ( $button ) : ?>
                <div class="d-flex justify-content-end">
                    <a class="btn <?php echo esc_attr( $button_style ); ?>"
                       href="<?php echo esc_url( $button['url'] ); ?>"
                       target="<?php echo esc_attr( $button['target'] ); ?>">
                        <?php echo esc_html( $button['title'] ); ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
.itc-tag {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 3px;
    background: rgba(0,0,0,0.08);
    color: inherit;
}
</style>
