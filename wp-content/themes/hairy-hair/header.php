<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
                <link rel="preload" as="image" href="https://hairyhair.com.au/wp-content/uploads/2026/03/mainbanner.png" fetchpriority="high">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <!--<body class="bg-grey-ee">-->
        <header class="header-main-wrap">
            <div class="header-top">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="social-links">
                                <div class="social-icons">
                                    <a href="<?php the_field('facebook', 'option'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/facebook-icon.png" alt="facebook-icon"
                                    title="facebook-icon"></a>
                                    <a href="<?php the_field('linkedin', 'option'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin-icon.png" alt="linkedin-icon"
                                    title="linkedin-icon"></a>
                                </div>
                                <div class="call-action">
                                    <a href="tel:<?php the_field( 'phone', 'option' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/assets/call-icon.png" alt="call-icon"
                                    title="call-icon">Call Us : <?php the_field( 'phone', 'option' ); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <div class="cms-links">
                                
                                <?php
                                wp_nav_menu(array(
                                'theme_location' => 'top-menu',
                                'container' => false,
                                'menu_class' => 'list-cms-inner',
                                'fallback_cb' => '__return_false',
                                'walker' => new bootstrap_5_wp_nav_menu_walker()
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid p-0">
                    <a class="navbar-brand" href="<?php echo home_url();?>">
                        <?php $site_logo = get_field( 'site_logo', 'option' ); ?>
                        <?php if ( $site_logo ) : ?>
                        <img src="<?php echo esc_url( $site_logo['url'] ); ?>" alt="<?php echo esc_attr( $site_logo['alt'] ); ?>" />
                        <?php endif; ?>
                    </a>
                     <div class="mobile-wrap">                 <!-- Extra icons only for mobile -->
                        <div class="mobile-extra-icons d-lg-none d-flex justify-content-center gap-2">
                        <!-- Quiz Icon -->
                        <?php $header_button_1 = get_field( 'header_button_1', 'option' ); ?>
                            <?php if ( $header_button_1 ) : ?>
                            <a href="<?php echo esc_url( $header_button_1['url'] ); ?>" target="<?php echo esc_attr( $header_button_1['target'] ); ?>" class="quiz-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 18 18" fill="none">
                                    <path d="M12.7002 2.01129C13.3484 2.00123 13.9982 2.00961 14.6467 2.00884C15.0848 2.00833 15.7845 1.96958 16.1861 2.05846C16.5966 2.14923 16.9957 2.35491 17.2971 2.64253C17.7591 3.0833 17.9918 3.66588 17.9977 4.29324L17.9951 9.10867C17.9982 9.57941 17.9976 10.0502 17.9933 10.5209C17.9931 10.8496 18.0232 11.2017 17.9573 11.5249C17.8696 11.9726 17.6463 12.3841 17.3162 12.7059C17.0996 12.9225 16.8382 13.0917 16.5493 13.2017C16.3538 13.2798 16.1578 13.3309 15.9471 13.3509C15.7042 13.3737 15.4526 13.3636 15.2086 13.3654C14.7335 13.3687 14.2583 13.3683 13.7832 13.3637L10.3734 13.3683C10.1666 13.3692 9.58075 13.3265 9.4182 13.408C9.36179 13.4363 9.31534 13.4799 9.27501 13.5271C9.19032 13.6261 9.12374 13.7475 9.04957 13.8546C8.91143 14.0543 8.7546 14.2527 8.58741 14.4301C8.55152 14.4682 8.50272 14.5322 8.45922 14.5591C8.34962 14.6935 8.20724 14.814 8.07864 14.9306C7.43837 15.5112 6.72826 15.9457 5.93371 16.2941C6.31153 15.6102 6.56403 14.7576 6.62616 13.9836C6.64821 13.7985 6.66026 13.6451 6.54585 13.4869C6.49582 13.4378 6.43303 13.3885 6.36192 13.3746C6.23154 13.349 6.08951 13.3615 5.95709 13.3621C5.75753 13.3613 5.55793 13.362 5.35837 13.3642C4.57041 13.3704 3.78234 13.3698 2.99433 13.3622C2.58358 13.3576 2.17865 13.3943 1.7733 13.3072C1.69507 13.2882 1.61781 13.2657 1.54171 13.2397C1.04832 13.0667 0.534506 12.6809 0.305765 12.2098C0.163319 11.9943 0.0755581 11.6988 0.0363463 11.4468C-0.0094765 11.1524 0.003715 10.8488 0.00396004 10.5517L0.00547635 9.40425L0.00390892 6.35287C0.00360262 5.77319 -0.00613257 5.19178 0.00632372 4.61231C0.00333217 4.49464 0.00284204 4.37697 0.00485342 4.25929C0.0104537 3.64531 0.268998 3.05951 0.721928 2.63462C1.01253 2.36344 1.40505 2.14341 1.80055 2.06372C2.22148 1.97892 2.66995 2.01103 3.09837 2.01175L4.60165 2.01047L8.05439 2.01006C8.51895 2.01114 8.98499 2.00098 9.44934 2.01119L12.7002 2.01129Z" fill="#0B1D38"></path>
                                    <path d="M4.37848 5.49976L4.53321 5.50583C5.09921 5.54979 5.60578 5.71458 5.9834 6.14983C6.07585 6.25643 6.14472 6.37389 6.21144 6.49723C6.24498 6.57314 6.282 6.64911 6.30711 6.72808C6.36556 6.88195 6.39905 7.04873 6.41794 7.21143C6.49161 7.84649 6.39099 8.44812 5.97906 8.95898C6.16779 9.08227 6.36556 9.18151 6.56681 9.28259C6.51223 9.49869 6.34856 9.80376 6.25346 10.0145C5.95976 9.91679 5.57546 9.64765 5.34011 9.44325C5.27865 9.47577 5.21341 9.50093 5.1475 9.52334C4.57676 9.71769 3.75122 9.67124 3.21591 9.41236C2.78611 9.20459 2.47297 8.8455 2.3214 8.3986C2.12323 7.81402 2.1487 7.05414 2.43014 6.50106C2.65497 6.05917 3.02677 5.77512 3.49648 5.62095C3.60037 5.58726 3.70701 5.56541 3.81376 5.54264C4.0028 5.51191 4.18775 5.50639 4.37848 5.49976Z" fill="white"></path>
                                    <path d="M4.18566 6.45457C4.4431 6.44151 4.66675 6.45559 4.86784 6.63202C5.0968 6.83286 5.15725 7.14748 5.17297 7.43576C5.18905 7.7317 5.16506 8.04724 5.03641 8.31853C4.84788 8.18983 4.50452 7.97128 4.30521 7.87577C4.21567 8.02294 4.14042 8.17967 4.04772 8.32429C4.2338 8.44003 4.43284 8.5456 4.6081 8.67649C4.35422 8.73254 4.07314 8.72535 3.84806 8.5822C3.8428 8.57888 3.83764 8.57541 3.83249 8.57204C3.75479 8.51543 3.697 8.45335 3.63793 8.37846C3.41796 8.03326 3.43884 7.54394 3.4929 7.15213C3.52394 7.01041 3.56468 6.8715 3.6509 6.75184C3.78225 6.56954 3.96767 6.48985 4.18566 6.45457Z" fill="#0B1D38"></path>
                                    <path d="M9.87856 6.62777C9.95544 6.62721 10.2505 6.60985 10.2995 6.64176L10.3039 6.65626C10.2934 6.95495 10.2988 7.25533 10.2982 7.55429C10.2977 7.82404 10.2901 8.09603 10.2996 8.36553C10.2967 8.76025 10.297 9.15497 10.3003 9.54969L9.64506 9.55112C9.53004 9.55092 9.4066 9.54163 9.29261 9.55245L9.21771 9.54668C9.21062 9.38414 9.20807 9.22006 9.19581 9.05787C9.02485 9.29577 8.86664 9.44182 8.59424 9.56312C8.47891 9.59814 8.36543 9.62137 8.24566 9.6349C8.09507 9.63939 7.95759 9.62606 7.80995 9.60054C7.53611 9.50793 7.36168 9.39021 7.22757 9.13236C7.17243 9.01683 7.14384 8.89252 7.12771 8.76643C7.0881 8.45737 7.09841 8.13039 7.09836 7.81868L7.0957 6.63124C7.33217 6.62583 7.56935 6.6289 7.80592 6.62864L8.25981 6.62869C8.25582 7.00412 8.25541 7.37959 8.25853 7.75507C8.25863 7.9753 8.23801 8.21809 8.27349 8.43572C8.28655 8.51613 8.31009 8.59449 8.37053 8.65335C8.4491 8.72982 8.56677 8.75836 8.6749 8.75428C8.79298 8.74866 8.90386 8.69618 8.98196 8.60878C9.05088 8.53292 9.08299 8.44333 9.10173 8.34506C9.14425 8.12161 9.12531 7.86916 9.1248 7.64128L9.12368 6.62941L9.87856 6.62777Z" fill="white"></path>
                                    <path d="M13.6061 6.62979L14.0453 6.63076L14.3926 6.62989L14.83 6.62994L15.2095 6.62984L15.5499 6.62928C15.5493 6.86125 15.5531 7.09419 15.5473 7.32606C15.2173 7.67887 14.8632 8.01616 14.5189 8.3557C14.3765 8.49609 14.2291 8.63387 14.0936 8.78064L14.8456 8.77712L15.6616 8.77676C15.6615 9.03528 15.6563 9.29513 15.6639 9.55344C15.3768 9.57621 15.0749 9.55421 14.7863 9.55395L13.2871 9.55451C13.1777 9.55462 12.7813 9.57274 12.6985 9.54752L12.6846 9.52113C12.7151 9.3035 12.6922 9.05779 12.691 8.8369C13.1633 8.35851 13.649 7.89287 14.1182 7.41167H12.7906L12.7889 6.63612C13.0603 6.62412 13.3343 6.63035 13.6061 6.62979Z" fill="white"></path>
                                    <path d="M11.024 6.6343C11.4069 6.61429 11.8023 6.62384 12.1856 6.63369C12.1723 7.21919 12.1853 7.80581 12.186 8.39141C12.1865 8.77485 12.1657 9.16737 12.1907 9.54969C11.9965 9.55081 11.7945 9.53876 11.6014 9.55199C11.4803 9.55025 11.1883 9.52743 11.0837 9.55975C11.055 9.55189 11.0403 9.55331 11.0225 9.52886L11.0249 7.42283C11.0245 7.16017 11.0312 6.89686 11.024 6.6343Z" fill="white"></path>
                                    <path d="M11.1002 5.46057L11.1083 5.45996C11.3515 5.48737 11.611 5.46297 11.8565 5.46701L12.1895 5.47012C12.1747 5.73344 12.1828 6.00125 12.1825 6.26508L11.0216 6.26763C11.0237 6.00569 11.0017 5.73308 11.0231 5.47288C11.0473 5.45736 11.0718 5.46165 11.1002 5.46057Z" fill="white"></path>
                                    </svg>
                            

                            </a>
                            <?php endif; ?>

                        <!-- Login / Profile -->
                        <?php if ( !is_user_logged_in() ) : ?>
                            <?php $header_button_2 = get_field( 'header_button_2', 'option' ); ?>
                            <?php if ( $header_button_2 ) : ?>
                            <a  href="<?php echo esc_url( $header_button_2['url'] ); ?>" target="<?php echo esc_attr( $header_button_2['target'] ); ?>" class="quiz-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <path d="M24.0008 21.9C26.3204 21.9 28.2008 20.0196 28.2008 17.7C28.2008 15.3804 26.3204 13.5 24.0008 13.5C21.6812 13.5 19.8008 15.3804 19.8008 17.7C19.8008 20.0196 21.6812 21.9 24.0008 21.9Z" fill="#0B1D38" stroke="#0B1D38" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M33.4508 34.4969C33.4508 29.2779 29.2198 25.0469 24.0008 25.0469C18.7818 25.0469 14.5508 29.2779 14.5508 34.4969" fill="#0B1D38"/>
                                    </svg>
                            </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                            
                            <?php
                            wp_nav_menu(array(
                            'theme_location'    => 'primary-menu', // Ensure this matches the registered menu location
                            'depth'             => 2, // Bootstrap 5 supports dropdowns up to depth 2
                            'container_id'      => 'navbarNavDropdown',
                            'menu_class'        => 'navbar-nav ',
                            'fallback_cb'       => '__return_false',
                            'walker'            => new bootstrap_5_wp_nav_menu_walker(), // Use your custom walker class
                            ));
                            ?>
                            
                            
                        </div>
                    </div>
                    <div class="top-right-btn d-flex align-items-center gap-2">
                        <div class="contact-btn">
                            
                            <?php $header_button_1 = get_field( 'header_button_1', 'option' ); ?>
                            <?php if ( $header_button_1 ) : ?>
                            <a class="btn secondary-btn" href="<?php echo esc_url( $header_button_1['url'] ); ?>" target="<?php echo esc_attr( $header_button_1['target'] ); ?>"><?php echo esc_html( $header_button_1['title'] ); ?></a>
                            <?php endif; ?>
                        </div>
                        <?php if ( !is_user_logged_in() ) : ?>
                            <div class="contact-btn login-btn">
                                
                                <?php $header_button_2 = get_field( 'header_button_2', 'option' ); ?>
                                <?php if ( $header_button_2 ) : ?>
                                <a class="btn dark-btn" href="<?php echo esc_url( $header_button_2['url'] ); ?>" target="<?php echo esc_attr( $header_button_2['target'] ); ?>"><?php echo esc_html( $header_button_2['title'] ); ?></a>
                                <?php endif; ?>
                            </div>
                            <div class="profile-section">
                                <div class="cart-icon">
                                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/cart-icon-main.png" alt="cart-icon-main" title="cart-icon-main">
                                        <span class="cart-count hairy-cart-count">
                                            <?php echo WC()->cart->get_cart_contents_count(); ?>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php else : ?>

                        <?php if ( is_user_logged_in() ) : 
                            $user_id   = get_current_user_id();
                            $profile_picture_id = get_user_meta( $user_id, 'profile_picture_id', true );
                            // Determine avatar URL: custom upload or WP avatar fallback
                            if ( $profile_picture_id ) {
                                $avatar = wp_get_attachment_image_url( $profile_picture_id, 'thumbnail' );
                            } else {
                                $avatar = get_avatar_url( $user_id, [ 'size' => 48 ] );
                            }
                            $edit_url  = wc_get_account_endpoint_url( 'edit-account' );
                            $orders_url= wc_get_account_endpoint_url( 'orders' );
                            $logout_url= wc_logout_url( wc_get_page_permalink('myaccount') );
                            ?>
                        <div class="profile-section">
                            <div class="cart-icon">
                                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/cart-icon-main.png" alt="cart-icon-main" title="cart-icon-main">
                                <span class="cart-count hairy-cart-count">
                                     <?php echo WC()->cart->get_cart_contents_count(); ?>
                                </span>
                        </a>
                            </div>
                            
                            <div class="profile-dropdown-wrapper">
                                <div class="profile-dropdown" id="profileToggle">
                                 <img 
                                        class="profile-pic" 
                                        src="<?php echo esc_url( $avatar ); ?>" 
                                        alt="<?php echo esc_attr( wp_get_current_user()->display_name ); ?>" 
                                        title="<?php echo esc_attr( wp_get_current_user()->display_name ); ?>"
                                    >
                                <img class="arrow-icon" src="<?php echo get_template_directory_uri(); ?>/assets/profile-icon.png" alt="profile-icon" title="profile-icon">
                                </div>
                            
                                <ul class="dropdown-menu" id="profileMenu">
                                    <li><a href="<?php echo esc_url( $edit_url ); ?>">My Profile</a></li>
                                    <li><a href="<?php echo esc_url( $orders_url ); ?>">Orders</a></li>
                                    <li><a href="<?php echo esc_url( $logout_url ); ?>">Logout</a></li>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                           <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>