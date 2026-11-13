<?php
/**
 * Component: HG – How It Works (Steps)
 *
 * ACF Flexible Content layout name: hg_how_it_works
 *
 * Sub-fields:
 *   title  (text)
 *   button (link)
 *   steps  (repeater)
 *     icon        (image)
 *     step_time   (text)
 *     title       (text)
 *     description (textarea)
 */

$title  = get_sub_field( 'title' );
$button = get_sub_field( 'button' );
$count  = 1;
?>

<section id="how-it-works" class="steps-to-fllow">
    <div class="container">
        <div class="global-title text-center">
            <?php if ( $title ) : ?>
            <h2><?php echo esc_html( $title ); ?></h2>
            <?php endif; ?>
        </div>
    </div>

    <div class="step-animation">
        <div class="steps-container" id="stepsFlow">
            <div class="timeline"></div>
            <div class="arrow" id="flowArrow"></div>

            <?php if ( have_rows( 'steps' ) ) : ?>
                <?php while ( have_rows( 'steps' ) ) : the_row(); ?>
                    <?php
                    $icon  = get_sub_field( 'icon' );
                    $time  = get_sub_field( 'step_time' );
                    $stitle = get_sub_field( 'title' );
                    $desc  = get_sub_field( 'description' );
                    ?>
                    <div class="step" data-step="<?php echo esc_attr( $count ); ?>">
                        <div class="top-card">
                            <div class="step-footer">
                                <div class="step-icon">
                                    <?php if ( $icon ) : ?>
                                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                         alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                                    <?php endif; ?>
                                </div>
                                <div class="step-footer-wrap">
                                    <h6>STEP <?php echo esc_html( $count ); ?></h6>
                                    <?php if ( $time ) : ?>
                                    <p><?php echo esc_html( $time ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ( $stitle ) : ?>
                            <h5><?php echo esc_html( $stitle ); ?></h5>
                            <?php endif; ?>
                            <?php if ( $desc ) : ?>
                            <p><?php echo esc_html( $desc ); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php $count++; ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $button ) : ?>
    <div class="d-flex flex-wrap justify-content-center">
        <a class="btn secondary-btn"
           href="<?php echo esc_url( $button['url'] ); ?>"
           target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
            <span><?php echo esc_html( $button['title'] ); ?></span>
        </a>
    </div>
    <?php endif; ?>
</section>
