<?php get_header(); ?>


    <div class="main-page">
        <div class="error-page">
            <div class="container">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/404.svg" alt="page not found">
                <h1>PAGE NOT FOUND</h1>
                <p>We can’t find the page you were looking for</p>
                <a href="<?php echo home_url(); ?>" class="btn secondary-btn">Go To Home</a>
            </div>
        </div>     
        
    </div>
  

<?php get_footer(); ?>