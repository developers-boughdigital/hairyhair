<?php get_header(); ?>
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
.search-result-item {
    border: 1px solid #eaeaeb;
    padding: 16px;
    border-radius: 16px;
    margin: 16px 0;
    background: white;
    color: #0b1d38;
}
.search-result-item p {
    font-size: 14px;
}
</style>

<div class="main">
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
    <section class="form-search-section">
        <div class="container">
            <form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
                <input type="search" class="search-field" placeholder="Search..."
                    value="<?php echo get_search_query(); ?>" name="s" />
                <button type="submit" class="search-submit btn secondary-btn">Search</button>
            </form>
        </div>
    </section>
    <section class="search-results">
        <div class="container">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <div class="search-result-item">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <?php the_excerpt(); ?>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No results found. Try a different search.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php get_footer(); ?>
