<section class="faq-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-12">
                <div class="global-title" id="gummie2">
                <?php echo str_replace( [ '<strong>', '</strong>' ], '', get_field( 'faq_content' ) ); ?>
                    <?php $faq_button = get_field( 'faq_button' ); ?>
                    <?php if ( $faq_button ) : ?>
                    <a class="btn secondary-btn" href="<?php echo esc_url( $faq_button['url'] ); ?>" target="<?php echo esc_attr( $faq_button['target'] ); ?>"><?php echo esc_html( $faq_button['title'] ); ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-12">
                <div class="all-faq">
                    <div class="container">
                        <div class="accordion" id="accordionExample">
                            <?php $count = 1; ?>
                            <?php if ( have_rows( 'faq' ) ) : ?>
                            <?php while ( have_rows( 'faq' ) ) : the_row(); ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                <button class="accordion-button <?php if($count != '1'){ echo 'collapsed'; } ?>" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne<?php echo $count; ?>" aria-expanded="true"
                                aria-controls="collapseOne<?php echo $count; ?>">
                                <?php the_sub_field( 'title' ); ?>
                                </button>
                                </h2>
                                <div id="collapseOne<?php echo $count; ?>" class="accordion-collapse collapse <?php if($count == '1'){ echo 'show'; } ?>"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <?php the_sub_field( 'content' ); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $count = $count + 1; ?>
                            <?php endwhile; ?>
                            <?php else : ?>
                            <?php // No rows found ?>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>