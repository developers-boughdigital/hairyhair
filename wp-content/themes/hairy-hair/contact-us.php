<?php /* Template Name: Contact */ ?>
<?php get_header(); ?>

    <div class="main-page contact-us">
        <section class="main-banner-content cms-main-banner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="ban-left-content cms-h1">
                        <nav aria-label="breadcrumb">
                            
                            <div class="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
                                
                                <?php if (function_exists('bcn_display')) {
                                bcn_display();
                                } ?>
                                
                            </div>
                        </nav>
                            <h1><?php the_title(); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-md-5">
                            <div class="bg-grey-3d">
                                <div class="top-contact">
                                    <h4>Contact Information</h4>
                                    <p>Have any questions or need further assistance?</p>

                                    <div class="each-contact">
                                        <div class="contact-icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/hairy-call.png" alt="hairy-call" />
                                        </div>
                                        <div class="contact-detail">
                                            <b>Call Us</b>
                                            <p><a href="tel:<?php the_field( 'phone', 'option' ); ?>"><?php the_field( 'phone', 'option' ); ?></a></p>
                                        </div>
                                    </div>

                                    <div class="each-contact">
                                        <div class="contact-icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/hairy-email.png" alt="hairy-email" />
                                        </div>
                                        <div class="contact-detail">
                                            <b>Email Us</b>
                                            <p><a href="mailto:<?php the_field( 'email', 'option' ); ?>"><?php the_field( 'email', 'option' ); ?></a></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bottom-contact">
                                    <div class="follow-us">
                                        <h4>Follow Us On</h4>
                                    </div>
                                    <div class="social-media">
                                <div class="each-social-link">
                                        <div class="social-icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin-icon.png" alt="linked in">
                                        </div>
                                        <p><a href="<?php the_field( 'linkedin', 'option' ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a></p>
                                </div>
                                    <div class="each-social-link">
                                        <div class="social-icon">
                                            <img src="<?php echo get_template_directory_uri(); ?>/assets/facebook-icon.png" alt="facebook">
                                        </div>
                                        <p><a href="<?php the_field( 'facebook', 'option' ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a></p>
                                    </div>
                                </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-7">
                            <div class="ml-60">
                                <div class="global-title">
                                    <h2>Get in touch with us. </br> We're here to assist you.</h2>
                                </div>
                                <div class="contact-form">
                                    <!-- <form>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="fullName" class="form-label">Full Name</label>
                                                    <input type="text" class="form-control" id="fullName"
                                                        placeholder="Ex. John Doe">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email Address</label>
                                                    <input type="email" class="form-control" id="email"
                                                        placeholder="john.doe@example.com">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-12">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Phone Number</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">+61</span>
                                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="Ex. 0412345678" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="message" class="form-label">Email Address</label>
                                                    <textarea class="form-control" id="message" rows="7"
                                                        placeholder="Ex. Want to know about hair pills"></textarea>
                                                </div>
            
                                            </div>
                                        </div>
                                    
                                        <div class="d-flex flex-wrap justify-content-end">
                                            <button type="submit" class="btn secondary-btn ml-auto">Leave US A MESSAGE</button>
                                        </div>
                                    </form> -->
                                    <?php echo do_shortcode('[contact-form-7 id="fb8fd65" title="Contact form 1"]'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <script src="<?php echo get_template_directory_uri(); ?>/dist/js/custom.min.js"></script>

    <?php 
    get_footer();
    ?>