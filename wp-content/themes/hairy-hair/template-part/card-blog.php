<?php
/* Blog card used by both PHP and AJAX loops */
?>
<div class="col-lg-4 col-md-6 col-12">
	<div class="latest-blog">
		<div class="blog-img">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'medium', [ 'alt' => get_the_title() ] );
			} else { ?>
				<img src="<?php echo get_template_directory_uri(); ?>/assets/placeholder.png" alt="<?php the_title_attribute(); ?>">
			<?php } ?>
			<div class="button-learn d-flex flex-wrap justify-content-center align-center">
				<a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn&nbsp;More</a>
			</div>
		</div>

		<div class="blog-desc">
			<a class="title-blog-main"> href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<p><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>

			<div class="tags"><?php the_tags( '', '' ); ?></div>

			<div class="writer">
				<div class="left-side">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
					<p><?php the_author(); ?></p>
				</div>
				<div class="right-side">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/clock.png" alt="clock">
					<p><?php echo get_the_date(); ?></p>
				</div>
			</div>
		</div><!-- /.blog-desc -->
	</div><!-- /.latest-blog -->
</div>
