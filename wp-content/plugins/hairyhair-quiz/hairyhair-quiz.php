<?php
/**
 * Plugin Name: Hairy Hair Quiz
 * Description: Quiz plugin with WooCommerce matching, contact form, and submissions CPT.
 * Version: 1.0.0
 * Author: Kimheang
 */

if (!defined('ABSPATH')) exit;

// --------------------
// Activation Hook
// --------------------
register_activation_hook(__FILE__, 'hairy_hair_quiz_activate');
function hairy_hair_quiz_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . "hairy_hair_quiz_table";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        full_name varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        dob date NOT NULL,
        email varchar(255) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// --------------------
// Deactivation Hook
// --------------------
register_deactivation_hook(__FILE__, 'hairy_hair_quiz_deactivate');
function hairy_hair_quiz_deactivate() {
    delete_option('hairy_hair_quiz_option');
    flush_rewrite_rules();
}

// --------------------
// Admin Page
// --------------------
function hairy_hair_quiz_admin_page() {
    echo '<div class="wrap">';
    echo '<h1>Welcome to Hairy Hair Quiz Plugin</h1>';
    echo '<p>Customize questions and answers for the Hairy Hair Quiz.</p>';
    echo '<button id="hairy-quiz-btn">Click Me</button>';
    echo '</div>';
}

add_action('admin_menu', function() {
    add_menu_page(
        'Hairy Hair Quiz',
        'Hairy Hair Quiz',
        'manage_options',
        'hairy-hair-quiz',
        'hairy_hair_quiz_admin_page',
        'dashicons-welcome-learn-more',
        20
    );
});

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('hairy-hair-quiz-admin', plugin_dir_url(__FILE__) . 'assets/quiz-style.css');
    
    wp_enqueue_script(
        'hairy-quiz-frontend',
        plugin_dir_url(__FILE__) . 'assets/quiz-frontend.js',
        ['jquery'],
        '1.0',
        true
    );

    wp_localize_script('hairy-quiz-frontend', 'hairyQuizAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('hairy_quiz_nonce')
    ]);
});

// --------------------
// Register Submissions CPT
// --------------------
add_action('init', function() {
    $labels = [
        'name' => 'Submissions',
        'singular_name' => 'Submission',
        'menu_name' => 'Submissions',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Submission',
        'edit_item' => 'Edit Submission',
        'new_item' => 'New Submission',
        'view_item' => 'View Submission',
        'all_items' => 'All Submissions',
        'search_items' => 'Search Submissions',
        'not_found' => 'No submissions found',
        'not_found_in_trash' => 'No submissions found in Trash'
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'hairy-hair-quiz',
        'supports' => ['title', 'editor', 'custom-fields'],
        'show_in_rest' => true
    ];

    register_post_type('submission', $args);
});

add_action('acf/init', function() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title'    => 'Hairy Hair Quiz Settings',
            'menu_title'    => 'Quiz Settings',
            'menu_slug'     => 'hairy-hair-quiz-settings',
            'capability'    => 'manage_options',
            'parent_slug'   => 'hairy-hair-quiz', // attaches to your plugin's main menu
            'redirect'      => false
        ]);
    }
});


add_action('acf/init', function() {
    if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_68e0ebae905af',
        'title' => 'Hairy Hair Quiz',
        'fields' => array(
            array(
                'key' => 'field_68e0ebb2524ce',
                'label' => 'Quiz repeater',
                'name' => 'quiz_repeater',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => '',
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => '',
                'sub_fields' => array(
                    array(
                        'key' => 'field_68e0ebc2524cf',
                        'label' => 'Attribute',
                        'name' => 'attribute',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_68e0ebce524d0',
                        'label' => 'Question',
                        'name' => 'question',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    array(
                        'key' => 'field_68e0ec03524d4',
                        'label' => 'Answer Type',
                        'name' => 'answer_type',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'radio' => 'radio',
                            'checkbox' => 'checkbox',
                        ),
                        'default_value' => false,
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_68e0ebd4524d1',
                        'label' => 'Answers',
                        'name' => 'answers',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => '',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'table',
                        'button_label' => '',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_68e0ebe3524d21',
                                'label' => 'Value',
                                'name' => 'value',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_68e0ebe3524d2',
                                'label' => 'Answer',
                                'name' => 'answer',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            array(
                                'key' => 'field_68e0ebf3524d3',
                                'label' => 'Disclaimer',
                                'name' => 'disclaimer',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'hairy-hair-quiz-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
    endif;
});

// Shortcode function just loads the template file
function hairy_quiz_step_form() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'includes/hairy-quiz-form.php'; 
    return ob_get_clean();
}

// Register shortcode
add_shortcode('hairy_quiz_submission', 'hairy_quiz_step_form');

// AJAX HANDLER: Save Quiz Submission
add_action('wp_ajax_hairy_quiz_submit', 'hairy_quiz_submit');
add_action('wp_ajax_nopriv_hairy_quiz_submit', 'hairy_quiz_submit');

function hairy_quiz_submit() {
    check_ajax_referer('hairy_quiz_nonce', 'security');

    // 1️⃣ Sanitize contact info
    $full_name = sanitize_text_field($_POST['full_name'] ?? '');
    $phone     = sanitize_text_field($_POST['phone'] ?? '');
    $dob       = sanitize_text_field($_POST['dob'] ?? '');
    $email     = sanitize_email($_POST['email'] ?? '');

    // 2️⃣ Decode answers JSON
    $answers_json = isset($_POST['answers']) ? wp_unslash($_POST['answers']) : '{}';
    $answers = json_decode($answers_json, true);
    if (!is_array($answers)) $answers = [];

    // 3️⃣ Get quiz questions from ACF
    $quiz_repeater = get_field('quiz_repeater', 'option') ?: [];

    // 4️⃣ Create submission post
    $post_id = wp_insert_post([
        'post_type'    => 'submission',
        'post_title'   => $full_name ?: 'Anonymous Submission',
        'post_status'  => 'publish',
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['message' => 'Error saving submission.']);
    }

    // 5️⃣ Save contact info
    $user_id = get_current_user_id();
    if ($user_id) update_post_meta($post_id, 'user_id', $user_id);
    update_post_meta($post_id, 'full_name', $full_name);
    update_post_meta($post_id, 'phone', $phone);
    update_post_meta($post_id, 'dob', $dob);
    update_post_meta($post_id, 'email', $email);

    // 6️⃣ Save answers
    $content = "<strong>Full Name:</strong> $full_name<br>";
    $content .= "<strong>Phone:</strong> $phone<br>";
    $content .= "<strong>Date of Birth:</strong> $dob<br>";
    $content .= "<strong>Email:</strong> $email<br><br><strong>Answers:</strong><br>";

    $answers_raw = [];

    foreach ($quiz_repeater as $i => $q) {
        $question_text = sanitize_text_field($q['question']);
        $attribute     = sanitize_title($q['attribute']);
        $nested_answers = $q['answers'] ?? [];

        $js_key = 'question_' . $i;
        $selected_values = isset($answers[$js_key]) ? (array)$answers[$js_key] : [];

        $answer_details = [];
        foreach ($selected_values as $val) {
            foreach ($nested_answers as $na) {
                if ($na['value'] === $val) {
                    $answer_details[] = [
                        'attribute'  => $attribute,
                        'answer'     => sanitize_text_field($na['answer']),
                        'value'      => sanitize_text_field($na['value']),
                        'disclaimer' => sanitize_text_field($na['disclaimer'] ?? '')
                    ];
                    break;
                }
            }
        }

        $answers_raw[$question_text] = $answer_details;
        update_post_meta($post_id, $question_text, $answer_details);

        if (!empty($answer_details)) {
            $text = array_map(fn($a) => "{$a['answer']} ({$a['attribute']}: {$a['value']})", $answer_details);
            $content .= "<em>$question_text:</em> " . implode(', ', $text) . "<br>";
        } else {
            $content .= "<em>$question_text:</em> No answer<br>";
        }
    }

    update_post_meta($post_id, 'answers_raw', $answers_raw);
    wp_update_post(['ID' => $post_id, 'post_content' => $content]);

    // 7️⃣ Handle file upload safely
    if (!empty($_FILES['quiz_file']['name'])) {
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attachment_id = media_handle_upload('quiz_file', $post_id);

    if (is_wp_error($attachment_id)) {
        error_log('Quiz file upload failed: ' . $attachment_id->get_error_message());
    } else {
        // Force attach to submission post
        wp_update_post([
            'ID' => $attachment_id,
            'post_parent' => $post_id
        ]);

        // Optional: set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        // Save URL & ID
        update_post_meta($post_id, 'uploaded_file', wp_get_attachment_url($attachment_id));
        update_post_meta($post_id, 'uploaded_file_id', $attachment_id);
    }
}

    // 8️⃣ Redirect to results page
    $results_page = site_url('/my-account/quiz/');
    wp_send_json_success([
        'message'  => 'Submission saved successfully!',
        'post_id'  => $post_id,
        'redirect' => $results_page,
    ]);
}


function quiz_results_shortcode($atts) {
    // Button URL
    if (is_user_logged_in()) {
        $quiz_url = site_url('/the-hair-treatment-quiz/');
    } else {
        $quiz_url = site_url('/login'); // Redirects to login page
    }

    // Take a Quiz button
    echo '<div class="take-quiz-btn" style="margin-bottom:20px; text-align:center;">';
    echo '<a href="' . esc_url($quiz_url) . '" class="btn dark-btn"><span>Take a Quiz</span></a>';
    echo '</div>';

    // If user not logged in, show last 3 products
if (!is_user_logged_in()) {
    $recent_products = wc_get_products([
        'limit' => 3,
        'status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    ]);

    if ($recent_products) {
        echo '<div class="recent-products"><h2>Latest Products</h2><div class="row">';
        foreach ($recent_products as $product) {
            $product_id = $product->get_id(); // ✅ define product ID correctly
            echo '<div class="col-lg-4 col-md-6 col-12">';
            echo '<div class="product-slide">';
            echo '<div class="product-image"><a href="' . esc_url(get_permalink($product_id)) . '">';
            echo $product->get_image('medium');

            // Product badge
            $terms = get_the_terms($product_id, 'product_cat');
            if (!empty($terms) && !is_wp_error($terms)) {
                $first_term = $terms[0]->name;
                $first_word = strtok($first_term, ' ');
                echo '<div class="product-badge badge-green-bg"><span>' . esc_html($first_word) . '</span></div>';
            }

            echo '</a></div>'; // close product-image
            echo '<div class="product-detail">';
            echo '<a href="' . esc_url(get_permalink($product_id)) . '"><h5>' . esc_html($product->get_name()) . '</h5></a>';
            echo '<p>' . esc_html(wp_trim_words($product->get_short_description(), 12, '...')) . '</p>';
            echo '<div class="bottom-detail">';
            echo '<h6>From <span class="woocommerce-Price-amount amount">' . wp_kses_post($product->get_price_html()) . '</span></h6>';
            echo '<a href="' . esc_url(get_permalink($product_id)) . '" class="btn dark-btn"><span>Add to Cart</span></a>';
            echo '</div>'; // close bottom-detail
            echo '</div>'; // close product-detail
            echo '</div>'; // close product-slide
            echo '</div>'; // close col
        }
        echo '</div></div>'; // close row and recent-products
    }

    return ob_get_clean();
}

    $user_id = get_current_user_id();

    // Get latest submission by this user
    $submission = get_posts([
        'post_type'      => 'submission',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_key'       => 'user_id',
        'meta_value'     => $user_id,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    if (!$submission) {
        return '<p>No quiz submission found.</p>';
    }

    $submission_id = $submission[0]->ID;

    $dob = get_post_meta($submission_id, 'dob', true);

    $age_warning = '';
    if (!empty($dob)) {
        $dob_datetime = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dob_datetime)->y;

        if ($age > 60) {
            $age_warning = '<div class="age-warning" style="color:red; font-weight:bold; margin-bottom:15px;">
                ⚠️ Your age is over 60. Please consult with your doctor before using any products.
            </div>';
        }
    }
    $answers_raw = get_post_meta($submission_id, 'answers_raw', true);

    if (empty($answers_raw) || !is_array($answers_raw)) {
        return '<p>No answers recorded.</p>';
    }

    $products = wc_get_products([
        'limit'  => -1,
        'status' => 'publish',
    ]);

    $product_scores = [];

    foreach ($products as $product) {
        $score = 0;
        $matched_disclaimers = [];

        foreach ($answers_raw as $question => $answer_set) {
            foreach ($answer_set as $ans) {
                $attr_name  = isset($ans['attribute']) ? sanitize_title($ans['attribute']) : '';
                $attr_value = isset($ans['value']) ? sanitize_text_field($ans['value']) : '';
                $disclaimer = isset($ans['disclaimer']) ? sanitize_text_field($ans['disclaimer']) : '';

                if (!$attr_name || !$attr_value) {
                    continue;
                }

                // WooCommerce product attributes are usually "pa_" prefixed
                $taxonomy = 'pa_' . $attr_name;
                $terms = wp_get_post_terms($product->get_id(), $taxonomy, ['fields' => 'names']);

                // Normalize values for comparison
                $normalize = fn($val) => strtolower(trim($val));
                $product_values = array_map($normalize, $terms);

                if (in_array($normalize($attr_value), $product_values, true)) {
                    $score++;
                    if ($disclaimer) {
                        $matched_disclaimers[] = $disclaimer;
                    }
                }
            }
        }

        if ($score > 0) {
            $product_scores[$product->get_id()] = [
                'score'       => $score,
                'disclaimers' => array_unique($matched_disclaimers),
            ];
        }
    }

    if (empty($product_scores)) {
        return '<p>No matching products found based on your quiz answers.</p>';
    }

    // Sort products by score (descending)
    uasort($product_scores, function ($a, $b) {
        return $b['score'] - $a['score'];
    });

    // Get top 3 products
    $top_products = array_slice($product_scores, 0, 3, true);

    // Output recommendations
    ob_start();
    echo $age_warning; // Display warning if age > 60
    ?>
<div class="quiz-recommendations">
    <h2>Recommended Products for You</h2>
    <div class="row">
        <?php foreach ($top_products as $product_id => $data): 
                $product = wc_get_product($product_id);
                if (!$product) continue; ?>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="product-slide" data-score="<?php echo esc_html($data['score']); ?>">
                <div class="product-image">
                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                        <?php echo $product->get_image('medium'); ?>
                        <?php $terms = get_the_terms($product_id, 'product_cat');
                                if (!empty($terms) && !is_wp_error($terms)) {
                                    $first_term = $terms[0]->name;
                                    $first_word = strtok($first_term, ' '); // Get first word only
                                    echo '<div class="product-badge badge-green-bg"> 
                                    <span>' . esc_html($first_word) . '</span></div>';
                                }?>
                    </a>
                </div>

                <div class="product-detail">
                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
                        <h5><?php echo esc_html($product->get_name()); ?></h5>
                    </a>
                    <p><?php echo esc_html(wp_trim_words($product->get_short_description(), 12, '...')); ?></p>

                    <div class="bottom-detail">
                        <h6>From <span
                                class="woocommerce-Price-amount amount"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                        </h6>
                        <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="btn dark-btn"
                            aria-label="<?php echo esc_attr($product->get_name()); ?>">
                            <span>Add to Cart</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
        <?php foreach ($top_products as $product_id => $data): 
                $product = wc_get_product($product_id);
                if (!$product) continue; ?>
        <?php if (!empty($data['disclaimers'])): ?>
        <div class="product-disclaimers">
            <ul>
                <?php foreach ($data['disclaimers'] as $d): ?>
                <li>
                    <p><?php echo esc_html($d); ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>
<?php

    return ob_get_clean();
}
add_shortcode('quiz_results', 'quiz_results_shortcode');