<?php /* Template Name: FAQ */ ?>
<?php get_header();?>
<style>
form.search-form {
    margin-bottom: 30px;
    display: flex;
    flex-direction: row;
}

.search-form input {
    width: 100%;
    border: 1px solid #d3d4d4;
    border-radius: 12px;
    margin-right: 20px;
    padding: 10px 15px;
}
</style>
<div class="main-page">
    <section class="main-banner-content faq-banner">
        <div class="container">
            <div class="ban-left-content text-center">
                <div class="content-wrapper">
                    <nav aria-label="breadcrumb">
                        <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">

                            <?php if (function_exists('bcn_display')) {
                                bcn_display();
                                } ?>

                        </div>
                    </nav>
                    <h1><?php the_field( 'faq_banner_title' ); ?></h1>
                    <?php the_field( 'faq_banner_description' ); ?>
                    <div class="contact-btn">
                        <?php $faq_banner_button = get_field( 'faq_banner_button' ); ?>
                        <?php if ( $faq_banner_button ) : ?>
                        <a class="btn secondary-btn" href="<?php echo esc_url( $faq_banner_button['url'] ); ?>"
                            target="<?php echo esc_attr( $faq_banner_button['target'] ); ?>"><?php echo esc_html( $faq_banner_button['title'] ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="white-radius-bg">
        <div class="wrapper">
            <div class="container">
                <div class="top-category">
                    <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                        <input type="search" class="search-field" placeholder="Search..."
                        value="<?php echo get_search_query(); ?>" name="s" />
                        <button type="submit" class="search-submit btn secondary-btn">Search</button>
                    </form>
                    <div class="global-title">
                        <h2>Top Categories</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-3 col-12">
                            <div class="tabs-container">
                                <nav class="tabs">
                                    <ul>
                                        <?php $count = 1; ?>
                                        <?php if ( have_rows( 'faq' ) ) : ?>
                                        <?php while ( have_rows( 'faq' ) ) : the_row(); ?>
                                        <li class="<?php if($count == '1'){ echo 'active'; } ?>"
                                            data-tab="tab-<?php echo $count; ?>"><?php the_sub_field( 'title' ); ?></li>
                                        <?php $count = $count + 1; ?>
                                        <?php endwhile; ?>
                                        <?php else : ?>
                                        <?php // No rows found ?>
                                        <?php endif; ?>

                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-9 col-12">
                            <div class="faq-title">
                                <h3>General Questions</h3>
                            </div>

                            <?php $count = 1; ?>
                            <?php if ( have_rows( 'faq' ) ) : ?>
                            <?php while ( have_rows( 'faq' ) ) : the_row(); ?>
                            <div id="tab-<?php echo $count; ?>"
                                class="tab-content  p-0 <?php if($count == '1'){ echo 'current'; } ?>">
                                <div class="accordion" id="accordionExample">

                                    <?php $accordion = 1; ?>
                                    <?php if ( have_rows( 'faq_question_answer' ) ) : ?>
                                    <?php while ( have_rows( 'faq_question_answer' ) ) : the_row(); ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button
                                                class="accordion-button <?php if($accordion != '1'){ echo 'collapsed'; } ?>"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapseOne<?php echo $accordion; ?>"
                                                aria-expanded="true"
                                                aria-controls="collapseOne<?php echo $accordion; ?>">
                                                <?php the_sub_field( 'question' ); ?>
                                            </button>
                                        </h2>
                                        <div id="collapseOne<?php echo $accordion; ?>"
                                            class="accordion-collapse collapse  <?php if($accordion == '1'){ echo 'show'; } ?>"
                                            data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <?php the_sub_field( 'answer' ); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $accordion = $accordion + 1; ?>
                                    <?php endwhile; ?>
                                    <?php else : ?>
                                    <?php // No rows found ?>
                                    <?php endif; ?>



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
    </section>
</div>


<?php get_footer(); ?>