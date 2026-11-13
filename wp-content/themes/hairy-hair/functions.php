<?php

define('QUIZ_BUTTON', 'Buy Now');


include_once('navwalker.php');

$hiaryhair_debug_file = get_stylesheet_directory() . '/debug.php';

if ( file_exists( $hiaryhair_debug_file ) ) {
    require_once $hiaryhair_debug_file;
}


add_action( 'after_setup_theme', 'setup_woocommerce_support' );

 function setup_woocommerce_support()
{
  add_theme_support('woocommerce');
}

function hairy_hair_setup() {
    add_theme_support('title-tag');
}
add_action('after_setup_theme', 'hairy_hair_setup');

function hairy_hair_scripts() {
    wp_enqueue_style('hairy-hair-style', get_stylesheet_uri());
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/dist/css/style.min.css?v=117', [], null);
    wp_enqueue_style('acf-components', get_template_directory_uri() . '/dist/css/acf-components.css', [], '1.0');
	
	
    wp_enqueue_script('custom-style',get_stylesheet_directory_uri().'/dist/js/custom.min.js?v=19');
    wp_enqueue_script('acf-components', get_template_directory_uri() . '/dist/js/acf-components.js', [], '1.0', true);

}
add_action('wp_enqueue_scripts', 'hairy_hair_scripts');


register_nav_menus([
    'top-menu' => __('Top Menu'),
    'primary-menu' => __('Primary Menu'),
    'links-menu'  => __('Links Menu'),
    'learn-menu'  => __('Learn Menu'),
    'support-menu'  => __('Support Menu'),
    'products-menu'  => __('Products Menu'),
]);


function enqueue_ajax_filter_scripts() {
    // Enqueue jQuery (WordPress includes it by default, but it's good to ensure it is loaded)
    wp_enqueue_script('jquery');
    
    // Enqueue your custom AJAX filter script
    wp_enqueue_script('ajax-post-filter', get_template_directory_uri() . '/dist/js/ajax-post-filter.js?v=1', array('jquery'), null, true);
    
    // Localize the script to provide the AJAX URL and nonce to the script
    wp_localize_script('ajax-post-filter', 'ajax_filter', array(
        'ajaxurl' => admin_url('admin-ajax.php'), // WordPress AJAX URL
        'nonce' => wp_create_nonce('ajax-filter-nonce') // Nonce for security
    ));
}

add_action('wp_enqueue_scripts', 'enqueue_ajax_filter_scripts');

function filter_posts_by_category() {
    // Check nonce
    if ( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'ajax-filter-nonce') ) {
        die('Permission denied');
    }

    // Get category ID and sort option
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $sort_option = isset($_POST['sort_option']) ? $_POST['sort_option'] : 'date';

    // Arguments for WP_Query
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 3, // Number of posts to display
        'orderby' => 'date', // Default sort
        'order' => 'DESC'
    );

    if ($category_id) {
        $args['cat'] = $category_id;
    }

    // Adjust sorting based on the selected option
    if ($sort_option === 'oldest') {
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
    } elseif ($sort_option === 'popular') {
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'post_views_count'; // Assuming a custom field for post views
        $args['order'] = 'DESC';
    } elseif ($sort_option === 'highest_rated') {
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'rating'; // Assuming a custom field for ratings
        $args['order'] = 'DESC';
    }

    // WP Query
    $query = new WP_Query($args);
    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            ?>
<div class="col-lg-4 col-md-6 col-12">
    <div class="latest-blog">
        <div class="blog-img">
            <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('full'); ?>" alt="<?php the_title(); ?>">
            <?php else : ?>
            <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png" alt="blog-1">
            <?php endif; ?>
            <div class="button-learn d-flex flex-wrap justify-content-center align-center">
                <a href="<?php the_permalink(); ?>" class="btn dark-btn">Learn More</a>
            </div>
        </div>
        <div class="blog-desc">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            <p><?php the_excerpt(); ?></p>

            <div class="tags">
                <?php the_category(' '); ?>
            </div>
            <div class="writer">
                <div class="left-side">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/testi-man.png" alt="test1">
                    <p><?php the_author(); ?></p>
                </div>
                <div class="right-side">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/clock.png" alt="clock">
                    <p><?php the_time('F j, Y'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endwhile;
    else :
        echo 'No posts found.';
    endif;

    wp_die(); // Required to terminate AJAX
}

add_action('wp_ajax_filter_posts', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts', 'filter_posts_by_category');
require_once get_template_directory() . '/inc/faq-schema.php';
require_once get_template_directory() . '/inc/acf-components.php';

require_once get_template_directory() . '/inc/user-profile-dob.php';


/**
 * Add a body-class only if single.php actually exists in the theme.
 */
function mytheme_add_body_class_if_single_template( $classes ) {
    // 1) Are we on a single post?
    // 2) Does single.php live in the (child) theme?
    if ( is_single() && locate_template( 'single.php', false, false ) ) {
        $classes[] = 'bg-grey-ee blog-artical-page';
    }
    if ( is_single() && locate_template( 'single-comparison.php', false, false ) ) {
        $classes[] = 'bg-grey-ee comparison-page';
    }
    if ( is_shop() && locate_template( 'archive-product.php', false, false ) ) {
        $classes[] = 'bg-grey-ee';
    }
    if ( is_single() && locate_template( 'single-treatments.php', false, false ) ) {
        $classes[] = 'bg-grey-ee prp-treatment-detail';
    }
    if ( is_category() ) {
        $classes[] = 'bg-grey-ee prp-treatment-detail';
    }
    
    return $classes;
}
add_filter( 'body_class', 'mytheme_add_body_class_if_single_template' );



function mytheme_setup() {
    // 300×300 hard-crop (you can change dimensions)
    add_image_size( 'square-300', 400, 300, true );
  }
  add_action( 'after_setup_theme', 'mytheme_setup' );





// Add this to your theme’s functions.php or a custom plugin

// 1) Add “Import CSV” submenu under Comparison CPT
add_action('admin_menu', 'ccsv_add_import_submenu');
function ccsv_add_import_submenu() {
    add_submenu_page(
        'edit.php?post_type=comparison',
        'Import CSV',
        'Import CSV',
        'edit_posts',
        'ccsv-import',
        'ccsv_render_import_page'
    );
}

// 2) Render upload form + handle submissionf
function ccsv_render_import_page() {
    ?>
<div class="wrap">
    <h1>Import Comparison CSV</h1>
    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('ccsv_import_nonce', 'ccsv_import_nonce_field'); ?>
        <input type="file" name="ccsv_file" accept=".csv" required />
        <?php 
            submit_button('Import CSV'); 
            // PDF download link
            $pdf_url = esc_url( home_url() . '/wp-content/themes/hairy-hair/sample-import.csv' );
            echo ' <a href="' . $pdf_url . '" target="_blank" class="button">Download Sample CSV</a>';
        ?>
    </form>
</div>
<?php

    // Handle the uploaded file
    if (
        ! empty($_POST['ccsv_import_nonce_field'])
        && wp_verify_nonce($_POST['ccsv_import_nonce_field'], 'ccsv_import_nonce')
        && ! empty($_FILES['ccsv_file'])
    ) {
        $file = $_FILES['ccsv_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $up = wp_handle_upload($file, ['test_form' => false]);
            if (! empty($up['file'])) {
                ccsv_process_csv($up['file']);
            } else {
                echo '<div class="error"><p>Upload failed.</p></div>';
            }
        } else {
            echo '<div class="error"><p>Upload error: ' . esc_html($file['error']) . '</p></div>';
        }
    }
}

// 3) CSV parsing & ACF population
function ccsv_process_csv($file_path) {
    if (! file_exists($file_path) || false === ($h = fopen($file_path, 'r'))) {
        echo '<div class="error"><p>Could not open CSV file.</p></div>';
        return;
    }

    // Read header row
    $header = fgetcsv($h);
    if (! $header) {
        echo '<div class="error"><p>Invalid or empty CSV header.</p></div>';
        fclose($h);
        return;
    }

    // Process each CSV line
    while ($row = fgetcsv($h)) {
        if (count($row) !== count($header)) {
            continue;
        }
        $data = array_combine($header, $row);

        // Insert the Comparison post
        $post_id = wp_insert_post([
            'post_type'   => 'comparison',
            'post_title'  => sanitize_text_field($data['post_title'] ?? ''),
            'post_name'   => sanitize_title($data['post_name'] ?? ''),
            'post_status' => 'publish',
        ]);
        if (is_wp_error($post_id)) {
            continue;
        }

        // -- Simple fields --
        update_field('treatment_1_title', sanitize_text_field($data['t1_title'] ?? ''), $post_id);
        update_field('treatment_1_icon', intval($data['t1_icon'] ?? 0), $post_id);
        update_field('treatment_1_image', intval($data['t1_image'] ?? 0), $post_id);
        update_field('treatment_2_title', sanitize_text_field($data['t2_title'] ?? ''), $post_id);
        update_field('treatment_2_icon', intval($data['t2_icon'] ?? 0), $post_id);
        update_field('treatment_2_image', intval($data['t2_image'] ?? 0), $post_id);
        update_field('welcome_text', wp_kses_post($data['welcome_text'] ?? ''), $post_id);

        // -- compare_treatment repeater (10 rows) --
        $compare_rows = [];
        for ($i = 1; $i <= 10; $i++) {
            $t       = sanitize_text_field($data["compare_treatment_title_{$i}"] ?? '');
            $c1      = sanitize_text_field($data["compare_treatment_treatment_1_{$i}"] ?? '');
            $c2      = sanitize_text_field($data["compare_treatment_treatment_2_{$i}"] ?? '');
            if ($t !== '') {
                $compare_rows[] = [
                    'title'        => $t,
                    'treatment_1'  => $c1,
                    'treatment_2'  => $c2,
                ];
            }
        }
        update_field('compare_treatment', $compare_rows, $post_id);

        // -- treatment_details repeater (10 rows) --
        $detail_rows = [];
        for ($i = 1; $i <= 10; $i++) {
            $dt       = sanitize_text_field($data["treatment_details_title_{$i}"] ?? '');
            $d1       = sanitize_textarea_field($data["treatment_details_treatment_1_content_{$i}"] ?? '');
            $d2       = sanitize_textarea_field($data["treatment_details_treatment_2_content_{$i}"] ?? '');
            if ($dt !== '') {
                $detail_rows[] = [
                    'title'               => $dt,
                    'treatment_1_content' => $d1,
                    'treatment_2_content' => $d2,
                ];
            }
        }
        update_field('treatment_details', $detail_rows, $post_id);

        // -- Hair solutions fields --
        update_field('hair_solutions_content', wp_kses_post($data['hair_text'] ?? ''), $post_id);
        update_field('hair_solutions_button', [
            'url'    => esc_url_raw($data['hair_btn_url'] ?? ''),
            'title'  => sanitize_text_field($data['hair_btn_title'] ?? ''),
            'target' => sanitize_text_field($data['hair_btn_target'] ?? ''),
        ], $post_id);
        update_field('hair_solutions_image', intval($data['hair_img'] ?? 0), $post_id);
        update_field('hair_solutions_image_mobile', intval($data['hair_img_mobile'] ?? 0), $post_id);

        // -- Summary --
        update_field('comparison_summary', wp_kses_post($data['comparison_summary'] ?? ''), $post_id);
    }

    fclose($h);
    echo '<div class="updated"><p>CSV imported successfully!</p></div>';
}


// Hide all item‐meta (including any short description) under the product name in the cart
add_filter( 'woocommerce_get_item_data', function( $item_data, $cart_item ) {
    if ( is_cart() ) {
        return []; // return an empty array = no “details” lines
    }
    return $item_data;
}, 10, 2 );


/**
 * WooCommerce “My Account” menu tweaks.
 * – Remove “Dashboard”
 * – Rename “Account details” to “My Profile”
 * – Place “My Profile” at the top
 */
add_filter( 'woocommerce_account_menu_items', 'acme_reorder_account_menu', 999 );

function acme_reorder_account_menu( $items ) {

	/* 1.  Remove the Dashboard entry */
	unset( $items['dashboard'] );              // 'dashboard'  => 'Dashboard'

	/* 2.  Rename “Account details” to “My Profile” */
	if ( isset( $items['edit-account'] ) ) {   // 'edit-account' => 'Account details'
		$items['edit-account'] = __( 'My Profile', 'woocommerce' );
	}

	/* 3.  Bring “My Profile” to the top by rebuilding the array */
	$sorted = array();

	// Put “My Profile” first if it exists
	if ( isset( $items['edit-account'] ) ) {
		$sorted['edit-account'] = $items['edit-account'];
		unset( $items['edit-account'] );
	}

	// Append the rest in their existing order
	foreach ( $items as $key => $label ) {
		$sorted[ $key ] = $label;
	}

	return $sorted;
}
/**
 * Make “My Profile” (edit‑account) the default My‑Account screen.
 *
 * If the visitor is on the My‑Account page *without* any endpoint
 * (/my-account/), we redirect them to the “edit-account” endpoint,
 * which we already renamed to “My Profile”.
 */
add_action( 'template_redirect', 'acme_account_default_to_profile' );

function acme_account_default_to_profile() {

	// Only run on the My‑Account page, when logged‑in, and no endpoint is present.
	if ( is_account_page() && is_user_logged_in() && ! is_wc_endpoint_url() ) {

		wp_safe_redirect( wc_get_endpoint_url( 'edit-account' ) );
		exit;
	}
}

// Skip the login page if shopper is already authenticated
add_action( 'template_redirect', function () {
    if ( is_page( 'login' ) && is_user_logged_in() ) {
        wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) ); // or any URL
        exit;
    }
} );

// Swap out default account URL for guests
add_filter( 'woocommerce_get_myaccount_page_url', function ( $url ) {
    if ( ! is_user_logged_in() ) {
        return home_url( '/login' );
    }
    return $url;
} );

add_filter( 'woocommerce_login_redirect', function ( $redirect, $user ) {
    // If login failed, WooCommerce sets $redirect = false
    return $redirect ?: home_url( '/login' );
}, 10, 2 );

/**
 * Send logged‑out visitors who hit /my-account to /login instead.
 */
add_action( 'template_redirect', function () {
if ( is_account_page() && ! is_user_logged_in() && ! is_wc_endpoint_url( 'lost-password' ) ) {
		wp_safe_redirect( home_url( '/login' ) );
		exit;                             // always exit after a redirect
	}
} );

/**
 * 1) Display the Profile Picture field on the Edit Account form.
 */
add_action( 'woocommerce_edit_account_form_start', 'add_profile_picture_field_to_edit_account' );
function add_profile_picture_field_to_edit_account() {
    $user_id   = get_current_user_id();
    $user      = wp_get_current_user();

    // Get avatar
    $avatar_id  = get_user_meta( $user_id, 'profile_picture_id', true );
    $avatar_url = $avatar_id
        ? wp_get_attachment_image_url( $avatar_id, 'thumbnail' )
        : get_avatar_url( $user_id, [ 'size' => 96 ] );

    // Build full name
    $first_name = get_user_meta( $user_id, 'first_name', true );
    $last_name  = get_user_meta( $user_id, 'last_name', true );
    if ( $first_name || $last_name ) {
        $full_name = trim( $first_name . ' ' . $last_name );
    } else {
        // fallback to display_name
        $full_name = $user->display_name;
    }
    ?>
<fieldset>
    <legend><?php esc_html_e( 'Profile Picture', 'your-textdomain' ); ?></legend>
    <div class="d-flex flex-wrap justify-content-between main-profile align-items-center">
        <?php if ( $avatar_url ) : ?>
        <div class="profile-image">
            <img src="<?php echo esc_url( $avatar_url ); ?>"
                alt="<?php esc_attr_e( 'Your avatar', 'your-textdomain' ); ?>" width="96" height="96"
                style="border-radius:50%;" />
            <div class="profile-name">
                <?php echo esc_html( $full_name ); ?>
            </div>
        </div>
        <?php endif; ?>

        <p class="form-row form-row-wide">
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" />
        </p>
    </div>
</fieldset>
<?php
}


/**
 * 2) Save the uploaded image when the user hits “Save changes.”
 */
add_action( 'woocommerce_save_account_details', 'save_profile_picture_on_edit_account', 12, 1 );
function save_profile_picture_on_edit_account( $user_id ) {
    if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    if ( ! empty( $_FILES['profile_picture']['name'] ) ) {

        // Upload
        $upload_overrides = [ 'test_form' => false ];
        $movefile = wp_handle_upload( $_FILES['profile_picture'], $upload_overrides );

        if ( ! isset( $movefile['error'] ) ) {
            // Create attachment
            $filetype = wp_check_filetype( $movefile['file'], null );
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title'     => sanitize_file_name( basename( $movefile['file'] ) ),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];
            $attach_id = wp_insert_attachment( $attachment, $movefile['file'] );
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );

            // Store in user meta (remove old if you like)
            update_user_meta( $user_id, 'profile_picture_id', $attach_id );
        } else {
            wc_add_notice( esc_html__( 'Profile picture upload error: ', 'your-textdomain' ) . esc_html( $movefile['error'] ), 'error' );
        }
    }
}


// Process our custom Forgot Password form and show a success notice
//add_action( 'woocommerce_before_lost_password_form', 'custom_process_forgot_password' );
function custom_process_forgot_password() {
    // Only on form POST
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
        return;
    }

    // WooCommerce's lost-password form uses this hidden field
    if ( empty( $_POST['wc_reset_password'] ) ) {
        return;
    }

    // Attempt to send the reset email via WooCommerce
    $success = WC_Shortcode_My_Account::retrieve_password();
    if ( $success ) {
        /* translators: %s: the username/email entered */
        wc_add_notice(
            sprintf(
                __( 'If an account exists for <strong>%s</strong>, you’ll receive a reset link shortly.', 'your-textdomain' ),
                esc_html( wp_unslash( $_POST['user_login'] ) )
            ),
            'success'
        );
    }
}

/* for the reset password email template from gpt */
// 1) Send reset link to /reset-password/?key=…&login=…
//add_filter( 'retrieve_password_message', 'custom_retrieve_password_message', 10, 4 );
function custom_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    // build our custom reset URL
    $reset_url = home_url( "/reset-password/?key={$key}&login=" . rawurlencode( $user_login ) );

    // you can customize these lines however you like
    $msg  = __( 'Someone requested a password reset for the following account:', 'woocommerce' ) . "\r\n\r\n";
    $msg .= sprintf( __( 'Username: %s', 'woocommerce' ), $user_login ) . "\r\n\r\n";
    $msg .= __( 'To reset your password, click the link below:', 'woocommerce' ) . "\r\n";
    $msg .= $reset_url . "\r\n\r\n";
    $msg .= __( 'If you did not request a password reset, please ignore this email.', 'woocommerce' );

    return $msg;
}
// – Filters the message body of the password reset mail :contentReference[oaicite:0]{index=0}

// 2) Catch any visits to wp-login.php?action=rp or action=resetpass and redirect them
add_action( 'login_form_rp',      'custom_redirect_rp_to_reset_page' );
add_action( 'woocommerce_before_lost_password_form','custom_redirect_rp_to_reset_page' );
function custom_redirect_rp_to_reset_page() {
    if ( isset( $_REQUEST['key'], $_REQUEST['login'] ) ) {
        $key    = sanitize_text_field( wp_unslash( $_REQUEST['key'] ) );
        $login  = rawurlencode( sanitize_user( wp_unslash( $_REQUEST['login'] ) ) );
        $target = home_url( "/reset-password/?key={$key}&login={$login}" );

        wp_safe_redirect( $target );
        exit;
    }
}
// when the forgot password link sent through the email it give message accordingly.
/**
 * Show a “link sent” message on the Forgot Password page
 */
//add_action( 'lostpassword_post', 'custom_lostpassword_success_notice', 10, 1 );
function custom_lostpassword_success_notice( $errors ) {
    // Only fire when there were no validation errors
    if ( ! is_wp_error( $errors ) ) {
        wc_add_notice(
            /* translators: %s: site name */
            sprintf( __( 'If an account exists for that email address, you’ll receive a reset link shortly at %s.', 'your-textdomain' ), '<em>' . esc_html( $_POST['user_login'] ) . '</em>' ),
            'success'
        );
    }
}

/**
 * For testing only: echo the reset‐password link on the Forgot Password page
 */
//add_action( 'woocommerce_before_lost_password_form', 'test_echo_reset_password_link' );
function test_echo_reset_password_link() {
    // Only after the Forgot Password form is submitted
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || empty( $_POST['user_login'] ) ) {
        return;
    }

    // Clean up the submitted username/email
    $user_login = sanitize_user( wp_unslash( $_POST['user_login'] ) );

    // Try to find the user by login or email
    $user = get_user_by( 'login', $user_login ) ?: get_user_by( 'email', $user_login );
    if ( ! $user ) {
        return;
    }

    // Generate a reset key
    $reset_key = get_password_reset_key( $user );
    if ( is_wp_error( $reset_key ) ) {
        return;
    }

    // Build your custom reset-password URL
    $reset_url = home_url( "/reset-password/?key={$reset_key}&login=" . rawurlencode( $user_login ) );

    // Echo it for testing (appears above the form)
    // echo '<div class="woocommerce-message">';
    // echo '<strong>Test Reset Link:</strong> ';
    // echo '<a href="' . esc_url( $reset_url ) . '">' . esc_html( $reset_url ) . '</a>';
    // echo '</div>';
}




// 2) Save uploaded files into Media Library, attached to this comment
add_action( 'comment_post', 'mytheme_save_review_media', 20, 2 );
function mytheme_save_review_media( $comment_id, $comment_approved ) {
    if ( empty( $_FILES['review_media'] ) || 'spam' === $comment_approved ) {
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $ids = [];
    foreach ( $_FILES['review_media']['name'] as $i => $name ) {
        if ( UPLOAD_ERR_OK !== $_FILES['review_media']['error'][ $i ] ) {
            continue;
        }
        $file = [
            'name'     => sanitize_file_name( $name ),
            'type'     => $_FILES['review_media']['type'][ $i ],
            'tmp_name' => $_FILES['review_media']['tmp_name'][ $i ],
            'error'    => $_FILES['review_media']['error'][ $i ],
            'size'     => $_FILES['review_media']['size'][ $i ],
        ];
        $attach = media_handle_sideload( $file, get_comment( $comment_id )->comment_post_ID );
        if ( ! is_wp_error( $attach ) ) {
            $ids[] = $attach;
        }
    }
    if ( $ids ) {
        update_comment_meta( $comment_id, 'review_attachments', $ids );
    }
}

// 3) Give our comment_form() an ID so we can hide/show it
add_filter( 'comment_form_defaults', 'mytheme_set_review_form_id' );
function mytheme_set_review_form_id( $defaults ) {
    if ( is_product() ) {
        $defaults['id_form'] = 'review_form_wrapper';
    }
    return $defaults;
}

// 4) Helper: build the reviews loop + pagination HTML
function mytheme_get_reviews_html( $product_id, $orderby = 'recent', $paged = 1 ) {
    $per_page = 5;
    switch ( $orderby ) {
        case 'high':
            $order_args = [ 'meta_key'=>'rating','orderby'=>'meta_value_num','order'=>'DESC' ];
            break;
        case 'low':
            $order_args = [ 'meta_key'=>'rating','orderby'=>'meta_value_num','order'=>'ASC' ];
            break;
        default:
            $order_args = [ 'orderby'=>'comment_date_gmt','order'=>'DESC' ];
    }

    $comments = get_comments( array_merge(
        [ 'post_id'=>$product_id, 'status'=>'approve', 'type'=>'review', 'number'=>$per_page, 'paged'=>$paged ],
        $order_args
    ) );

    ob_start();
    foreach ( $comments as $c ) {
        $rating = intval( get_comment_meta( $c->comment_ID, 'rating', true ) );
        $avatar = get_avatar_url( $c, [ 'size'=>48 ] );
        $date   = date_i18n( 'd/m/Y', strtotime( $c->comment_date ) );
        ?>
<div class="review-card">
    <?php if ( $c->comment_content ) : ?>
    <p><?php echo wp_kses_post( wpautop( $c->comment_content ) ); ?></p>
    <?php endif; ?>

    <?php
          $media = get_comment_meta( $c->comment_ID, 'review_attachments', true );
          if ( is_array( $media ) && $media ) : ?>
    <div class="review-media d-flex flex-wrap">
        <?php foreach ( $media as $aid ) :
                $url  = wp_get_attachment_url( $aid );
                $mime = get_post_mime_type( $aid );
                if ( 0 === strpos( $mime, 'video/' ) ) : ?>
        <video src="<?php echo esc_url( $url ); ?>" controls></video>
        <?php else : ?>
        <img src="<?php echo esc_url( $url ); ?>" alt="" />
        <?php endif;
              endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between user-info-wrapper">
        <div class="user-info d-flex">
            <img src="<?php echo esc_url( $avatar ); ?>" alt="" />
            <div>
                <strong><?php echo esc_html( $c->comment_author ); ?></strong>
                <div class="stars">
                    <?php echo str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ); ?>
                </div>
            </div>
        </div>
        <span class="date"><?php echo esc_html( $date ); ?></span>
    </div>
</div>
<?php
    }

    // pagination
    $total = get_comment_count( $product_id )['approved'];
    if ( $total > $per_page ) {
        $pages = ceil( $total / $per_page );
        echo '<div class="d-flex justify-content-center">';
        echo paginate_comments_links( [
            'prev_text'=>'&larr;', 'next_text'=>'&rarr;',
            'type'=>'list', 'add_args'=>false,
            'total'=>$pages, 'current'=>$paged,
        ] );
        echo '</div>';
    }

    return ob_get_clean();
}

// 5) AJAX handler for sorting/pagination
add_action( 'wp_ajax_mytheme_load_reviews',   'mytheme_ajax_load_reviews' );
add_action( 'wp_ajax_nopriv_mytheme_load_reviews', 'mytheme_ajax_load_reviews' );
function mytheme_ajax_load_reviews() {
    if ( empty( $_POST['product_id'] ) ) {
        wp_send_json_error( 'Missing product_id' );
    }
    $product_id = absint( $_POST['product_id'] );
    $orderby    = sanitize_text_field( $_POST['orderby'] ?? 'recent' );
    $paged      = max( 1, absint( $_POST['paged'] ?? 1 ) );
    $html       = mytheme_get_reviews_html( $product_id, $orderby, $paged );
    wp_send_json_success( [ 'html' => $html ] );
}

// 6) Enqueue our single JS file (AJAX sort + hide/show + enctype patch)
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_review_scripts' );
function mytheme_enqueue_review_scripts() {
    if ( is_product() ) {
        wp_enqueue_script(
            'mytheme-reviews',
            get_stylesheet_directory_uri() . '/js/review.js',
            [ 'jquery' ],
            '1.0',
            true
        );
        wp_localize_script( 'mytheme-reviews', 'MyTheme', [
            'ajax_url'   => admin_url( 'admin-ajax.php' ),
            'product_id' => get_the_ID(),
        ] );
    }
}

// 1. Before the quantity input: output the “minus” button
add_action( 'woocommerce_before_quantity_input_field', 'ct_add_qty_minus_button' );
function ct_add_qty_minus_button() {
    echo '<button type="button" class="qty-button minus" aria-label="Decrease quantity">&minus;</button>';
}

// 2. After the quantity input: output the “plus” button
add_action( 'woocommerce_after_quantity_input_field', 'ct_add_qty_plus_button' );
function ct_add_qty_plus_button() {
    echo '<button type="button" class="qty-button plus" aria-label="Increase quantity">&plus;</button>';
}

add_action( 'wp_enqueue_scripts', 'ct_qty_plus_minus_scripts' );
function ct_qty_plus_minus_scripts() {
   if ( is_product() || is_cart() ) {
        wp_enqueue_script(
            'ct-qty-controls',
            get_stylesheet_directory_uri() . '/js/qty-controls.js',
            array( 'jquery' ),
            '1.4',
            true
        );
    }
}


/**
 * Move the "Add a coupon" panel underneath the order-summary block—
 * only on desktop (viewport ≥992px), and only once.
 */
function move_coupon_after_summary_block() {
    if ( ! function_exists('is_checkout') || ! is_checkout() ) {
        return;
    }

    add_action( 'wp_footer', function() {
        ?>
<script>
(function($) {
    function moveCoupon() {
        // Only on desktop
        if ($(window).width() < 992) {
            return;
        }

        var $coupon = $('.wp-block-woocommerce-checkout-order-summary-coupon-form-block');
        var $summary = $('.wp-block-woocommerce-checkout-order-summary-block');

        // Only move if both exist AND coupon is NOT already right after summary
        if ($coupon.length && $summary.length &&
            !$summary.next().is($coupon)) {
            $coupon.insertAfter($summary);
        }
    }

    // Run initially when the page loads
    $(window).on('load', moveCoupon);

    // Also run when the window is resized (in case user rotates device or resizes)
    $(window).on('resize', moveCoupon);

    // Catch late DOM inserts/mutations
    var observer = new MutationObserver(moveCoupon);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})(jQuery);
</script>
<?php
    }, 100 );
}
add_action( 'wp_enqueue_scripts', 'move_coupon_after_summary_block', 20 );


/**
 * Append a "Pay Now" button at the end of the order summary content on block checkout.
 */
function add_custom_pay_button_to_order_summary() {
    if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
        return;
    }

    // Inject our script in the footer so the blocks have rendered
    add_action( 'wp_footer', function() {
        ?>
<script>
(function($) {
    function insertPayButton() {
        var $container = $('.wc-block-components-checkout-order-summary__content');
        // Only if the container exists and we haven't already added our button
        if ($container.length && !$('#custom-pay-button').length) {

            // Create the button
            var $btn = $('<button/>', {
                id: 'custom-pay-button',
                type: 'button',
                text: 'Pay Now',
                class: 'button alt wc-block-components-button wc-block-components-button--contained btn secondary-btn'
            }).css({
                'display': 'block',
                'width': '100%',
                'margin-top': '1em'
            }).on('click', function(e) {
                e.preventDefault();
                // Submit the main checkout form
                $('form.checkout').submit();
            });

            // Append it
            $container.append($btn);
        }
    }

    // Run on window load
    $(window).on('load', insertPayButton);

    // Also watch for async block renders
    var observer = new MutationObserver(insertPayButton);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})(jQuery);
</script>
<?php
    }, 100 );
}
//add_action( 'wp_enqueue_scripts', 'add_custom_pay_button_to_order_summary', 20 );

/**
 * Inject ACF FAQ into Yoast's schema under WebPage or CollectionPage mainEntity.
 * Handles both singular pages and the blog index (posts page).
 */
/**
 * Inject ACF FAQ into Yoast's schema under WebPage or CollectionPage mainEntity.
 * Handles regular pages, blog index (posts page), and WooCommerce shop page.
 */
function mytheme_add_faqpage_to_yoast_schema_graph( $graph ) {
    if ( is_admin() || ! function_exists( 'get_field' ) ) {
        return $graph;
    }

    // Determine the correct page ID
    $page_id = null;
    if ( is_home() ) {
        $page_id = get_option( 'page_for_posts' );
    } elseif ( function_exists( 'is_shop' ) && is_shop() ) {
        $page_id = get_option( 'woocommerce_shop_page_id' );
    } elseif ( is_singular() ) {
        $page_id = get_the_ID();
    }

    if ( ! $page_id ) {
        return $graph;
    }

    // Load ACF FAQ field
    $faqs = get_field( 'faq', $page_id );
    if ( empty( $faqs ) || ! is_array( $faqs ) ) {
        return $graph;
    }

    $faq_items = [];
    foreach ( $faqs as $faq ) {
        $question = isset( $faq['title'] ) ? trim( wp_strip_all_tags( $faq['title'] ) ) : '';
        $answer   = isset( $faq['content'] ) ? trim( wp_strip_all_tags( $faq['content'] ) ) : '';
        if ( $question && $answer ) {
            $faq_items[] = [
                '@type'          => 'Question',
                'name'           => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $answer,
                ],
            ];
        }
    }

    if ( empty( $faq_items ) ) {
        return $graph;
    }

    $injected = false;

    // Inject into existing WebPage or CollectionPage node
    foreach ( $graph as &$node ) {
        if ( isset( $node['@type'] ) && in_array( $node['@type'], [ 'WebPage', 'CollectionPage' ], true ) ) {
            $node['mainEntity'] = [
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_items,
            ];
            $injected = true;
            break;
        }
    }

    // If no suitable node found, create one
    if ( ! $injected ) {
        $graph[] = [
            '@type'      => 'WebPage',
            '@id'        => get_permalink( $page_id ) . '#webpage',
            'url'        => get_permalink( $page_id ),
            'mainEntity' => [
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_items,
            ],
        ];
    }

    return $graph;
}
add_filter( 'wpseo_schema_graph', 'mytheme_add_faqpage_to_yoast_schema_graph', 20 );

/*Treatment Comparison From GPT*/


// Enqueue script and localize AJAX URL (best in functions.php, shown here for completeness)
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_script( 'compare-treatments', get_stylesheet_directory_uri() . '/js/compare-treatments.js?v='.rand(), array('jquery'), null, true );
    wp_localize_script( 'compare-treatments', 'CompareAjax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'compare_treatments_nonce' ),
    ) );

    // Before/After Slider component
    wp_enqueue_script( 'before-after-slider', get_stylesheet_directory_uri() . '/js/before-after-slider.js', array(), null, true );
} );


// AJAX handler
add_action( 'wp_ajax_nopriv_compare_treatments', 'compare_treatments_ajax' );
add_action( 'wp_ajax_compare_treatments', 'compare_treatments_ajax' );
function compare_treatments_ajax() {
    check_ajax_referer( 'compare_treatments_nonce', 'nonce' );
    $ids = array_filter( array_map( 'intval', $_POST['ids'] ?? array() ) );
    if ( count( $ids ) < 2 ) {
        wp_send_json_error( 'Select at least two treatments.' );
    }
    // Define ACF fields
    $compare_fields = array(
        'introduction'                 => 'Introduction',
        'effectiveness'                => 'Effectiveness',
        'treatment_cost'               => 'Treatment Cost',
        'treatment_duration'           => 'Treatment Duration',
        'treatment_method'             => 'Treatment Method',
        'safety'                       => 'Safety',
        'ease_of_use'                  => 'Ease of Use',
        'accessibility'                => 'Accessibility',
        'sustainability_of_results'    => 'Sustainability of Results',
        'emotional_appeal'             => 'Emotional Appeal',
        'health_benefits_beyond_hair_loss' => 'Health Benefits Beyond Hair Loss',
        'environmental_impact'         => 'Environmental Impact',
        'product_button'             => 'Purchasing',

    );
    ob_start();
    ?>
<div class="treatment-center">
    <div class="treatment-compare pb-54">
        <div class="table-view">
            <div class="comparison-container">
                <div class="comparison-header">
                    <div class="column-title blank"></div>
                    <?php foreach ( $ids as $id ): ?>
                    <div class="column-title fill"><?php echo esc_html( get_the_title( $id ) ); ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="comparison-row">
                    <?php foreach ( $compare_fields as $field_key => $label ): ?>
                    <div class="comparison-row-wrap">
                        <div class="row-title"><?php echo esc_html( $label ); ?></div>
                        <?php foreach ( $ids as $id ): ?>
                        <div class="column-content">
                            <?php
                    $value = get_field( $field_key, $id );

                    // Special handling for product_button
                    if ( $field_key === 'product_button' && !empty($value['url']) ) {
                        // Assume ACF link field returns array with 'url', 'title', 'target'
                        $title = $value['title'] ?: 'Buy Now';
                        $target = $value['target'] ?: '_self';
                        echo '<a class="btn secondary-btn" href="' . esc_url($value['url']) . '" target="' . esc_attr($target) . '" class="product-button">' . esc_html($title) . '</a>';
                    } 
                    elseif ( is_array( $value ) ) {
                        echo esc_html( implode( ', ', $value ) );
                    } 
                    else {
                        echo wp_kses_post( $value );
                    }
                ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $html = ob_get_clean();
    wp_send_json_success( $html );
}



require_once get_template_directory() . '/extend/index.php'; 


add_filter('bcn_after_fill', 'change_products_to_shop_breadcrumb');

function change_products_to_shop_breadcrumb($trail) {
    foreach ($trail->breadcrumbs as $key => $breadcrumb) {
        // Look for the breadcrumb with title "Products"
        if ($breadcrumb->get_title() === 'Products') {
            $breadcrumb->set_title('Shop'); // Change label
            $breadcrumb->set_url(get_permalink(wc_get_page_id('shop'))); // Change URL to actual shop page
        }
    }
    return $trail;
}

//Direct add to cart button
function custom_add_to_cart_url( $product ) {
    // For simple subscriptions: add directly to cart → checkout
    if ( $product->is_type( 'subscription' ) ) {
        return esc_url( add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() ) );
    }

    // For variable subscriptions: add first variation to cart → checkout
    if ( $product->is_type( 'variable-subscription' ) ) {
        $variations = $product->get_available_variations();
        if ( ! empty( $variations ) ) {
            $first_variation = reset( $variations );
            $variation_id    = $first_variation['variation_id'];

            $url_args = array(
                'add-to-cart'  => $product->get_id(),
                'product_id'   => $product->get_id(),
                'variation_id' => $variation_id,
            );

            foreach ( $first_variation['attributes'] as $attribute => $value ) {
                if ( ! empty( $value ) ) {
                    $url_args[ $attribute ] = $value;
                }
            }

            return esc_url( add_query_arg( $url_args, wc_get_cart_url() ) );
        }
        // Fallback: no variations found, go to product page
        return get_permalink( $product->get_id() );
    }

    // For variable products: add first variation to cart
    if ( $product->is_type( 'variable' ) ) {
        $variations = $product->get_available_variations();
        if ( ! empty( $variations ) ) {
            $first_variation = reset( $variations );
            $variation_id = $first_variation['variation_id'];

            $url_args = array(
                'add-to-cart'   => $product->get_id(),
                'product_id'    => $product->get_id(),
                'variation_id'  => $variation_id,
            );

            foreach ( $first_variation['attributes'] as $attribute => $value ) {
                if ( ! empty( $value ) ) {
                    $url_args[ $attribute ] = $value;
                }
            }

            return esc_url( add_query_arg( $url_args, wc_get_cart_url() ) );
        }
    }

    // For simple products: direct add to cart
    return esc_url( add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() ) );
}


// Replace subscription labels everywhere (gettext)
add_filter( 'gettext', 'rename_subscription_labels', 20, 3 );
function rename_subscription_labels( $translated_text, $text, $domain ) {
    if ( stripos( $translated_text, 'Free Trial' ) !== false ) {
        return str_ireplace( 'Free Trial', 'Upfront', $translated_text );
    }

    if ( stripos( $translated_text, 'Sign-up fee' ) !== false ) {
        return str_ireplace( 'Sign-up fee', 'Upfront Charge', $translated_text );
    }

    return $translated_text;
}

// Additional fix: Change label for Sign-up Fee in cart totals
add_filter( 'woocommerce_cart_totals_fee_html', 'rename_fee_label_in_cart', 10, 2 );
add_filter( 'woocommerce_cart_totals_fee_label', 'rename_fee_label_in_cart_label', 10, 2 );

function rename_fee_label_in_cart( $fee_html, $fee ) {
    if ( stripos( $fee->name, 'Sign-up fee' ) !== false ) {
        $fee_html = str_ireplace( 'Sign-up fee', 'Upfront Charge', $fee_html );
    }
    return $fee_html;
}

function rename_fee_label_in_cart_label( $label, $fee ) {
    if ( stripos( $label, 'Sign-up fee' ) !== false ) {
        $label = str_ireplace( 'Sign-up fee', 'Upfront Charge', $label );
    }
    return $label;
}

add_action( 'wp_footer', 'custom_wc_blocks_text_override_script' );
function custom_wc_blocks_text_override_script() {
    // Only load on pages where WooCommerce blocks might appear
    if ( is_cart() || is_checkout() ) {
        ?>
<script type="text/javascript">
(function() {
    const interval = setInterval(() => {
        if (typeof wp !== 'undefined' && wp.i18n && wp.i18n.setLocaleData) {
            wp.i18n.setLocaleData({
                    'Sign up fee:': ['Upfront Charge:'],
                    'Sign-up fee:': ['Upfront Charge:'],
                    'Free trial:': ['Upfront:'],
                    'Free Trial:': ['Upfront:']
                },
                'woocommerce'
            );
            clearInterval(interval);
        }
    }, 100);
})();
</script>
<?php
    }
}

/**
 * After WooCommerce creates the customer, split the submitted full name
 * into first_name and last_name, then save it.
 */
add_action( 'woocommerce_created_customer', 'save_first_and_last_name', 10, 1 );
function save_first_and_last_name( $customer_id ) {
    // If first name wasn’t submitted, bail.
    if ( empty( $_POST['first_name'] ) ) {
        return;
    }

    // Sanitize inputs
    $first_name = sanitize_text_field( wp_strip_all_tags( $_POST['first_name'] ) );
    $last_name  = isset( $_POST['last_name'] )
        ? sanitize_text_field( wp_strip_all_tags( $_POST['last_name'] ) )
        : '';

    // Save into WP user meta
    update_user_meta( $customer_id, 'first_name', $first_name );
    update_user_meta( $customer_id, 'last_name',  $last_name );

    // Update display name
    wp_update_user( [
        'ID'           => $customer_id,
        'display_name' => trim( $first_name . ' ' . $last_name ),
    ] );
}

add_filter('show_admin_bar', function($show) {
    if (current_user_can('administrator')) {
        return true;
    }
    return false;
}, 1000);

add_action('init', function() {
    remove_post_type_support('product', 'editor');
});



add_action('admin_init', function() {
    if (isset($_GET['run_generate_token']) && current_user_can('manage_options')) {
        do_action('generate_zoho_access_token_event');
        echo 'Token generation manually triggered.';
        exit;
    }
});


add_filter('acf/fields/post_object/query', 'acf_post_object_search_title_only_for_treatment_guide', 10, 3);

function acf_post_object_search_title_only_for_treatment_guide( $args, $field, $post_id ) {
    // Change 'treatment_guide' to your actual field name or use $field['key'] for the field key
    if ( $field['name'] === 'treatment_guide' ) {
        // Only search in post_title
        if ( isset( $args['s'] ) ) {
            $args['search_columns'] = array( 'post_title' );
        }
    }
    return $args;
}

/*Analytics code for purchase product*/
add_action('wp_footer', 'push_woocommerce_purchase_to_datalayer');
function push_woocommerce_purchase_to_datalayer() {
    if (!is_order_received_page()) {
        return;
    }

    $order_id = absint(get_query_var('order-received'));
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    $currency = $order->get_currency();
    $total = $order->get_total();
    $tax = $order->get_total_tax();
    $shipping = $order->get_shipping_total();
    $transaction_id = $order->get_order_number();

    $items = [];
    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        if (!$product) continue;

        $items[] = [
            'item_id'   => $product->get_sku() ?: $product->get_id(),
            'item_name' => $product->get_name(),
            'quantity'  => $item->get_quantity(),
            'price'     => $product->get_price(),
        ];
    }

    ?>
<script>
window.dataLayer = window.dataLayer || [];
window.dataLayer.push({
    event: "purchase",
    ecommerce: {
        transaction_id: "<?php echo esc_js($transaction_id); ?>",
        value: <?php echo esc_js($total); ?>,
        currency: "<?php echo esc_js($currency); ?>",
        tax: <?php echo esc_js($tax); ?>,
        shipping: <?php echo esc_js($shipping); ?>,
        items: <?php echo wp_json_encode($items); ?>
    }
});
</script>
<?php
}

/*Quiz Popup Script*/
add_action('wp_footer', 'quiz_popup_script');
function quiz_popup_script() {
    ?>
<script>
$(document).ready(function() {

    $('a[href*="/hair-treatment-quiz"]').on('click', function(e) {
        e.preventDefault();

        $('#quizPopup').removeClass('hidden');

        console.log($(this).data('product-url'))
        $('#productLink').attr('href', $(this).data('product-url'));


        setTimeout(function() {
            $('#quizPopup').addClass('hidden');
        }, 15000)
    });


    $('#quizPopup .message img.close').on('click', function(e) {
        e.preventDefault();
        $('#quizPopup').addClass('hidden');
        // $('#productLink').attr('href', '');
    });

    $('#productLink').on('click', function(e) {
        e.preventDefault();
        $('.actualCartBtn').click();
        // $('#productLink').attr('href', '');
    });
});
</script>
<?php
}


/**
 * Change the checkout “Add to Cart” button text to “Place Order”.
 */
function change_checkout_place_order_text() {
    // Only on the WooCommerce checkout
    if ( ! function_exists('is_checkout') || ! is_checkout() ) {
        return;
    }

    // Register a dummy footer script that depends on jQuery
    wp_register_script( 'change-order-text-handler', false, [ 'jquery' ], null, true );
    wp_enqueue_script( 'change-order-text-handler' );

    // Inline our actual jQuery logic
    $js = <<<JS
(function($){
    function updateButtonText() {
        $('.wc-block-components-checkout-place-order-button__text').each(function(){
            if ( $(this).text().trim() === 'Add to Cart' ) {
                $(this).text('Place Order');
            }
        });
    }

    // Initial run on page load
    $(window).on('load', updateButtonText);

    // Also watch for any late React/AJAX mounts
    if ( window.MutationObserver ) {
        var observer = new MutationObserver(updateButtonText);
        observer.observe(document.body, { childList: true, subtree: true });
    }
})(jQuery);
JS;

    wp_add_inline_script( 'change-order-text-handler', $js );
}
add_action( 'wp_enqueue_scripts', 'change_checkout_place_order_text', 20 );

function register_comparison_cpt() {
    $labels = array(
        'name'               => 'Comparisons',
        'singular_name'      => 'Comparison',
        'menu_name'          => 'Comparisons',
        'name_admin_bar'     => 'Comparison',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Comparison',
        'new_item'           => 'New Comparison',
        'edit_item'          => 'Edit Comparison',
        'view_item'          => 'View Comparison',
        'all_items'          => 'All Comparisons',
        'search_items'       => 'Search Comparisons',
        'parent_item_colon'  => 'Parent Comparisons:',
        'not_found'          => 'No comparisons found.',
        'not_found_in_trash' => 'No comparisons found in Trash.'
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'comparison'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'thumbnail')
    );

    register_post_type('comparison', $args);
}
add_action('init', 'register_comparison_cpt');


function register_hair_treatments_post_type() {
    $labels = array(
        'name'               => _x( 'Hair Treatments', 'post type general name', 'textdomain' ),
        'singular_name'      => _x( 'Hair Treatment', 'post type singular name', 'textdomain' ),
        'menu_name'          => _x( 'Hair Treatments', 'admin menu', 'textdomain' ),
        'name_admin_bar'     => _x( 'Hair Treatment', 'add new on admin bar', 'textdomain' ),
        'add_new'            => _x( 'Add New', 'hair treatment', 'textdomain' ),
        'add_new_item'       => __( 'Add New Hair Treatment', 'textdomain' ),
        'new_item'           => __( 'New Hair Treatment', 'textdomain' ),
        'edit_item'          => __( 'Edit Hair Treatment', 'textdomain' ),
        'view_item'          => __( 'View Hair Treatment', 'textdomain' ),
        'all_items'          => __( 'All Hair Treatments', 'textdomain' ),
        'search_items'       => __( 'Search Hair Treatments', 'textdomain' ),
        'parent_item_colon'  => __( 'Parent Hair Treatments:', 'textdomain' ),
        'not_found'          => __( 'No hair treatments found.', 'textdomain' ),
        'not_found_in_trash' => __( 'No hair treatments found in Trash.', 'textdomain' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'hair-treatments' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-heart', // you can change icon
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
    );

    register_post_type( 'hair-treatments', $args );
}
add_action( 'init', 'register_hair_treatments_post_type' );

function register_review_post_type() {
    $args = array(
        'labels' => array(
            'name'               => 'Reviews',
            'singular_name'      => 'Review',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Review',
            'edit_item'          => 'Edit Review',
            'new_item'           => 'New Review',
            'view_item'          => 'View Review',
            'search_items'       => 'Search Reviews',
            'not_found'          => 'No reviews found',
            'not_found_in_trash' => 'No reviews found in Trash',
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => false,
        'show_in_rest'        => false,
        'menu_icon'           => 'dashicons-star-filled',
        'menu_position'       => 25,
        'supports'            => array( 'title' ),
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
        'capability_type'     => 'post',
    );
    register_post_type( 'review', $args );
}
add_action( 'init', 'register_review_post_type' );

if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'  => 'Theme Footer Settings',
        'menu_title'  => 'Theme Option',
        'menu_slug'   => 'theme-footer-settings',
        'capability'  => 'edit_posts',
        'redirect'    => false,
        'position'    => 61,
        'icon_url'    => 'dashicons-admin-generic',
    ));
}

// Skip cart — redirect straight to checkout after adding any product
add_filter( 'woocommerce_add_to_cart_redirect', function( $url ) {
    return wc_get_checkout_url();
}, 10, 1 );

// Rename 'Add to cart' to 'Buy Now' on shop/archive; keep default on single product page
add_filter( 'woocommerce_product_add_to_cart_text', function( $text ) {
    return is_product() ? $text : __( 'Buy Now', 'woocommerce' );
} );

// Update cart icon count via WooCommerce AJAX fragments
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
    $count = WC()->cart->get_cart_contents_count();
    $fragments['span.hairy-cart-count'] = '<span class="cart-count hairy-cart-count">' . $count . '</span>';
    return $fragments;
} );

// Disable star-rating-wrap and cust-reviews sections
add_action( 'wp_head', function() {
    echo '<style>.star-rating-wrap, .cust-reviews { display: none !important; }</style>';
} );


// Footer newsletter subscription handler
function hairy_footer_subscribe() {
    check_ajax_referer( 'footer_subscribe_nonce', 'nonce' );

    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Please enter a valid email address.' ) );
    }

    $subscribers = get_option( 'hairy_hair_subscribers', array() );

    if ( in_array( $email, $subscribers, true ) ) {
        wp_send_json_error( array( 'message' => 'This email is already subscribed.' ) );
    }

    $subscribers[] = $email;
    update_option( 'hairy_hair_subscribers', $subscribers );

    wp_mail(
        get_option( 'admin_email' ),
        'New Newsletter Subscriber — Hairy Hair',
        "A new subscriber has signed up:\n\n" . $email
    );

    wp_send_json_success( array( 'message' => 'Thank you for subscribing!' ) );
}
add_action( 'wp_ajax_footer_subscribe', 'hairy_footer_subscribe' );
add_action( 'wp_ajax_nopriv_footer_subscribe', 'hairy_footer_subscribe' );