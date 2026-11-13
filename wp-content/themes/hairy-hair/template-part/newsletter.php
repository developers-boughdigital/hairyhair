<?php $newsletter_image = get_field( 'newsletter_image' ); ?>
<section class="newsletter mb-0  <?php if($newsletter_image['url'] ==''){ echo 'center-text'; } ?>">
    <div class="container">
    
        <div class="bg-primary news-bg-wrap" style="background-image: url('<?php echo esc_url( $newsletter_image['url'] ); ?>');">
            <div class="news-wrap <?php if($newsletter_image['url'] !=''){ echo 'news-bg-inner'; } ?>">
                <?php the_field( 'newsletter_title' ); ?>
                <ul class="tags">
                    <?php if ( have_rows( 'newsletter_tags' ) ) : ?>
                    <?php while ( have_rows( 'newsletter_tags' ) ) : the_row(); ?>
                    <li>
                        <a href="<?php the_sub_field( 'link' ); ?>"><?php the_sub_field( 'title' ); ?></a>
                    </li>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <?php // No rows found ?>
                    <?php endif; ?>
                </ul>
                
                <?php $newsletter_button = get_field( 'newsletter_button' ); ?>
                <?php if ( $newsletter_button ) : ?>
                <a class="btn secondary-btn d-flex m-auto" href="<?php echo esc_url( $newsletter_button['url'] ); ?>" target="<?php echo esc_attr( $newsletter_button['target'] ); ?>"><?php echo esc_html( $newsletter_button['title'] ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>