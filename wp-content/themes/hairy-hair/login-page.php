<?php 
/*
Template Name: Login
*/
defined( 'ABSPATH' ) || exit;
?>
<?php get_header();?>
<?php /* Remove WooCommerce wrappers if you want 100 % custom HTML */
//remove_all_actions( 'woocommerce_before_main_content' );
//remove_all_actions( 'woocommerce_after_main_content' );
 ?>
<div class="main-page login-page">

        <section class="white-radius-bg">
            <div class="wrapper">
                <div class="login-form">
                    <div class="container">
                        

                      <?php   echo do_shortcode( '[woocommerce_my_account]' ); ?>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php get_footer();?>    