<?php
/**
 * Component: HG – Hair Journey (Animated Steps)
 *
 * ACF Flexible Content layout name: hg_hair_journey
 *
 * Sub-fields:
 *   title (text)
 *   steps (repeater)
 *     journey_image       (image)
 *     journey_title       (text)
 *     journey_description (textarea)
 */

$title = get_sub_field( 'title' );

if ( ! $title && ! have_rows( 'steps' ) ) {
    return;
}
?>
<style>
#hairjourney { background: #fff; margin: 0 16px; border-radius: 16px; padding: 80px; text-align: center; margin-bottom: 75px; }
#hairjourney.steps-to-fllow .steps-container { display: flex; flex-direction: row; justify-content: space-around; }
#hairjourney .step-animation .journey-step {
    background: white;
    z-index: 1;
    display: flex;
    flex-wrap: wrap;
    border-radius: 16px;
    padding: 30px 20px;
    opacity: 0;
    transform: translateY(20px);
    transition: opacity .8s ease, transform .8s ease;
}
#hairjourney .step-animation .journey-step.show { opacity: 1; transform: translateY(0); }
#hairjourney .step-animation .arrow { transition: left 4s ease, opacity .3s ease; }
#hairjourney .step-animation .journey-step h5 { font-size: 24px; margin-bottom: 6px; }
#hairjourney .step-animation .journey-step p { font-size: 12px; max-width: 225px; margin: 0 auto; }
#hairjourney .step-animation .journey-timeline {
    position: absolute;
    top: 50%;
    bottom: auto;
    left: 0;
    height: 2px;
    width: 0;
    background-image: linear-gradient(to right, #0a2540 33%, transparent 0);
    background-size: 16px 2px;
    background-repeat: repeat-x;
    z-index: 1;
    transition: width 4s ease;
}
#hairjourney .step-animation .journey-timeline::after {
    position: absolute;
    height: 15px;
    width: 15px;
    border-radius: 100%;
    content: "";
    background: #3d506a;
    top: auto;
    margin-top: -6px;
    left: -6px;
}
@media screen and (max-width: 1024px) {
    #hairjourney { padding: 40px 20px; }
}
</style>
<section id="hairjourney" class="steps-to-fllow">
    <div class="container">
        <?php if ( $title ) : ?>
        <div class="global-title text-center">
            <h2 class="global-title text-center"><?php echo esc_html( $title ); ?></h2>
        </div>
        <?php endif; ?>
    </div>

    <?php if ( have_rows( 'steps' ) ) : ?>
    <div class="step-animation">
        <div class="steps-container" id="stepsJourney">
            <div class="journey-timeline"></div>
            <div class="arrow" id="journeyArrow"></div>

            <?php
            $count = 1;
            while ( have_rows( 'steps' ) ) : the_row();
                $journey_image       = get_sub_field( 'journey_image' );
                $journey_title       = get_sub_field( 'journey_title' );
                $journey_description = get_sub_field( 'journey_description' );
            ?>
            <div class="journey-step" data-step="<?php echo esc_attr( $count ); ?>">
                <div class="top-card">
                    <?php if ( $journey_image ) : ?>
                    <img src="<?php echo esc_url( $journey_image['url'] ); ?>"
                         alt="<?php echo esc_attr( $journey_image['alt'] ); ?>" />
                    <?php endif; ?>
                    <?php if ( $journey_title ) : ?>
                    <h5><?php echo esc_html( $journey_title ); ?></h5>
                    <?php endif; ?>
                    <?php if ( $journey_description ) : ?>
                    <p><?php echo esc_html( $journey_description ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php $count++; endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<script>
(function ($) {
    var $stepsSection = $('#stepsJourney');
    var $arrow        = $('#journeyArrow');
    var $steps        = $('.journey-step');
    var $timeline     = $('.journey-timeline');

    if (window.matchMedia('(min-width: 1140px)').matches) {
        var animated = false;

        function isInViewport($el) {
            var elementTop    = $el.offset().top;
            var elementBottom = elementTop + $el.outerHeight();
            var viewportTop   = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();
            return elementBottom > viewportTop + ($el.outerHeight() / 2) &&
                   elementTop < viewportBottom - ($el.outerHeight() / 2);
        }

        $(window).on('scroll.hgJourney', function () {
            if (!animated && $stepsSection.length && isInViewport($stepsSection)) {
                $arrow.css({ opacity: 1, left: '98%' });
                $timeline.css({ width: '98%' });
                $steps.each(function (index) {
                    var $step = $(this);
                    setTimeout(function () { $step.addClass('show'); }, index * 1000);
                });
                animated = true;
            }
        });
    } else {
        $steps.addClass('show');
    }
}(jQuery));
</script>
