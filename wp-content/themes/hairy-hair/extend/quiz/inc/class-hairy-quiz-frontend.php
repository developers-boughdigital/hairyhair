<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class Hairy_Quiz_Frontend {

    /**
     * Allowed values for the lead-form Step 2 / Step 3 picklists (2026-11-12 implementation,
     * values checked against the Zoho CRM Lead layout). Sent to Zoho unchanged, so they must match
     * the Zoho picklist values exactly (capitalisation and spacing).
     *
     * @param string $field Form field name.
     * @return string[]
     */
    public static function lead_picklist_options( $field ) {
        $options = array(
            'age_group'                  => array( 'Under 25', '25 - 34', '35 - 44', '45 - 54', '55+' ),
            'current_hair_loss'          => array( 'Mild thinning', 'Noticeable Patches', 'Significant loss', 'Nearly bald' ),
            'allergies_or_sensitivities' => array( 'Yes', 'No' ),
            'taking_any_medications'     => array( 'Yes', 'No' ),
            'high_blood_pressure'        => array( 'Yes', 'No', 'Unsure' ),
        );
        return isset( $options[ $field ] ) ? $options[ $field ] : array();
    }

    /**
     * Max characters for a Step 3 explanation.
     */
    const EXPLANATION_MAX_CHARS = 2000;

    /**
     * Cleans a free-text medical explanation without losing content.
     *
     * Kept as plain text (no tag stripping, so entries like "<5mg daily" survive); it is always
     * escaped on output. Line breaks are normalised and control characters removed.
     *
     * @param mixed $raw Submitted value.
     * @return string|WP_Error Clean text ('' if empty) or an error for invalid input.
     */
    public static function clean_explanation( $raw ) {
        if ( ! is_string( $raw ) ) {
            return new WP_Error( 'invalid', __( 'Please enter your explanation as text.', 'hairy-hair' ) );
        }
        $text = wp_unslash( $raw );
        if ( '' !== $text && '' === wp_check_invalid_utf8( $text ) ) {
            return new WP_Error( 'invalid', __( 'Your explanation contains characters we cannot accept. Please retype it.', 'hairy-hair' ) );
        }
        $text = str_replace( array( "\r\n", "\r" ), "\n", $text );
        $text = preg_replace( '/[^\P{C}\n\t]/u', '', $text ); // drop control/invisible format chars, keep newlines and tabs
        $text = trim( (string) $text );
        if ( mb_strlen( $text ) > self::EXPLANATION_MAX_CHARS ) {
            return new WP_Error( 'too_long', sprintf( __( 'Please keep each explanation under %d characters.', 'hairy-hair' ), self::EXPLANATION_MAX_CHARS ) );
        }
        return $text;
    }

    protected $core_manager;

    protected $wpdb;


    public function __construct( Hairy_Quiz_Manager_Core $core_manager ) {
        $this->core_manager = $core_manager;
        $this->wpdb         = $core_manager->get_wpdb();

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
        add_shortcode( 'hairy_quiz', array( $this, 'render_quiz_shortcode' ) );

        add_action( 'wp_ajax_hairy_submit_quiz', array( $this, 'handle_quiz_submission' ) );
        add_action( 'wp_ajax_nopriv_hairy_submit_quiz', array( $this, 'handle_quiz_submission' ) ); 

        add_action( 'wp_ajax_hairy_submit_lead', array( $this, 'handle_lead_submission' ) );
        add_action( 'wp_ajax_nopriv_hairy_submit_lead', array( $this, 'handle_lead_submission' ) );
    }


    public function enqueue_frontend_scripts() {
        wp_enqueue_style( 'hairy-frontend-css', HAIRY_URL . 'css/hairy-frontend.css', array(), '1.0.4' );
        wp_enqueue_script( 'hairy-frontend-js', HAIRY_URL . 'js/hairy-frontend.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.4', true );
        wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' ); // For datepicker

        wp_localize_script( 'hairy-frontend-js', 'hairyQuiz', array(
            'ajax_url'                => admin_url( 'admin-ajax.php' ),
            'nonce'                   => wp_create_nonce( 'hairy_quiz_frontend_nonce' ),
            'quiz_submitted_message'  => __( 'Quiz submitted successfully!', 'hairy-hair' ),
            'quiz_error_message'      => __( 'An error occurred during submission. Please try again.', 'hairy-hair' ),
            'required_fields_message' => __( 'Please fill in all required fields.', 'hairy-hair' ),
        ) );
    }

    public function render_quiz_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'quiz_type' => '',
        ), $atts, 'hairy_quiz' );

        $quiz_type = sanitize_text_field( $atts['quiz_type'] );

        if ( empty( $quiz_type ) ) {
            return '<p>' . esc_html__( 'Please specify a quiz type in the shortcode, e.g., [hairy_quiz quiz_type="My Personality Quiz"]', 'hairy-hair' ) . '</p>';
        }

        $questions = $this->wpdb->get_results( $this->wpdb->prepare(
            "SELECT * FROM {$this->core_manager->get_table_questions()} WHERE quiz_type = %s ORDER BY question_sequence ASC, ID ASC",
            $quiz_type
        ) );

        if ( ! $questions ) {
            return '<p>' . sprintf( esc_html__( 'No questions found for quiz type: "%s".', 'hairy-hair' ), esc_html( $quiz_type ) ) . '</p>';
        }

        ob_start(); 

        ?>
        <section class="personal-info-wrap hair-quiz-wrap">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                        <div class="sign-form-wrap">
                            
                            <div class="step-top-action">
                                <div class="progress" style="height: 6px; width: 480px; margin: 0 auto;">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="step-container d-flex justify-content-between">
                                    <?php  for($i = 1; $i <= count($questions)+1; $i++) { ?>
                                        <div 
                                            class="step-circle step-circle-<?php echo $i; ?>"
                                                <?php 
                                                    if($i == 1) echo "first_question"; 
                                                    elseif($i == count($questions)) echo "last_question"; 
                                                ?> 
                                                onclick="displaySteps(<?php echo $i; ?>)">
                                            <span><?php echo $i; ?></span> 
                                        </div> 
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="sign-up-content-form" id="multi-step-form">
                                <form id="hairy-frontend-quiz-form" data-quiz-type="<?php echo esc_attr( $quiz_type ); ?>" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="hairy_submit_quiz">
                                <input type="hidden" name="hh_submission_token" value="">
                                <?php wp_nonce_field( 'hairy_quiz_frontend_nonce', 'hairy_quiz_frontend_nonce' ); ?> 

                                <?php $count = 1; ?>
                                <?php foreach ( $questions as $question ) : ?>
                                <div class="hairy-quiz-question <?php echo ($question->answer_type == 'multi') ? 'hairy-quiz-question-checkbox' : '' ?> step step-<?php echo $count; ?>" data-count_id="<?php echo $count; ?>" data-question_id="<?php echo $question->ID; ?>" <?php echo ($count>1) && $count>1 ? 'style="display: none;"' : ''; ?> >

                                    <div class="question-block">
                                        <h2><?php echo esc_html( $question->question ); ?></h2>
                                        <div class="options">
                                            <?php
                                            $answers = $this->wpdb->get_results( $this->wpdb->prepare(
                                                "SELECT ID, answer_text, answer_label FROM {$this->core_manager->get_table_answers()} WHERE question_id = %d ORDER BY answer_label ASC",
                                                $question->ID
                                            ) );

                                            if ( $answers ) {
                                                foreach ( $answers as $answer ) {
                                                    $display_label = $answer->answer_label ? esc_html($answer->answer_label) : $answer->answer_text;
                                                    ?>
                                                   
                                                    <label class="option-card">
                                                        <input type="<?php echo ($question->answer_type == 'multi') ? 'checkbox' : 'radio' ?>" name="question_<?php echo esc_attr( $question->ID ); ?><?php echo ($question->answer_type == 'multi') ? '[]' : '' ?>" value="<?php echo esc_attr( $answer->ID ); ?>">
                                                        <span class="answer-text"><?php echo esc_html( $answer->answer_text ); ?></span>
                                                    </label>
                                                    <?php
                                                }
                                                if($question->ID == 14) {
                                                ?>
                                                <input type="file" name="file" class="form-control question-file-input" accept="image/*" style="display: none;" id="file-input">
                                                <input type="text" name="input_file" id="file-url"  style="display: none;">
                                                <div id="upload-status"></div>
                                                <?php
                                                }
                                            } 
                                            ?>
                                            
                                            
                                        </div>
                                    </div>
                                    <div class="next-btn-form">

                                        <?php if ($count > 1): ?>
                                            <button type="button" class="btn dark-btn prev-step-btn">Previous</button>
                                        <?php endif; ?>
 
                                        <button type="button" class="btn secondary-btn next-step-btn">Next</button> 
                                        
                                    </div>
                                </div>
                                <?php $count++; endforeach; ?> 
                                <div class="hairy-quiz-question step step-<?php echo $count; ?>" data-count_id="<?php echo $count; ?>" data-question_id="<?php echo $question->ID; ?>" <?php echo ($count>1) && $count>1 ? 'style="display: none;"' : ''; ?> >

                                    <div class="question-block sign-process">
                                        <h2><?php echo esc_html__( 'Your Personal Information', 'hairy-hair' ); ?></h2> 
                                        <div class="personal-info-fields"> 
                                            <?php
                                            $user_id    = get_current_user_id();
                                            ?>
                                            
                                            <div class="row">
                                                
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="first_name" class="form-label"><?php echo esc_html__('First Name', 'hairy-hair'); ?></label>
                                                        <input type="text" name="first_name" class="form-control" id="first_name" required placeholder="John" value="<?php echo gm_get_user_meta($user_id, 'first_name', true); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="last_name" class="form-label"><?php echo esc_html__('Last Name', 'hairy-hair'); ?></label>
                                                        <input type="text" name="last_name" class="form-control" id="last_name" required placeholder="Doe" value="<?php echo gm_get_user_meta($user_id, 'last_name', true); ?>">
                                                    </div>
                                                </div>

                                                <?php if(!$user_id) : ?>
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="email" class="form-label"><?php echo esc_html__('Email', 'hairy-hair'); ?></label>
                                                        <input type="email" name="email" class="form-control" id="email" required placeholder="dev@gmail.com" value="<?php echo gm_get_user_meta($user_id, 'email', true); ?>">
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                                
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="phone" class="form-label"><?php echo esc_html__('Phone', 'hairy-hair'); ?></label>
                                                        <input type="tel" name="phone" class="form-control" id="phone" required placeholder="0412345678" maxlength="10" inputmode="numeric" value="<?php echo gm_get_user_meta($user_id, 'phone', true); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="dob" class="form-label"><?php echo esc_html__('Date of Birth', 'hairy-hair'); ?> <span class="text-danger">*</span></label>
                                                        <input type="text" name="dob" class="form-control required hairy-datepicker" id="dob" required placeholder="DD-MM-YYYY" value="<?php echo gm_get_user_meta($user_id, 'dob', true); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-12">
                                                    <div class="mb-3 field-list-all">
                                                        <label for="gender" class="form-label"><?php echo esc_html__('Gender', 'hairy-hair'); ?></label> 
                                                        <select name="gender" class="form-control" id="gender" required>
                                                            <option value="male" <?php if(gm_get_user_meta($user_id, 'gender', true) == 'male') echo 'selected'; ?>><?php echo esc_html__('Male', 'hairy-hair'); ?></option>
                                                            <option value="female" <?php if(gm_get_user_meta($user_id, 'gender', true) == 'female') echo 'selected'; ?>><?php echo esc_html__('Female', 'hairy-hair'); ?></option>
                                                        </select>

                                                    </div>
                                                </div>
                                                
                                            </div>

                                        </div> 

                                    </div>
                                    <div class="next-btn-form">
                                        <button type="button" class="btn dark-btn prev-step-btn">Previous</button>
                                        <button type="submit" id="hairy-submit-quiz-button" class="btn secondary-btn">Submit</button>                                        
                                    </div>
                                </div>

                                <div id="hairy-quiz-message" class="hairy-quiz-message formErrorMessage" style="display:none;"></div> 

                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php

        return ob_get_clean();
    }

    public function handle_quiz_submission() {

        check_ajax_referer( 'hairy_quiz_frontend_nonce', 'hairy_quiz_frontend_nonce' );
 

        $user_id    = get_current_user_id(); 
        
        if($user_id) {
            $current_user = wp_get_current_user();
            $email      = $current_user->user_email;
        } else {
            $email      = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
        }
        
        $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $dob        = isset( $_POST['dob'] ) ? sanitize_text_field( $_POST['dob'] ) : ''; 
        $gender     = isset( $_POST['gender'] ) ? sanitize_text_field( $_POST['gender'] ) : '';
        $quiz_type  = isset( $_POST['quiz_type'] ) ? sanitize_text_field( $_POST['quiz_type'] ) : '';


        if ( empty( $first_name ) || empty( $last_name ) || empty( $phone ) || empty( $dob ) || empty( $gender ) || empty( $quiz_type ) ) {
            wp_send_json_error( array( 'message' => 'Please fill all required fields.' ) );
        }

        $phone_digits = preg_replace( '/\D/', '', $phone );
        if ( strlen( $phone_digits ) !== 10 || $phone_digits[0] !== '0' ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid 10-digit Australian phone number starting with 0 (e.g. 0412345678).', 'hairy-hair' ) ) );
        }

        if ( empty( $dob ) ) {
            wp_send_json_error( array( 'message' => __( 'Date of Birth is required.', 'hairy-hair' ) ) );
        }
        $dob_parsed = \DateTime::createFromFormat( 'd-m-Y', $dob );
        if ( ! $dob_parsed || $dob_parsed->format( 'd-m-Y' ) !== $dob ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid Date of Birth in DD-MM-YYYY format.', 'hairy-hair' ) ) );
        }

        if ( empty( $email ) && ! $user_id ) {
            wp_send_json_error( array( 'message' => 'Please enter Email.' ) );
        } 

        $questions = $this->wpdb->get_results( $this->wpdb->prepare(
            "SELECT * FROM {$this->core_manager->get_table_questions()} WHERE quiz_type = %s",
            $quiz_type
        ) );

        if ( ! $questions ) {
            wp_send_json_error( array( 'message' => __( 'Could not retrieve questions for this quiz type.', 'hairy-hair' ) ) );
        }

        foreach($questions as $question) {
            $qq[ $question->ID ] = $question->question;
        }  

        require_once( 'products_arr.php' );

        $question_ids = [];  
 
        foreach ($_POST as $key => $value) { 

            if(is_array($value)) {
                foreach($value as $value_item) {
                    if (strpos($key, 'question_') === 0) {
                        $question_ids[] = $value_item;  
                    }
                }
            } else {
                if (strpos($key, 'question_') === 0) {
                    $question_ids[] = $value;  
                }
            }
            
        }
 
        if (!empty($question_ids)) {
            global $wpdb;
            $safe_ids = array_map('intval', $question_ids);  
            $id_list = implode(',', $safe_ids);
        
            $query = "SELECT * FROM {$this->core_manager->get_table_answers()} WHERE ID IN ($id_list)";
            $question_answers = $wpdb->get_results($query);
        }
        if($question_answers) {
            foreach ($question_answers as $answer) {
                $quiz_results_data[$answer->question_id][]['id'] = $qq[$answer->question_id];
                $quiz_results_data[$answer->question_id][]['answer'] = $answer->answer_label.') '.$answer->answer_text;

                if( $answer->filter_type == 'add') {
                    $add_class[] = $answer->filter_class;
                } else if( $answer->filter_type == 'remove') {
                    $remove_class[] = $answer->filter_class;
                }
            }
        } else {
            wp_send_json_error( array( 'message' => __( 'No answers found for the submitted questions.', 'hairy-hair' ) ) );
        }
 
        if (!empty($add_class) && is_array($add_class)) {
            $add_class = array_filter(array_map(function($cls) {
                return trim(str_replace(' ', ',', $cls));
            }, $add_class));
            $add_class = explode(',', implode(',', $add_class)); 
        }
 
        if (!empty($remove_class) && is_array($remove_class)) {
            $remove_class = array_filter(array_map(function($cls) {
                return trim(str_replace(' ', ',', $cls));
            }, $remove_class));
            $remove_class = explode(',', implode(',', $remove_class)); 
        }
 
        if (isset($_POST['gender'])) {
            $remove_class[] = $_POST['gender'];
        }

        // echo implode(' ', $add_class) . '<br>';
        // echo implode(' ', $remove_class) . '<br>';
 
        $added_ids = [];
        if (!empty($add_class)) {
            foreach ($add_class as $class) {
                $class = trim($class);
                if ($class === '') continue;

                 

                foreach ($products as $product) {
                    $p_class = explode(' ', $product['all_classes']);
                    $p_class = array_unique(array_filter(array_map(function($cls) {
                        return strtolower(trim($cls));
                    }, $p_class))); 


                    if (in_array(strtolower($class), $p_class)) {
                        $added_ids[] = $product['product_id'];
                    }
                }
            }
        }
        
        
        
        $removed_ids = [];
        if (!empty($remove_class)) {
            foreach ($remove_class as $class) {
                $class = trim($class);
                if ($class === '') continue;

                foreach ($products as $product) {
                    $p_class = explode(' ', $product['all_classes']);
                    $p_class = array_unique(array_filter(array_map(function($cls) {
                        return strtolower(trim($cls));
                    }, $p_class))); 


                    if (in_array(strtolower($class), $p_class)) {
                        $removed_ids[] = $product['product_id'];
                    }
                }
            }
        }

        // if($gender == 'male') 
        //     $removed_ids = array_merge($removed_ids, 'female');
        // else
        //     $removed_ids = array_merge($removed_ids, 'male');
 
        $added_ids = array_unique($added_ids); 
        $removed_ids = array_unique($removed_ids);  


        if(isset($added_ids) && isset($removed_ids))
            $recommended_products_ids = array_values(array_diff($added_ids, $removed_ids));
        
        
        $recommended_products_ids = array_unique($recommended_products_ids); 

        if(is_array($value)) {
            foreach($value as $value_item) {
                if (strpos($key, 'question_') === 0) {
                    $question_ids[] = $value_item;  
                }
            }
        } 

        // Only a photo this visitor uploaded (private-uploads owner check) is kept; anything else is dropped.
        $input_file = '';
        if ( isset( $_POST['input_file'] ) && '' !== trim( (string) $_POST['input_file'] ) && function_exists( 'hh_upload_claim' ) ) {
            $input_file = hh_upload_claim( wp_unslash( $_POST['input_file'] ) );
        }
        if ( '' !== $input_file ) {
            $next_index = count($quiz_results_data);
            $quiz_results_data[$next_index] = [
                ['id' => 'File'],
                ['answer' => $input_file]
            ];
        }

        // Stored answers: the submitted form without the nonce/idempotency token, with the verified photo reference.
        $posted = wp_unslash( $_POST );
        unset( $posted['hairy_quiz_frontend_nonce'], $posted[ Hairy_Submission_Guard::TOKEN_FIELD ] );
        $posted['input_file'] = $input_file;

        $data = array(
            'email'                => $email,
            'first_name'           => $first_name,
            'last_name'            => $last_name,
            'phone'                => $phone,
            'dob'                  => $dob,
            'gender'               => $gender,
            'quiz_answers'         => wp_json_encode( $posted ),
            'quiz_results'         => wp_json_encode( $quiz_results_data ),
            'recommended_products' => wp_json_encode( $recommended_products_ids ),
        );

        $fingerprint = $posted;
        unset( $fingerprint['action'] );
        $guard = Hairy_Submission_Guard::begin( 'quiz', $email, $fingerprint );
        if ( is_wp_error( $guard ) ) {
            wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
        }
        if ( isset( $guard['duplicate'] ) ) {
            wp_send_json_success( array( 'message' => $guard['duplicate']['message'], 'duplicate' => true ) );
        }
        $guard = $guard['guard'];

        // Every submission is a new history row (earlier questionnaires are never overwritten).
        $row = array_merge( $data, array( 'submission_date' => current_time( 'mysql' ) ) );
        if ( $user_id ) {
            $row['user_id'] = (int) $user_id;
        }
        $inserted = $this->wpdb->insert( $this->core_manager->get_table_quiz_results(), $row );
        if ( ! $inserted ) {
            Hairy_Submission_Guard::release( $guard );
            wp_send_json_error( array( 'message' => 'Failed to insert record.' ) );
        }
        $quiz_id = (int) $this->wpdb->insert_id;
        $message = 'Quiz result added. Redirecting...';
        Hairy_Submission_Guard::complete( $guard, $quiz_id, $message );

        if ($user_id) {
            update_user_meta($user_id, 'email', $email);
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'dob', $dob);
            update_user_meta($user_id, 'gender', $gender);
        } elseif ( function_exists( 'hh_guest_access_grant' ) ) {
            hh_guest_access_grant( $email, 'quiz', $quiz_id ); // Replaces the old quiz_user_email cookie.
        }

        wp_send_json_success( array( 'message' => $message ) );
    }

    public function handle_lead_submission() { 
        check_ajax_referer( 'hairy_lead_frontend_nonce', 'hairy_lead_frontend_nonce' );

        $user_id        = get_current_user_id(); 
        $first_name     = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name      = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $email          = isset( $_POST['email'] ) ? strtolower( trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ) ) : '';
        $phone          = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $dob            = isset( $_POST['dob'] ) ? sanitize_text_field( $_POST['dob'] ) : '';
        $gender         = isset( $_POST['gender'] ) ? sanitize_text_field( $_POST['gender'] ) : '';
        // Consent is true only when the checkbox (value "1") was actually ticked; absent or anything
        // else (including the old receive_communication radio's "on") is false. Optional.
        $consent_to_contact         = isset( $_POST['consent_to_contact'] ) && '1' === $_POST['consent_to_contact'];
        $hair_loss_duration         = isset( $_POST['hair-loss-duration'] ) ? sanitize_text_field( $_POST['hair-loss-duration'] ) : '';
        $expacted_results           = isset( $_POST['expacted_results'] ) ? sanitize_text_field( $_POST['expacted_results'] ) : '';
        // Step 2 and Step 3 picklists: compared unmodified (no trimming/case changes) against the exact allowed values.
        $picklist = function ( $name ) {
            return isset( $_POST[ $name ] ) && is_string( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
        };
        $age_group                  = $picklist( 'age_group' );
        $current_hair_loss          = $picklist( 'current_hair_loss' );
        $allergies_or_sensitivities = $picklist( 'allergies_or_sensitivities' );
        $taking_any_medications     = $picklist( 'taking_any_medications' );
        $high_blood_pressure        = $picklist( 'high_blood_pressure' );

        if ( empty( $first_name ) || empty( $last_name ) || empty( $email )  || empty( $phone ) || empty( $dob ) || empty( $gender ) || empty( $hair_loss_duration ) || empty( $expacted_results ) || '' === $age_group || '' === $current_hair_loss || '' === $allergies_or_sensitivities || '' === $taking_any_medications || '' === $high_blood_pressure ) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields.', 'hairy-hair' ) ) );
        }

        foreach ( array( 'age_group', 'current_hair_loss', 'allergies_or_sensitivities', 'taking_any_medications', 'high_blood_pressure' ) as $field ) {
            if ( ! in_array( $$field, self::lead_picklist_options( $field ), true ) ) {
                wp_send_json_error( array( 'message' => __( 'Please choose one of the listed options for each question.', 'hairy-hair' ) ) );
            }
        }

        // Step 3 explanations: required when the answer is "Yes", not stored (NULL) when "No".
        $explanations = array();
        foreach ( array( 'explain_allergies' => $allergies_or_sensitivities, 'explain_medications' => $taking_any_medications ) as $field => $answer ) {
            $explanations[ $field ] = null;
            if ( 'Yes' !== $answer ) {
                continue;
            }
            $text = self::clean_explanation( isset( $_POST[ $field ] ) ? $_POST[ $field ] : '' );
            if ( is_wp_error( $text ) ) {
                wp_send_json_error( array( 'message' => $text->get_error_message() ) );
            }
            if ( '' === $text ) {
                wp_send_json_error( array( 'message' => 'explain_allergies' === $field
                    ? __( 'Please explain your allergies or sensitivities.', 'hairy-hair' )
                    : __( 'Please list or explain your other medications or supplements.', 'hairy-hair' ) ) );
            }
            $explanations[ $field ] = $text;
        }

        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'hairy-hair' ) ) );
        }

        $phone_digits = preg_replace( '/\D/', '', $phone );
        if ( strlen( $phone_digits ) !== 10 || $phone_digits[0] !== '0' ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid 10-digit Australian phone number starting with 0 (e.g. 0412345678).', 'hairy-hair' ) ) );
        }

        if ( empty( $dob ) ) {
            wp_send_json_error( array( 'message' => __( 'Date of Birth is required.', 'hairy-hair' ) ) );
        }
        $dob_parsed = \DateTime::createFromFormat( 'd-m-Y', $dob );
        if ( ! $dob_parsed || $dob_parsed->format( 'd-m-Y' ) !== $dob ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid Date of Birth in DD-MM-YYYY format.', 'hairy-hair' ) ) );
        }

        $table = $this->core_manager->get_table_leads_results();

        $data = array(
            'first_name'                 => $first_name,
            'last_name'                  => $last_name,
            'email'                      => $email,
            'phone'                      => $phone,
            'dob'                        => $dob,
            'gender'                     => $gender,
            'receive_communication'      => $consent_to_contact ? 'yes' : 'no', // legacy column, kept readable in wp-admin
            'hair_loss_duration'         => $hair_loss_duration,
            'expacted_results'           => $expacted_results,
            'allergies_or_sensitivities' => $allergies_or_sensitivities,
            'taking_any_medications'     => $taking_any_medications,
            'high_blood_pressure'        => $high_blood_pressure,
            // Step 2 / Step 3 fields (schema v4).
            'consent_to_contact'         => $consent_to_contact ? 1 : 0,
            'age_group'                  => $age_group,
            'current_hair_loss'          => $current_hair_loss,
            'explain_allergies'          => $explanations['explain_allergies'],
            'explain_medications'        => $explanations['explain_medications'],
        );

        // Duplicate-request protection (same token or identical payload within 2 min), under a per-email lock.
        $guard = Hairy_Submission_Guard::begin( 'lead', $email, $data );
        if ( is_wp_error( $guard ) ) {
            wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
        }
        if ( isset( $guard['duplicate'] ) ) {
            wp_send_json_success( array( 'message' => $guard['duplicate']['message'], 'duplicate' => true ) );
        }
        $guard = $guard['guard'];

        // A guest's own previous lead, proven by the server-side guest-access token (not by email).
        $token_lead_id = 0;
        if ( ! $user_id && function_exists( 'hh_guest_access_row' ) ) {
            $prev = hh_guest_access_row( 'lead' );
            if ( $prev && strtolower( trim( $prev['email'] ) ) === $email ) {
                $token_lead_id = (int) $prev['ID'];
            }
        }

        // One Lead per email (runs under the per-email lock taken by the guard above, so concurrent
        // submissions for the same email are serialised and cannot both insert).
        $existing = $this->resolve_existing_lead( $email, (int) $user_id, $token_lead_id );
        $saved_message = __( 'Your information has been saved. Redirecting...', 'hairy-hair' );

        if ( 'ignore' === $existing['action'] ) {
            // Rule 3: a registered customer's Lead that is already in Zoho is never changed by a quiz
            // submission: no insert, no update, no Zoho call. Same response as a save (no email enumeration).
            Hairy_Submission_Guard::complete( $guard, (int) $existing['id'], $saved_message );
            wp_send_json_success( array( 'message' => $saved_message, 'crm' => 'pending' ) );
        }

        $row = array_merge( $data, array( 'submission_date' => current_time( 'mysql' ) ) );
        if ( Hairy_Quiz_DB_Migrations::is_current() ) {
            $row['zoho_sync_status'] = 'pending';
        } else {
            // Schema not upgraded yet: never lose answers; keep the Step 2/3 values as JSON in quiz_answers.
            $v4 = array( 'consent_to_contact', 'age_group', 'current_hair_loss', 'explain_allergies', 'explain_medications' );
            $row['quiz_answers'] = wp_json_encode( array_intersect_key( $row, array_flip( $v4 ) ) );
            $row = array_diff_key( $row, array_flip( $v4 ) );
        }

        $prev_lead_id = $token_lead_id;
        if ( 'update' === $existing['action'] ) {
            // Rule 2: authorised update of the guest Lead (same ID, user_id and zoho_id untouched).
            $lead_id = (int) $existing['id'];
            if ( isset( $row['zoho_sync_status'] ) ) {
                if ( 'create_uncertain' === $existing['zoho_sync_status'] ) {
                    unset( $row['zoho_sync_status'] ); // a CREATE may have landed: keep the flag so the retry searches first
                } else {
                    $row['zoho_sync_attempts'] = 0;
                    $row['zoho_sync_error']    = null;
                }
            }
            $saved = $this->wpdb->update( $table, $row, array( 'ID' => $lead_id, 'user_id' => 0 ) );
            if ( false === $saved ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'message' => 'Failed to update record.' ) );
            }
            $prev_lead_id = 0;
        } else {
            $row['user_id'] = $user_id ? (int) $user_id : 0;
            if ( 'park' === $existing['action'] && isset( $row['zoho_sync_status'] ) ) {
                // Same email as an unsynced Lead this visitor cannot prove is theirs: keep the answers,
                // never overwrite the other Lead, and do not sync (no duplicate Zoho Lead). Staff review.
                $row['zoho_sync_status'] = 'needs_review';
                $row['zoho_sync_error']  = 'UNVERIFIED_EXISTING_EMAIL';
            }
            if ( ! $this->wpdb->insert( $table, $row ) ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'message' => 'Failed to insert record.' ) );
            }
            $lead_id = (int) $this->wpdb->insert_id;
        }
        Hairy_Submission_Guard::complete( $guard, $lead_id, $saved_message );

        if ( $user_id ) {
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'dob', $dob);
            update_user_meta($user_id, 'gender', $gender);
            update_user_meta($user_id, 'email', $email);
        } elseif ( function_exists( 'hh_guest_access_grant' ) && 'park' !== $existing['action'] ) {
            hh_guest_access_grant( $email, 'lead', $lead_id ); // Replaces the old lead_user_email cookie.
        }

        // Zoho: saved locally first; sync failures are queued for retry, never lost or blocking.
        $crm = 'not_configured';
        if ( 'park' !== $existing['action'] && class_exists( 'HH_Zoho_Lead_Sync' ) ) {
            try {
                $crm = HH_Zoho_Lead_Sync::sync_lead( $lead_id, array( 'prev_lead_id' => $prev_lead_id, 'lock_wait' => 10 ) );
            } catch ( \Throwable $e ) {
                $crm = 'error';
                error_log( sprintf( 'HairyHair lead #%d: unexpected Zoho sync error (%s).', $lead_id, get_class( $e ) ) );
            }
        }

        wp_send_json_success( array(
            'message' => 'synced' === $crm ? "Information inserted. Redirecting..." : $saved_message,
            'crm'     => 'synced' === $crm ? 'synced' : 'pending', // Saved locally; CRM sync not (yet) confirmed.
        ) );
        exit;
    }

    /**
     * Decides what a lead-form submission does with existing Leads for the same (normalised) email.
     * Caller holds the per-email lock. Never deletes or merges rows.
     *
     * - No row with this email                                -> insert.
     * - Any row owned by a WordPress user AND with a zoho_id    -> ignore (rule 3; checked first, most protective).
     * - Guest row (user_id 0) without zoho_id, and the submitter is authorised for it
     *   (guest-access token for that exact row, or a logged-in account with this email verified)
     *                                                           -> update that row (rule 2).
     * - Guest row without zoho_id, submitter not authorised     -> park: new row, needs_review, no Zoho sync.
     * - Anything else                                         -> insert (existing P3 behaviour; the shared
     *   Zoho sync applies its ownership rules: update own Lead, or park possible duplicates for review).
     *
     * @param string $email         Normalised email.
     * @param int    $user_id       Submitting WordPress user (0 = guest).
     * @param int    $token_lead_id Lead row proven by the guest-access token, or 0.
     * @return array{action: string, id?: int, zoho_sync_status?: string|null}
     */
    protected function resolve_existing_lead( $email, $user_id, $token_lead_id ) {
        $rows = $this->wpdb->get_results( $this->wpdb->prepare(
            "SELECT * FROM {$this->core_manager->get_table_leads_results()} WHERE LOWER(TRIM(email)) = %s ORDER BY ID DESC", // * : works before and after the zoho_id / sync-state migrations
            $email
        ) );
        if ( ! $rows ) {
            return array( 'action' => 'insert' );
        }

        $has_zoho = function ( $r ) {
            $z = isset( $r->zoho_id ) ? trim( (string) $r->zoho_id ) : '';
            return '' !== $z && '0' !== $z;
        };
        $real_user = function ( $r ) {
            $id = (int) $r->user_id; // legacy rows stored a Zoho Lead ID in user_id: not a WordPress user
            return $id > 0 && $id < 100000000000 && get_userdata( $id );
        };

        foreach ( $rows as $r ) {
            if ( $real_user( $r ) && $has_zoho( $r ) ) {
                return array( 'action' => 'ignore', 'id' => (int) $r->ID );
            }
        }

        $guest_unsynced = array_values( array_filter( $rows, function ( $r ) use ( $has_zoho ) {
            return 0 === (int) $r->user_id && ! $has_zoho( $r );
        } ) );
        if ( $guest_unsynced ) {
            $verified_account = false;
            if ( $user_id > 0 && class_exists( 'HH_Registration_Lead' ) ) {
                $user = get_userdata( $user_id );
                $verified_account = $user && HH_Registration_Lead::normalize_email( $user->user_email ) === $email && HH_Registration_Lead::is_email_verified( $user_id );
            }
            foreach ( $guest_unsynced as $r ) {
                if ( ( $token_lead_id && (int) $r->ID === $token_lead_id ) ) {
                    return array( 'action' => 'update', 'id' => (int) $r->ID, 'zoho_sync_status' => isset( $r->zoho_sync_status ) ? $r->zoho_sync_status : null );
                }
            }
            if ( $verified_account ) {
                $r = $guest_unsynced[0]; // newest
                return array( 'action' => 'update', 'id' => (int) $r->ID, 'zoho_sync_status' => isset( $r->zoho_sync_status ) ? $r->zoho_sync_status : null );
            }
            return array( 'action' => 'park' );
        }

        return array( 'action' => 'insert' );
    }
}
