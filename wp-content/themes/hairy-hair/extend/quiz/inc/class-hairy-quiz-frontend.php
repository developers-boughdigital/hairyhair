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
        // Lead form Step 1 / Step 2 (saved as the customer moves through the quiz).
        add_action( 'wp_ajax_hairy_lead_step', array( $this, 'handle_lead_step' ) );
        add_action( 'wp_ajax_nopriv_hairy_lead_step', array( $this, 'handle_lead_step' ) );
    }


    public function enqueue_frontend_scripts() {
        wp_enqueue_style( 'hairy-frontend-css', HAIRY_URL . 'css/hairy-frontend.css', array(), '1.0.5' );
        wp_enqueue_script( 'hairy-frontend-js', HAIRY_URL . 'js/hairy-frontend.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.5', true );
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

    /* =====================================================================================
     * Lead form (personal-information.php): one Lead per email, saved step by step.
     *
     * Step 1 (AJAX hairy_lead_step, lead_step=1): look up the email, then insert a Pending Lead,
     *   resume the customer's own Pending Lead, or stop (completed / verification / review).
     * Step 2 (AJAX hairy_lead_step, lead_step=2): save Step 2 answers on the same Lead (Pending).
     * Step 3 (form submit, hairy_submit_lead): save the final answers, mark the Lead Completed.
     *
     * Ownership (P2/P3): a guest owns only the Lead their guest-access token was issued for; a
     * logged-in customer owns Leads with their user ID, and unowned Leads with their email once
     * that email is verified. An email match alone never authorises reading or changing a Lead.
     * All steps run under the per-email lock of Hairy_Submission_Guard (no duplicate rows from
     * concurrent requests); Zoho goes through the shared HH_Zoho_Lead_Sync service.
     * ===================================================================================== */

    const LEAD_MSG_VERIFY = 'We could not continue with this email address here. If you have already started or completed this quiz, please log in to your account, or continue on the device where you started it.';
    const LEAD_MSG_DONE   = 'Your email address has already been used to complete this quiz.';
    const LEAD_MSG_REVIEW = 'We need to check your details before you can continue. Please call us on 1300 800 546 and we will help you.';

    /**
     * Step 1 fields (validated; sends a JSON error otherwise).
     *
     * @return array Lead columns.
     */
    protected function lead_input_step1() {
        $first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $email      = isset( $_POST['email'] ) ? strtolower( trim( sanitize_email( wp_unslash( $_POST['email'] ) ) ) ) : '';
        $phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $dob        = isset( $_POST['dob'] ) ? sanitize_text_field( $_POST['dob'] ) : '';
        $gender     = isset( $_POST['gender'] ) ? sanitize_text_field( $_POST['gender'] ) : '';
        // Consent is true only when the checkbox (value "1") was actually ticked; absent or anything else is false.
        $consent    = isset( $_POST['consent_to_contact'] ) && '1' === $_POST['consent_to_contact'];

        if ( empty( $first_name ) || empty( $last_name ) || empty( $email ) || empty( $phone ) || empty( $dob ) || empty( $gender ) ) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields.', 'hairy-hair' ) ) );
        }
        if ( ! is_email( $email ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'hairy-hair' ) ) );
        }
        $phone_digits = preg_replace( '/\D/', '', $phone );
        if ( strlen( $phone_digits ) !== 10 || $phone_digits[0] !== '0' ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid 10-digit Australian phone number starting with 0 (e.g. 0412345678).', 'hairy-hair' ) ) );
        }
        $dob_parsed = \DateTime::createFromFormat( 'd-m-Y', $dob );
        if ( ! $dob_parsed || $dob_parsed->format( 'd-m-Y' ) !== $dob ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a valid Date of Birth in DD-MM-YYYY format.', 'hairy-hair' ) ) );
        }
        return array(
            'first_name'            => $first_name,
            'last_name'             => $last_name,
            'email'                 => $email,
            'phone'                 => $phone,
            'dob'                   => $dob,
            'gender'                => $gender,
            'consent_to_contact'    => $consent ? 1 : 0,
            'receive_communication' => $consent ? 'yes' : 'no', // legacy column, kept readable in wp-admin
        );
    }

    /**
     * Step 2 fields (validated).
     *
     * @return array Lead columns.
     */
    protected function lead_input_step2() {
        $picklist = function ( $name ) {
            return isset( $_POST[ $name ] ) && is_string( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
        };
        $values = array(
            'hair_loss_duration' => isset( $_POST['hair-loss-duration'] ) ? sanitize_text_field( $_POST['hair-loss-duration'] ) : '',
            'expacted_results'   => isset( $_POST['expacted_results'] ) ? sanitize_text_field( $_POST['expacted_results'] ) : '',
            'age_group'          => $picklist( 'age_group' ),
            'current_hair_loss'  => $picklist( 'current_hair_loss' ),
        );
        if ( in_array( '', $values, true ) ) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields.', 'hairy-hair' ) ) );
        }
        foreach ( array( 'age_group', 'current_hair_loss' ) as $field ) {
            if ( ! in_array( $values[ $field ], self::lead_picklist_options( $field ), true ) ) {
                wp_send_json_error( array( 'message' => __( 'Please choose one of the listed options for each question.', 'hairy-hair' ) ) );
            }
        }
        return $values;
    }

    /**
     * Step 3 fields (validated; explanations required for "Yes", not stored for "No").
     *
     * @return array Lead columns.
     */
    protected function lead_input_step3() {
        $picklist = function ( $name ) {
            return isset( $_POST[ $name ] ) && is_string( $_POST[ $name ] ) ? wp_unslash( $_POST[ $name ] ) : '';
        };
        $values = array(
            'allergies_or_sensitivities' => $picklist( 'allergies_or_sensitivities' ),
            'taking_any_medications'     => $picklist( 'taking_any_medications' ),
            'high_blood_pressure'        => $picklist( 'high_blood_pressure' ),
        );
        if ( in_array( '', $values, true ) ) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields.', 'hairy-hair' ) ) );
        }
        foreach ( $values as $field => $value ) {
            if ( ! in_array( $value, self::lead_picklist_options( $field ), true ) ) {
                wp_send_json_error( array( 'message' => __( 'Please choose one of the listed options for each question.', 'hairy-hair' ) ) );
            }
        }
        foreach ( array( 'explain_allergies' => 'allergies_or_sensitivities', 'explain_medications' => 'taking_any_medications' ) as $field => $question ) {
            $values[ $field ] = null;
            if ( 'Yes' !== $values[ $question ] ) {
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
            $values[ $field ] = $text;
        }
        return $values;
    }

    /**
     * Which Lead this request may use for the email (caller holds the per-email lock).
     *
     * @param string $email   Normalised email.
     * @param int    $user_id Logged-in user (0 = guest).
     * @return array{action: string, id?: int, row?: object, ids?: int[]}
     *         insert | resume (own Pending Lead) | completed (own Completed Lead) | verify | review
     */
    protected function resolve_lead( $email, $user_id ) {
        $rows = $this->wpdb->get_results( $this->wpdb->prepare(
            "SELECT * FROM {$this->core_manager->get_table_leads_results()} WHERE email = %s ORDER BY ID DESC", // case-insensitive collation; indexed
            $email
        ) );
        if ( ! $rows ) {
            return array( 'action' => 'insert' );
        }

        $token_lead_id = 0;
        if ( ! $user_id && function_exists( 'hh_guest_access_row' ) ) {
            $own = hh_guest_access_row( 'lead' );
            $token_lead_id = ( $own && strtolower( trim( $own['email'] ) ) === $email ) ? (int) $own['ID'] : 0;
        }
        $verified = false;
        if ( $user_id > 0 && class_exists( 'HH_Registration_Lead' ) ) {
            $user     = get_userdata( $user_id );
            $verified = $user && HH_Registration_Lead::normalize_email( $user->user_email ) === $email && HH_Registration_Lead::is_email_verified( $user_id );
        }

        $authorised = array();
        foreach ( $rows as $r ) {
            $uid  = (int) $r->user_id;
            $real = $uid > 0 && $uid < 100000000000 && get_userdata( $uid ); // legacy rows hold a Zoho ID in user_id
            if ( $user_id > 0 ) {
                $ok = ( $real && $uid === $user_id ) || ( ! $real && $verified );
            } else {
                $ok = $token_lead_id && (int) $r->ID === $token_lead_id && 0 === $uid;
            }
            if ( $ok ) {
                $authorised[] = $r;
            }
        }
        if ( ! $authorised ) {
            return array( 'action' => 'verify' );
        }

        $status = function ( $r ) { return isset( $r->lead_status ) ? (string) $r->lead_status : ''; };
        foreach ( $authorised as $r ) {
            if ( 'Completed' === $status( $r ) ) {
                return array( 'action' => 'completed', 'id' => (int) $r->ID );
            }
        }
        $pending = array_values( array_filter( $authorised, function ( $r ) use ( $status ) { return 'Pending' === $status( $r ); } ) );
        if ( count( $pending ) > 1 ) {
            // Several open Leads: prefer the token's Lead (guest) or the customer's own (logged in).
            $mine = array_values( array_filter( $pending, function ( $r ) use ( $user_id, $token_lead_id ) {
                return $user_id > 0 ? (int) $r->user_id === $user_id : (int) $r->ID === $token_lead_id;
            } ) );
            $pending = 1 === count( $mine ) ? $mine : $pending;
        }
        if ( 1 === count( $pending ) ) {
            return array( 'action' => 'resume', 'id' => (int) $pending[0]->ID, 'row' => $pending[0] );
        }
        return array( 'action' => 'review', 'ids' => array_map( function ( $r ) { return (int) $r->ID; }, $pending ?: $authorised ) );
    }

    /**
     * Sends the response for a Lead this request may not continue with. Never says whether a
     * questionnaire was completed unless the requester owns that Lead.
     *
     * @param array  $res   From resolve_lead().
     * @param string $email For logging only (not logged).
     */
    protected function lead_blocked_response( array $res ) {
        if ( 'completed' === $res['action'] ) {
            wp_send_json_error( array( 'state' => 'already_completed', 'message' => __( self::LEAD_MSG_DONE, 'hairy-hair' ) ) );
        }
        if ( 'review' === $res['action'] ) {
            error_log( sprintf( 'HairyHair lead form: %d open Leads for one email need admin review (#%s).', count( $res['ids'] ), implode( ', #', $res['ids'] ) ) );
            wp_send_json_error( array( 'state' => 'needs_review', 'message' => __( self::LEAD_MSG_REVIEW, 'hairy-hair' ) ) );
        }
        wp_send_json_error( array( 'state' => 'verification_required', 'message' => __( self::LEAD_MSG_VERIFY, 'hairy-hair' ) ) );
    }

    /**
     * Keeps only columns the table has (if a migration has not run yet, the rest go to quiz_answers).
     *
     * @param array $row Column => value.
     * @return array
     */
    protected function fit_lead_row( array $row ) {
        $cols  = array_flip( (array) $this->wpdb->get_col( "SHOW COLUMNS FROM {$this->core_manager->get_table_leads_results()}" ) );
        $extra = array_diff_key( $row, $cols );
        $row   = array_intersect_key( $row, $cols );
        if ( $extra && isset( $cols['quiz_answers'] ) ) {
            $row['quiz_answers'] = wp_json_encode( $extra );
        }
        return $row;
    }

    /**
     * Marks a Lead for a fresh Zoho sync after its answers changed (keeps create_uncertain, so the
     * next sync searches before creating, and needs_review, which waits for an administrator).
     *
     * @param array       $row      Values being saved.
     * @param string|null $existing Current zoho_sync_status.
     */
    protected function lead_resync( array &$row, $existing ) {
        if ( in_array( (string) $existing, array( 'create_uncertain', 'needs_review' ), true ) ) {
            return;
        }
        $row['zoho_sync_status']   = 'pending';
        $row['zoho_sync_attempts'] = 0;
        $row['zoho_sync_error']    = null;
    }

    /**
     * @param int $lead_id Lead ID.
     * @return string Zoho sync result.
     */
    protected function lead_sync_now( $lead_id ) {
        if ( ! class_exists( 'HH_Zoho_Lead_Sync' ) ) {
            return 'not_configured';
        }
        try {
            return HH_Zoho_Lead_Sync::sync_lead( $lead_id, array( 'lock_wait' => 10 ) );
        } catch ( \Throwable $e ) {
            error_log( sprintf( 'HairyHair lead #%d: unexpected Zoho sync error (%s).', $lead_id, get_class( $e ) ) );
            return 'error';
        }
    }

    /**
     * Saved Step 2/3 answers of the customer's own Pending Lead, to restore the form on resume.
     *
     * @param object $row Lead.
     * @return array{answers: array, next_step: int}
     */
    protected function lead_resume_data( $row ) {
        $answers = array();
        $map = array( 'hair-loss-duration' => 'hair_loss_duration', 'expacted_results' => 'expacted_results', 'age_group' => 'age_group', 'current_hair_loss' => 'current_hair_loss', 'allergies_or_sensitivities' => 'allergies_or_sensitivities', 'explain_allergies' => 'explain_allergies', 'taking_any_medications' => 'taking_any_medications', 'explain_medications' => 'explain_medications', 'high_blood_pressure' => 'high_blood_pressure' );
        foreach ( $map as $field => $column ) {
            if ( isset( $row->$column ) && '' !== trim( (string) $row->$column ) ) {
                $answers[ $field ] = (string) $row->$column;
            }
        }
        $step2_done = isset( $answers['hair-loss-duration'], $answers['expacted_results'], $answers['age_group'], $answers['current_hair_loss'] );
        return array( 'answers' => $answers, 'next_step' => $step2_done ? 3 : 2 );
    }

    /**
     * After a save: profile fields for logged-in customers, guest-access token for guests.
     */
    protected function lead_after_save( $user_id, array $d1, $lead_id ) {
        if ( $user_id ) {
            foreach ( array( 'first_name', 'last_name', 'phone', 'dob', 'gender', 'email' ) as $key ) {
                update_user_meta( $user_id, $key, $d1[ $key ] );
            }
        } elseif ( function_exists( 'hh_guest_access_grant' ) ) {
            hh_guest_access_grant( $d1['email'], 'lead', $lead_id ); // the token is what authorises Steps 2 and 3
        }
    }

    /**
     * AJAX hairy_lead_step: Step 1 (lead_step=1) and Step 2 (lead_step=2).
     */
    public function handle_lead_step() {
        check_ajax_referer( 'hairy_lead_frontend_nonce', 'hairy_lead_frontend_nonce' );
        $step    = isset( $_POST['lead_step'] ) ? absint( $_POST['lead_step'] ) : 0;
        $user_id = (int) get_current_user_id();
        if ( ! in_array( $step, array( 1, 2 ), true ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid request.', 'hairy-hair' ) ) );
        }
        $d1 = $this->lead_input_step1();
        $d2 = 2 === $step ? $this->lead_input_step2() : array();
        $email = $d1['email'];

        // Token-only replay: the Step 1/2 result depends on who asks, so an identical payload from another browser is never replayed.
        $guard = Hairy_Submission_Guard::begin( 'lead', $email, array_merge( $d1, $d2, array( '_step' => $step ) ), false );
        if ( is_wp_error( $guard ) ) {
            wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
        }
        if ( isset( $guard['duplicate'] ) ) {
            wp_send_json_success( array_merge( (array) ( $guard['duplicate']['data'] ?? array() ), array( 'duplicate' => true ) ) );
        }
        $guard = $guard['guard'];
        $table = $this->core_manager->get_table_leads_results();
        $res   = $this->resolve_lead( $email, $user_id );

        if ( ! in_array( $res['action'], array( 'insert', 'resume' ), true ) ) {
            Hairy_Submission_Guard::release( $guard );
            $this->lead_blocked_response( $res );
        }

        if ( 2 === $step ) {
            if ( 'resume' !== $res['action'] ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'state' => 'restart', 'message' => __( 'Please go back to the first step and press Next again.', 'hairy-hair' ) ) );
            }
            $row = $this->fit_lead_row( array_merge( $d1, $d2, array( 'submission_date' => current_time( 'mysql' ) ) ) );
            if ( false === $this->wpdb->update( $table, $row, array( 'ID' => $res['id'] ) ) ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
            }
            $data = array( 'state' => 'saved', 'next_step' => 3 );
            Hairy_Submission_Guard::complete( $guard, $res['id'], '', $data );
            $this->lead_after_save( $user_id, $d1, $res['id'] );
            wp_send_json_success( $data ); // Step 2 is local only; Zoho gets it with Step 3.
        }

        // Step 1.
        $values = array_merge( $d1, array( 'submission_date' => current_time( 'mysql' ) ) );
        if ( 'insert' === $res['action'] ) {
            $values['user_id']          = $user_id;
            $values['lead_status']      = 'Pending';
            $values['zoho_sync_status'] = 'pending';
            if ( ! $this->wpdb->insert( $table, $this->fit_lead_row( $values ) ) ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
            }
            $lead_id = (int) $this->wpdb->insert_id;
            $resume  = array( 'answers' => array(), 'next_step' => 2 );
        } else {
            $lead_id = (int) $res['id'];
            $this->lead_resync( $values, isset( $res['row']->zoho_sync_status ) ? $res['row']->zoho_sync_status : null );
            if ( false === $this->wpdb->update( $table, $this->fit_lead_row( $values ), array( 'ID' => $lead_id ) ) ) {
                Hairy_Submission_Guard::release( $guard );
                wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
            }
            $resume = $this->lead_resume_data( $res['row'] );
        }
        $state = 'insert' === $res['action'] ? 'new_lead' : 'resume_pending';
        // Replayed for an identical repeat request (answers are not kept in the replay record).
        Hairy_Submission_Guard::complete( $guard, $lead_id, '', array( 'state' => $state, 'next_step' => $resume['next_step'] ) );
        $this->lead_after_save( $user_id, $d1, $lead_id );

        $crm = $this->lead_sync_now( $lead_id );
        if ( 'new_lead' === $state && 'synced' !== $crm ) {
            $state = 'sync_retry'; // saved locally; Zoho will be retried in the background
        }
        wp_send_json_success( array(
            'state'     => $state,
            'next_step' => $resume['next_step'],
            'answers'   => $resume['answers'],
            'crm'       => 'synced' === $crm ? 'synced' : 'pending',
        ) );
    }

    /**
     * Final submission (Step 3, form submit, AJAX hairy_submit_lead): completes the Lead.
     */
    public function handle_lead_submission() {
        check_ajax_referer( 'hairy_lead_frontend_nonce', 'hairy_lead_frontend_nonce' );
        $user_id = (int) get_current_user_id();
        $d1 = $this->lead_input_step1();
        $d2 = $this->lead_input_step2();
        $d3 = $this->lead_input_step3();
        $email = $d1['email'];

        $guard = Hairy_Submission_Guard::begin( 'lead', $email, array_merge( $d1, $d2, $d3, array( '_step' => 3 ) ) );
        if ( is_wp_error( $guard ) ) {
            wp_send_json_error( array( 'message' => __( 'We could not save your answers just now. Please try again in a moment.', 'hairy-hair' ) ) );
        }
        if ( isset( $guard['duplicate'] ) ) {
            wp_send_json_success( array( 'message' => $guard['duplicate']['message'], 'duplicate' => true ) );
        }
        $guard = $guard['guard'];
        $table = $this->core_manager->get_table_leads_results();
        $res   = $this->resolve_lead( $email, $user_id );
        if ( ! in_array( $res['action'], array( 'insert', 'resume' ), true ) ) {
            Hairy_Submission_Guard::release( $guard );
            $this->lead_blocked_response( $res );
        }

        // Completed only together with all answers in the same write.
        $values = array_merge( $d1, $d2, $d3, array( 'lead_status' => 'Completed', 'submission_date' => current_time( 'mysql' ) ) );
        if ( 'insert' === $res['action'] ) { // no Step 1 save happened (e.g. an old cached page)
            $values['user_id']          = $user_id;
            $values['zoho_sync_status'] = 'pending';
            $saved   = $this->wpdb->insert( $table, $this->fit_lead_row( $values ) );
            $lead_id = (int) $this->wpdb->insert_id;
        } else {
            $lead_id = (int) $res['id'];
            $this->lead_resync( $values, isset( $res['row']->zoho_sync_status ) ? $res['row']->zoho_sync_status : null );
            $saved = false !== $this->wpdb->update( $table, $this->fit_lead_row( $values ), array( 'ID' => $lead_id ) );
        }
        if ( ! $saved ) {
            Hairy_Submission_Guard::release( $guard );
            wp_send_json_error( array( 'message' => 'Failed to save record.' ) );
        }
        $saved_message = __( 'Your information has been saved. Redirecting...', 'hairy-hair' );
        Hairy_Submission_Guard::complete( $guard, $lead_id, $saved_message );
        $this->lead_after_save( $user_id, $d1, $lead_id );

        // Saved and Completed locally first; a Zoho failure is retried in the background.
        $crm = $this->lead_sync_now( $lead_id );
        wp_send_json_success( array(
            'message' => 'synced' === $crm ? "Information inserted. Redirecting..." : $saved_message,
            'crm'     => 'synced' === $crm ? 'synced' : 'pending',
        ) );
        exit;
    }
}
