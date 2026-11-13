<?php
/**
 * Component: Steps / Process
 *
 * ACF Flexible Content layout name: steps_section
 *
 * Fields: title, subtitle, button, steps[] { icon, title, description, step_time }
 *
 * Reuses the existing .steps-to-fllow / .steps-container CSS already in the theme.
 */

$title    = get_sub_field( 'title' );
$subtitle = get_sub_field( 'subtitle' );
$button   = get_sub_field( 'button' );
$count    = 1;
?>
<section class="steps-to-fllow">
    <div class="container">
        <?php if ( $title || $subtitle ) : ?>
        <div class="global-title text-center">
            <?php if ( $title ) : ?><h2><?php echo esc_html( $title ); ?></h2><?php endif; ?>
            <?php if ( $subtitle ) : ?><p><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="step-animation">
        <div class="steps-container" id="stepsFlow">
            <div class="timeline"></div>
            <div class="arrow" id="flowArrow"></div>

            <?php if ( have_rows( 'steps' ) ) : ?>
                <?php while ( have_rows( 'steps' ) ) : the_row(); ?>
                <?php
                $icon   = get_sub_field( 'icon' );
                $stitle = get_sub_field( 'title' );
                $desc   = get_sub_field( 'description' );
                $time   = get_sub_field( 'step_time' );
                ?>
                <div class="step" data-step="<?php echo esc_attr( $count ); ?>">
                    <div class="top-card">

                        <?php if ( $icon ) : ?>
                        <div class="step-icon">
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                        </div>
                        <?php endif; ?>

                        <?php if ( $stitle ) : ?><h5><?php echo esc_html( $stitle ); ?></h5><?php endif; ?>
                        <?php if ( $desc ) : ?><p><?php echo esc_html( $desc ); ?></p><?php endif; ?>

                    </div>
                    <div class="step-footer">
                        <h6>STEP <?php echo esc_html( $count ); ?></h6>
                        <?php if ( $time ) : ?><p><?php echo esc_html( $time ); ?></p><?php endif; ?>
                    </div>
                </div>
                <?php $count++; ?>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>

        <?php if ( $button ) : ?>
        <a class="btn secondary-btn m-auto d-flex justify-content-center"
           href="<?php echo esc_url( $button['url'] ); ?>"
           target="<?php echo esc_attr( $button['target'] ?: '_self' ); ?>">
            <?php echo esc_html( $button['title'] ); ?>
        </a>
        <?php endif; ?>

    </div>
</section>
