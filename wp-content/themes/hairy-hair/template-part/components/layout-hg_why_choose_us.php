<?php
/**
 * Component: HG – Why Choose Us
 *
 * ACF Flexible Content layout name: hg_why_choose_us
 *
 * Sub-fields:
 *   title       (text)
 *   subtitle    (textarea)
 *   button      (link)
 *   extra_class (text)
 *   items       (repeater)
 *     icon        (image)
 *     title       (text)
 *     description (textarea)
 */

$title       = get_sub_field( 'title' );
$subtitle    = get_sub_field( 'subtitle' );
$button      = get_sub_field( 'button' );
$extra_class = get_sub_field( 'extra_class' );

if ( ! $title && ! $subtitle && ! $button && ! have_rows( 'items' ) ) {
    return;
}
?>
<style>
.why-choose-us {
    background: #3B82C4;
    margin: 0 16px;
    padding: 65px 0;
    border-radius: 16px;
    margin-bottom: 50px;
}
.why-choose-us .wcu-header {
    text-align: center;
    margin-bottom: 40px;
}
.why-choose-us .wcu-header h2,
.why-choose-us .wcu-header p {
    color: #fff;
    margin-bottom: 0;
}
.why-choose-us .wcu-header h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
}
.why-choose-us .wcu-header .wcu-subtitle {
    font-size: 14px;
    font-weight: 400;
    max-width: 560px;
    margin: 0 auto;
    opacity: 0.9;
}
.why-choose-us .wcu-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}
.why-choose-us .wcu-card {
    flex: 1 1 calc(33.333% - 14px);
    min-width: 240px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 12px;
    padding: 28px 24px;
}
.why-choose-us .wcu-card .wcu-card-icon {
    width: 40px;
    height: 40px;
    margin-bottom: 20px;
    display: block;
}
.why-choose-us .wcu-card .wcu-card-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
.why-choose-us .wcu-card h6 {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 10px;
    line-height: 1.3;
}
.why-choose-us .wcu-card p {
    font-size: 13px;
    font-weight: 400;
    color: rgba(255, 255, 255, 0.85);
    margin: 0;
    line-height: 1.6;
}
.why-choose-us .wcu-footer {
    text-align: center;
    margin-top: 40px;
}
@media screen and (max-width: 1024px) {
    .why-choose-us .wcu-card { flex: 1 1 calc(50% - 10px); }
}
@media screen and (max-width: 600px) {
    .why-choose-us .wcu-card { flex: 1 1 100%; }
}
</style>
<section class="why-choose-us<?php echo $extra_class ? ' ' . esc_attr( $extra_class ) : ''; ?>">
    <div class="container">

        <?php if ( $title || $subtitle ) : ?>
        <div class="global-title text-center white-title">
            <?php if ( $title ) : ?>
                <h2 style="color:#fff;"><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
            <?php if ( $subtitle ) : ?>
                <p class="wcu-subtitle"><?php echo esc_html( $subtitle ); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ( have_rows( 'items' ) ) : ?>
        <div class="wcu-grid">
            <?php while ( have_rows( 'items' ) ) : the_row();
                $item_icon  = get_sub_field( 'icon' );
                $item_title = get_sub_field( 'title' );
                $item_desc  = get_sub_field( 'description' );
            ?>
            <div class="wcu-card">
                <?php if ( $item_icon ) : ?>
                <span class="wcu-card-icon">
                    <img src="<?php echo esc_url( $item_icon['url'] ); ?>"
                         alt="<?php echo esc_attr( $item_icon['alt'] ); ?>" />
                </span>
                <?php endif; ?>
                <?php if ( $item_title ) : ?>
                <h6><?php echo esc_html( $item_title ); ?></h6>
                <?php endif; ?>
                <?php if ( $item_desc ) : ?>
                <p><?php echo esc_html( $item_desc ); ?></p>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <?php if ( $button ) : ?>
        <div class="wcu-footer">
            <a class="btn secondary-btn"
               href="<?php echo esc_url( $button['url'] ); ?>"
               target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
                <span><?php echo esc_html( $button['title'] ); ?></span>
            </a>
        </div>
        <?php endif; ?>

    </div>
</section>
