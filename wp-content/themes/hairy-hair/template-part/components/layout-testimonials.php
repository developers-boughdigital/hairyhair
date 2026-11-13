<?php
/**
 * Component: Testimonials (Loved by People)
 * Fields: title (text), testimonial (relationship → review CPT)
 */

$title        = get_sub_field( 'title' ) ?: 'Loved by people';
$testimonials = get_sub_field( 'testimonial' );

if ( ! $testimonials ) {
    return;
}
?>
<section class="loved-by-people">
    <div class="container">
        <div class="global-title d-flex flex-wrap justify-content-between align-items-center">
            <h2><?php echo esc_html( $title ); ?></h2>
        </div>
    </div>
    <div class="container">
        <div class="loved-by-slider bottom-arrows">
            <?php foreach ( $testimonials as $post ) :
                setup_postdata( $post );
                $video = get_field( 'video', $post->ID );
            ?>
            <div class="testi-slide">
                <?php if ( $video ) : ?>
                <div class="testi-img">
                    <video width="100%" height="397" controls>
                        <source src="<?php echo esc_url( $video['url'] ); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <?php else : ?>
                <div class="review-main-wrap">
                    <div class="testi-review">
                        <?php echo wp_kses_post( get_field( 'review', $post->ID ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="testi-client-info">
                    <div class="client-profile">
                        <?php $profile_icon = get_field( 'profile_icon', $post->ID ); ?>
                        <?php if ( $profile_icon ) : ?>
                        <img src="<?php echo esc_url( $profile_icon['url'] ); ?>" alt="<?php echo esc_attr( $profile_icon['alt'] ); ?>">
                        <?php endif; ?>
                    </div>
                    <div class="client-name">
                        <h6><?php echo esc_html( get_the_title( $post->ID ) ); ?> - <span><?php echo esc_html( get_field( 'location', $post->ID ) ); ?></span></h6>
                        <ul>
                            <?php
                            $stars = (int) get_field( 'stars', $post->ID );
                            for ( $i = 0; $i < $stars; $i++ ) : ?>
                <li><?php echo hh_star_svg(); ?></li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div><!-- .testi-client-info -->

                <?php if ( ! $video ) : ?>
                </div><!-- .review-main-wrap -->
                <?php endif; ?>
            </div><!-- .testi-slide -->
            <?php endforeach;
            wp_reset_postdata(); ?>
        </div>
    </div>
</section>
