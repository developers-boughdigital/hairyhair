<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}


class Hairy_Quiz_Frontend {

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
        wp_enqueue_style( 'hairy-frontend-css', HAIRY_URL . 'css/hairy-frontend.css', array(), '1.0.2' );
        wp_enqueue_script( 'hairy-frontend-js', HAIRY_URL . 'js/hairy-frontend.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.2', true );
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

        if(isset($_POST['input_file']) && !empty($_POST['input_file'])) {
            $input_file = $_POST['input_file']; 
            $next_index = count($quiz_results_data);
            $quiz_results_data[$next_index] = [
                ['id' => 'File'],
                ['answer' => $input_file]
            ];
        }

        $data = array(
            'email'                => $email,
            'first_name'           => $first_name,
            'last_name'            => $last_name,
            'phone'                => $phone,
            'dob'                  => $dob,
            'gender'               => $gender,
            'quiz_answers'         => wp_json_encode( $_POST ),
            'quiz_results'         => wp_json_encode( $quiz_results_data ),
            'recommended_products' => wp_json_encode( $recommended_products_ids ),
            'submission_date'      => current_time( 'mysql' ),
        ); 
 
        if ($user_id) {
            update_user_meta($user_id, 'email', $email);
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'dob', $dob);
            update_user_meta($user_id, 'gender', $gender);
        } else {
            $cookie_name  = 'quiz_user_email';
            $cookie_value = $user_id ? $user_id : $email;
            $cookie_time  = time() + ( 30 * DAY_IN_SECONDS );

            setcookie( $cookie_name, $cookie_value, $cookie_time, COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE[ $cookie_name ] = $cookie_value;
 
        }
        $existing = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->core_manager->get_table_quiz_results()} WHERE user_id = %d",
                $user_id
            )
        ); 

        $where_column = $user_id ? 'user_id' : 'email';
        $where_value  = $user_id ? $user_id : $email;
        $where_format = $user_id ? '%d' : '%s';
        $formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );

        if ( $existing ) { 

            $updated = $this->wpdb->update(
                $this->core_manager->get_table_quiz_results(),
                $data,
                array( $where_column => $where_value ),
                $formats,
                array( $where_format )
            );

            if ( $updated !== false ) {
                wp_send_json_success( array(
                    'message' => 'Quiz result updated. Redirecting...'
                ));
            } else {
                wp_send_json_error( array( 'message' => 'Failed to update record.' ) );
            }

        } else {
  
            $inserted = $this->wpdb->insert(
                $this->core_manager->get_table_quiz_results(),
                array_merge( [ $where_column => $where_value ], $data ),
                array_merge( [ $where_format ], $formats )
            );

            
            // wp_send_json_error( array( 'message' => $wpdb->last_error ) );
            if ( $inserted ) {
                wp_send_json_success( array(
                    'message' => 'Quiz result added. Redirecting...'
                ));
            } else {
                wp_send_json_error( array( 'message' => 'Failed to insert record.' ) );
            }
             
        }

    }

    public function handle_lead_submission() { 
        check_ajax_referer( 'hairy_lead_frontend_nonce', 'hairy_lead_frontend_nonce' );

        $user_id        = get_current_user_id(); 
        $first_name     = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name      = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $email          = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
        $phone          = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
        $dob            = isset( $_POST['dob'] ) ? sanitize_text_field( $_POST['dob'] ) : '';
        $gender         = isset( $_POST['gender'] ) ? sanitize_text_field( $_POST['gender'] ) : '';
        $receive_communication      = isset( $_POST['receive_communication'] ) ? sanitize_text_field( $_POST['receive_communication'] ) : '';
        $hair_loss_duration         = isset( $_POST['hair-loss-duration'] ) ? sanitize_text_field( $_POST['hair-loss-duration'] ) : '';
        $expacted_results           = isset( $_POST['expacted_results'] ) ? sanitize_text_field( $_POST['expacted_results'] ) : '';
        $allergies_or_sensitivities = isset( $_POST['allergies_or_sensitivities'] ) ? sanitize_text_field( $_POST['allergies_or_sensitivities'] ) : '';
        $taking_any_medications     = isset( $_POST['taking_any_medications'] ) ? sanitize_text_field( $_POST['taking_any_medications'] ) : '';
        $high_blood_pressure        = isset( $_POST['high_blood_pressure'] ) ? sanitize_text_field( $_POST['high_blood_pressure'] ) : ''; 
         
        if ( empty( $first_name ) || empty( $last_name ) || empty( $email )  || empty( $phone ) || empty( $dob ) || empty( $gender ) || empty( $receive_communication ) || empty( $hair_loss_duration ) || empty( $expacted_results ) || empty( $allergies_or_sensitivities ) || empty( $taking_any_medications ) || empty( $high_blood_pressure )) {
            wp_send_json_error( array( 'message' => __( 'Please fill all required fields.', 'hairy-hair' ) ) );
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

        global $wpdb;
        $table = $this->core_manager->get_table_leads_results();       
        /*$existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d",$user_id
        ));*/

       /* $existing = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM $table WHERE email = %d",$email
        ));*/

        if ( $user_id) {
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'dob', $dob);
            update_user_meta($user_id, 'gender', $gender);
            update_user_meta($user_id, 'email', $email);
        } else {
            $cookie_name  = 'lead_user_email';
            $cookie_value = $user_id ? $user_id : $email;
            $cookie_time  = time() + ( 30 * DAY_IN_SECONDS );

            setcookie( $cookie_name, $cookie_value, $cookie_time, COOKIEPATH, COOKIE_DOMAIN );
            $_COOKIE[ $cookie_name ] = $cookie_value;
        }

        $data = array(
            'first_name'                 => $first_name,
            'last_name'                  => $last_name,
            'email'                      => $email,
            'phone'                      => $phone,
            'dob'                        => $dob,
            'gender'                     => $gender,
            'receive_communication'      => $receive_communication,
            'hair_loss_duration'         => $hair_loss_duration,
            'expacted_results'           => $expacted_results,
            'allergies_or_sensitivities' => $allergies_or_sensitivities,
            'taking_any_medications'     => $taking_any_medications,
            'high_blood_pressure'        => $high_blood_pressure,
            'submission_date'            => current_time( 'mysql' ),
        );      
        $formats = array(
            '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s',
            '%s', '%s', '%s', '%s', '%s'
        );
        $zoho_data = [
            'First_Name'                                        => $first_name,                 
            'Last_Name'                                         => $last_name,                 
            'Email'                                             => $email,                 
            'Phone'                                             => $phone,
            'Date_Of_Birth'                                     => date( "Y-m-d", strtotime($dob) ) , //'1990-04-11',
            'Gender'                                            => $gender,
            'How_long_have_you_been_experiencing_hair_loss'     => $hair_loss_duration,
            'What_results_are_you_hoping_to_achieve'            => $expacted_results,
            'Explain_Other_Medications_or_Supplements'          => $allergies_or_sensitivities,
            'Are_you_currently_taking_any_other_medications'    => $taking_any_medications,
            'Do_you_have_a_history_with_high_blood_pressure'    => $high_blood_pressure,
            'Lead_Source'                                       => 'Website - Quiz Questionnaire',
            'Website'                                           => get_site_url().'/lets-get-started',
        ];
        // Insert into local DB (always, regardless of Zoho)
        $db_where_column = $user_id ? 'user_id' : 'email';
        $db_where_value  = $user_id ? $user_id   : $email;
        $db_where_format = $user_id ? '%d'        : '%s';

        $inserted = $this->wpdb->insert(
            $table,
            array_merge( [ $db_where_column => $db_where_value ], $data ),
            array_merge( [ $db_where_format ], $formats )
        );

        if ( ! $inserted ) {
            wp_send_json_error( array( 'message' => 'Failed to insert record.' ) );
            exit;
        }

        // Optional Zoho sync — does not block the response if plugin is inactive
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ( is_plugin_active( 'connect-with-zoho/class-connect-with-zoho.php' ) ) {
            $zoho_obj = new Connect_To_Zoho();
            $zoho_obj->updateInsertCRMrecord( 'Leads', '', $zoho_data );
        }

        wp_send_json_success( array( 'message' => "Information inserted. Redirecting..." ) );
        exit;

        /*$where_column = $email ? 'email' : 'email';
        $where_value  = $email ? $email : $email;
        $where_format = $email ? '%s' : '%s';*/

        /*if ( $existing ) {
            $updated = $wpdb->update(
                $table,
                $data,
                array( $where_column => $where_value ),
                $formats,
                array( $where_format )
            );

            if ( $updated !== false ) {
                wp_send_json_success( array( 'message' => "Information updated. Redirecting..." ) );
            }
            wp_send_json_error( array( 'message' => 'Failed to update record.' ) );

        } else { 
            $inserted = $this->wpdb->insert(
                $table,
                array_merge( [ $where_column => $where_value ], $data ),
                array_merge( [ $where_format ], $formats )
            );
            //$inserted = $this->wpdb->insert($table ,$data, $formats );
            if ( $inserted ) {
                wp_send_json_success( array( 'message' => "Information inserted. Redirecting..." ) );
            } 
            wp_send_json_error( array( 'message' => 'Failed to insert record.' ) );            
        }*/
    }
}