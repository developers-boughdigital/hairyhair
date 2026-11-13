<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Handles all admin-side functionalities for the quiz manager.
 */
class Hairy_Quiz_Admin {

    /**
     * @var Hairy_Quiz_Manager_Core $core_manager Instance of the core quiz manager class.
     */
    protected $core_manager;

    /**
     * @var wpdb $wpdb WordPress database access abstraction object.
     */
    protected $wpdb;

    /**
     * Constructor.
     *
     * @param Hairy_Quiz_Manager_Core $core_manager The core manager instance.
     */
    public function __construct( Hairy_Quiz_Manager_Core $core_manager ) {
        $this->core_manager = $core_manager;
        $this->wpdb         = $core_manager->get_wpdb();

        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // AJAX handlers for saving/deleting questions and answers
        add_action( 'wp_ajax_hairy_save_question', array( $this, 'handle_question_form_submission' ) );
        add_action( 'wp_ajax_hairy_delete_question', array( $this, 'delete_question' ) );
        add_action( 'wp_ajax_hairy_get_question_edit_form', array( $this, 'ajax_get_question_edit_form' ) );
    }

    /**
     * Adds custom menu pages to the WordPress admin dashboard.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Quiz Manager', 'hairy-hair' ), // Page title
            __( 'Quiz Manager', 'hairy-hair' ), // Menu title
            'manage_options', // Capability required
            'hairy-quiz-manager', // Menu slug
            array( $this, 'render_questions_page' ), // Callback function to render the page
            'dashicons-lightbulb', // Icon URL or Dashicon class
            20 // Position in the menu
        );

        // Submenu for Questions (points to the same main page for tabbed navigation)
        add_submenu_page(
            'hairy-quiz-manager', // Parent slug
            __( 'Questions', 'hairy-hair' ), // Page title
            __( 'Questions', 'hairy-hair' ), // Menu title
            'manage_options', // Capability
            'hairy-quiz-manager', // Menu slug (same as parent for tab effect)
            array( $this, 'render_questions_page' ) // Callback
        );

        // Submenu for Quiz Results
        add_submenu_page(
            'hairy-quiz-manager', // Parent slug
            __( 'Quiz Results', 'hairy-hair' ), // Page title
            __( 'Quiz Results', 'hairy-hair' ), // Menu title
            'manage_options', // Capability
            'hairy-quiz-results', // Menu slug
            array( $this, 'render_quiz_results_page' ) // Callback
        );
    }

    /**
     * Enqueues scripts and styles for the admin area.
     */
    public function enqueue_admin_scripts() {
        // Enqueue only on our custom admin pages
        if ( isset( $_GET['page'] ) && ( $_GET['page'] === 'hairy-quiz-manager' || $_GET['page'] === 'hairy-quiz-results' ) ) {
            // Admin CSS
            wp_enqueue_style( 'hairy-admin-css', HAIRY_URL . 'css/hairy-admin.css', array(), '1.0.0' );

            // jQuery UI Datepicker CSS (for DOB field in Quiz Results)
            wp_enqueue_style( 'jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );

            // Admin JavaScript
            wp_enqueue_script( 'hairy-admin-js', HAIRY_URL . 'js/hairy-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), '1.0.0', true );

            // Localize script with AJAX URL and nonce for security
            wp_localize_script( 'hairy-admin-js', 'hairyQuizAdmin', array(
                'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                'nonce'                         => wp_create_nonce( 'hairy_quiz_admin_nonce' ),
                'delete_confirm'                => __( 'Are you sure you want to delete this item? This action cannot be undone.', 'hairy-hair' ),
                'add_answer_placeholder'        => __( 'Enter answer option', 'hairy-hair' ),
                'add_new_quiz_type_text'        => __( 'Add New Quiz Type...', 'hairy-hair' ),
                'answer_labels'                 => $this->core_manager->get_answer_labels(), // Pass allowed labels to JS
                'incomplete_answer_row_error'   => __( 'All answer options must have both a text and a label selected.', 'hairy-hair' ),
                'duplicate_label_error'         => __( 'Each answer must have a unique label (A, B, C...). Duplicate labels are not allowed.', 'hairy-hair' ),
                'invalid_label_error'           => __( 'Invalid answer label provided. Labels must be A, B, C, D, or E.', 'hairy-hair' ),
            ) );
        }
    }

    /**
     * Renders the main Questions admin page.
     */
    public function render_questions_page() {
        $message = '';
        $error   = '';
        if ( isset( $_GET['message'] ) ) {
            if ( $_GET['message'] === 'saved' ) {
                $message = __( 'Question saved successfully!', 'hairy-hair' );
            } elseif ( $_GET['message'] === 'deleted' ) {
                $message = __( 'Question deleted successfully!', 'hairy-hair' );
            } elseif ( $_GET['message'] === 'error' ) {
                $error = __( 'An error occurred. Please try again.', 'hairy-hair' );
            }
        }

        $all_questions = $this->wpdb->get_results( "SELECT * FROM {$this->core_manager->get_table_questions()} ORDER BY quiz_type ASC, question_sequence ASC, ID ASC" );
        $quiz_types    = $this->core_manager->get_quiz_types();

      
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Quiz Questions Manager', 'hairy-hair' ); ?></h1>

            <?php if ( $message ) : ?>
                <div class="notice notice-success is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
            <?php endif; ?>
            <?php if ( $error ) : ?>
                <div class="notice notice-error is-dismissible"><p><?php echo esc_html( $error ); ?></p></div>
            <?php endif; ?>

            <div id="hairy-quiz-form-container" class="hairy-quiz-form-container">
                <h2><?php esc_html_e( 'Add New Question', 'hairy-hair' ); ?></h2>
                <form id="hairy-question-form" method="post">
                    <input type="hidden" name="action" value="hairy_save_question">
                    <?php wp_nonce_field( 'hairy_quiz_admin_nonce', 'hairy_quiz_nonce' ); ?>
                    <input type="hidden" name="question_id" id="question_id" value="">

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <th scope="row"><label for="quiz_type_select"><?php esc_html_e( 'Quiz Type', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <select name="quiz_type_select" id="quiz_type_select" class="regular-text" required>
                                        <option value=""><?php esc_html_e( '-- Select Quiz Type --', 'hairy-hair' ); ?></option>
                                        <?php foreach ( $quiz_types as $type ) : ?>
                                            <option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
                                        <?php endforeach; ?>
                                        <option value="add_new"><?php esc_html_e( 'Add New Quiz Type...', 'hairy-hair' ); ?></option>
                                    </select>
                                    <input type="text" name="quiz_type_new" id="quiz_type_new" class="regular-text" placeholder="<?php esc_attr_e( 'Enter new quiz type', 'hairy-hair' ); ?>" style="display:none; margin-top: 5px;">
                                    <p class="description"><?php esc_html_e( 'Select an existing quiz type or add a new one.', 'hairy-hair' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="question_sequence"><?php esc_html_e( 'Question Sequence', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <input type="number" name="question_sequence" id="question_sequence" class="small-text" min="0" value="0" required>
                                    <p class="description"><?php esc_html_e( 'Order of the question within quiz.', 'hairy-hair' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="question_text"><?php esc_html_e( 'Question Text', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <textarea name="question_text" id="question_text" rows="4" class="large-text" placeholder="<?php esc_attr_e( 'Enter the full question text here.', 'hairy-hair' ); ?>" required></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="answer_type"><?php esc_html_e( 'Answer Type', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <select name="answer_type" id="answer_type" required>
                                            <option value="options">Options</option>
                                            <option value="multi">Multi Choice</option>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Select answer type(Text, Options).', 'hairy-hair' ); ?></p>
                                </td>
                            </tr>
                            <tr class="hairy-answer-options">
                                <th scope="row"><label for="answers_container"><?php esc_html_e( 'Answer Options', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <div id="answers_container"></div>
                                    <button type="button" id="hairy-add-answer" class="button button-primary"><?php esc_html_e( 'Add Another Answer', 'hairy-hair' ); ?></button>
                                    <p class="description"><?php esc_html_e( 'Provide all possible answer options for the question, assigning a unique label (A, B, C...) to each.', 'hairy-hair' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="question_note"><?php esc_html_e( 'Note (for Admin)', 'hairy-hair' ); ?></label></th>
                                <td>
                                    <textarea name="question_note" id="question_note" rows="2" class="large-text"></textarea>
                                    <p class="description"><?php esc_html_e( 'Internal notes for this question, not visible to users.', 'hairy-hair' ); ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Question', 'hairy-hair' ); ?>">
                        <button type="button" id="hairy-cancel-edit" class="button button-secondary" style="display:none;"><?php esc_html_e( 'Cancel Edit', 'hairy-hair' ); ?></button>
                    </p>
                </form>
            </div>

            <hr>

            <h2><?php esc_html_e( 'Existing Questions', 'hairy-hair' ); ?></h2>
            <?php if ( $all_questions ) : ?>
                <table class="wp-list-table widefat fixed striped hairy-quiz-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'ID', 'hairy-hair' ); ?></th>
                            <th><?php esc_html_e( 'Quiz Type', 'hairy-hair' ); ?></th>
                            <th><?php esc_html_e( 'Sequence', 'hairy-hair' ); ?></th>
                            <th><?php esc_html_e( 'Question', 'hairy-hair' ); ?></th>
                            <th><?php esc_html_e( 'Answers (Label: Text)', 'hairy-hair' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'hairy-hair' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $all_questions as $question ) : ?>
                            <?php
                                $answers = $this->wpdb->get_results( $this->wpdb->prepare(
                                    "SELECT answer_text, answer_label FROM {$this->core_manager->get_table_answers()} WHERE question_id = %d ORDER BY answer_label ASC",
                                    $question->ID
                                ) );
                                $answer_display_texts = array();
                                foreach ( $answers as $answer ) {
                                    $answer_display_texts[] = esc_html( $answer->answer_label ) . ': ' . esc_html( $answer->answer_text );
                                }
                            ?>
                            <tr id="question-row-<?php echo esc_attr( $question->ID ); ?>">
                                <td><?php echo esc_html( $question->ID ); ?></td>
                                <td><?php echo esc_html( $question->quiz_type ); ?></td>
                                <td><?php echo esc_html( $question->question_sequence ); ?></td>
                                <td><?php echo esc_html( $question->question ); ?></td>
                                <td><?php echo implode( '<br>', $answer_display_texts ); ?></td>
                                <td>
                                    <button type="button" class="button button-secondary hairy-edit-question" data-id="<?php echo esc_attr( $question->ID ); ?>"><?php esc_html_e( 'Edit', 'hairy-hair' ); ?></button>
                                    <button type="button" class="button button-danger hairy-delete-question" data-id="<?php echo esc_attr( $question->ID ); ?>"><?php esc_html_e( 'Delete', 'hairy-hair' ); ?></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e( 'No questions found. Start by adding a new question above!', 'hairy-hair' ); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Handles the AJAX submission for saving (adding/editing) a question.
     */
    public function handle_question_form_submission() {
        check_ajax_referer( 'hairy_quiz_admin_nonce', 'hairy_quiz_nonce' );

        $question_id        = isset( $_POST['question_id'] ) ? absint( $_POST['question_id'] ) : 0;
        $quiz_type_select   = isset( $_POST['quiz_type_select'] ) ? sanitize_text_field( $_POST['quiz_type_select'] ) : '';
        $quiz_type_new      = isset( $_POST['quiz_type_new'] ) ? sanitize_text_field( $_POST['quiz_type_new'] ) : '';
        $question_sequence  = isset( $_POST['question_sequence'] ) ? absint( $_POST['question_sequence'] ) : 0;
        $answer_type        = isset( $_POST['answer_type'] ) ? $_POST['answer_type'] : 'options';
        $question_text      = isset( $_POST['question_text'] ) ? sanitize_textarea_field( $_POST['question_text'] ) : '';
        $note               = isset( $_POST['question_note'] ) ? sanitize_textarea_field( $_POST['question_note'] ) : '';
        $answers_texts      = isset( $_POST['answers_text'] ) ? (array) $_POST['answers_text'] : array();
        $answers_labels     = isset( $_POST['answers_label'] ) ? (array) $_POST['answers_label'] : array();

        $quiz_type = '';
        if ( 'add_new' === $quiz_type_select ) {
            $quiz_type = $quiz_type_new;
        } else {
            $quiz_type = $quiz_type_select;
        }

        

        if($answer_type === 'options') {
            error_reporting(E_ALL);
            // Basic validation for main question fields
            if ( empty( $quiz_type ) || empty( $question_text ) ) {
                wp_send_json_error( array( 'message' => __( 'Quiz Type and Question Text are required.', 'hairy-hair' ) ) );
            }

            // Validate answer options: must have at least one, and labels must be unique and valid
            $sanitized_answer_data = [];
            $provided_labels = [];
            $num_answers_texts = count($answers_texts);
            $num_answers_labels = count($answers_labels); 

            if ($num_answers_texts !== $num_answers_labels) {
                wp_send_json_error( array( 'message' => __( 'Internal error: Answer text and label counts do not match.', 'hairy-hair' ) ) );
            }

            for ($i = 0; $i < $num_answers_texts; $i++) {
                $text_raw = $answers_texts[$i];
                $label_raw = $answers_labels[$i];

                $text = sanitize_text_field($text_raw);
                $label = strtoupper(sanitize_text_field($label_raw));

                if (empty($text) && empty($label)) {
                    continue; // Skip entirely empty rows
                }
                // echo "<pre>";print_r($label);echo "</pre>"; 
                // if (empty($text) || empty($label)) {
                //     wp_send_json_error( array( 'message' => hairyQuizAdmin['incomplete_answer_row_error'] ) );
                // }

                // if (!in_array($label, $this->core_manager->get_answer_labels())) {
                //     echo 'sd';
                //     wp_send_json_error( array( 'message' => hairyQuizAdmin['invalid_label_error'] ) );
                // }
                // if (in_array($label, $provided_labels)) {
                //     wp_send_json_error( array( 'message' => sprintf(hairyQuizAdmin['duplicate_label_error'], esc_html($label)) ) );
                // }
                $provided_labels[] = $label;
                $sanitized_answer_data[] = [
                    'text' => $text,
                    'label' => $label
                ];
            }
            

            if ( empty( $sanitized_answer_data ) ) {
                wp_send_json_error( array( 'message' => __( 'At least one answer option is required.', 'hairy-hair' ) ) );
            }

        } 

        $question_data = array(
            'quiz_type'         => $quiz_type,
            'question_sequence' => $question_sequence,
            'answer_type'       => $answer_type,
            'question'          => $question_text,
            'note'              => $note,
        );

        $result_message = '';

        
        if ( $question_id ) {
            // Update existing question
            $updated = $this->wpdb->update(
                $this->core_manager->get_table_questions(),
                $question_data,
                array( 'ID' => $question_id ),
                array( '%s', '%d', '%s', '%s', '%s', '%s' ),
                array( '%d' )
            );

            // If question update succeeded or no changes were made, proceed with answers
            if ( false !== $updated ) {
                $result_message = __( 'Question updated successfully.', 'hairy-hair' );

                // Delete existing answers for this question
                $this->wpdb->delete( $this->core_manager->get_table_answers(), array( 'question_id' => $question_id ), array( '%d' ) );

                // Insert new answers
                if($answer_type === 'options') {
                    foreach ( $sanitized_answer_data as $answer ) {
                        $this->wpdb->insert(
                            $this->core_manager->get_table_answers(),
                            array(
                                'question_id' => $question_id,
                                'answer_text' => $answer['text'],
                                'answer_label' => $answer['label'],
                            ),
                            array( '%d', '%s', '%s' )
                        );
                    }
                }
            } else {
                wp_send_json_error( array( 'message' => __( 'Error updating question.', 'hairy-hair' ) ) );
            }
        } else {
            // Insert new question
            $inserted = $this->wpdb->insert(
                $this->core_manager->get_table_questions(),
                $question_data,
                array( '%s', '%d', '%s', '%s', '%s', '%s' )
            );

            // echo 'que id: '. $this->wpdb->insert_id;
            // echo 'question_data: '. json_encode($question_data);

            if ( $inserted ) {
                $question_id = $this->wpdb->insert_id; // Get the ID of the newly inserted question
                $result_message = __( 'Question added successfully.', 'hairy-hair' );

                // Insert answers for the new question
                if($answer_type === 'options') {
                    foreach ( $sanitized_answer_data as $answer ) {
                        $this->wpdb->insert(
                            $this->core_manager->get_table_answers(),
                            array(
                                'question_id' => $question_id,
                                'answer_text' => $answer['text'],
                                'answer_label' => $answer['label'],
                            ),
                            array( '%d', '%s', '%s' )
                        );
                    }
                }
            } else {
                wp_send_json_error( array(
                    'message' => __( 'Database error occurred.', 'hairy-hair' ),
                    'mysql_error' => $this->wpdb->last_error
                ) );
                wp_send_json_error( array( 'message' => __( 'Error adding question.', 'hairy-hair' ) ) );
            }
        }
         

        // Update quiz types option
        $existing_quiz_types = $this->core_manager->get_quiz_types();
        if ( ! in_array( $quiz_type, $existing_quiz_types ) ) {
            $existing_quiz_types[] = $quiz_type;
            $this->core_manager->update_quiz_types( $existing_quiz_types );
        }

        wp_send_json_success( array( 'message' => $result_message ) );
    }


    /**
     * Handles the AJAX request to delete a question.
     */
    public function delete_question() {
        check_ajax_referer( 'hairy_quiz_admin_nonce', 'nonce' );

        $question_id = isset( $_POST['question_id'] ) ? absint( $_POST['question_id'] ) : 0;

        if ( ! $question_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid question ID.', 'hairy-hair' ) ) );
        }

        // Deleting from questions table will cascade delete from answers table due to FK constraint
        $deleted = $this->wpdb->delete( $this->core_manager->get_table_questions(), array( 'ID' => $question_id ), array( '%d' ) );

        if ( $deleted ) {
            wp_send_json_success( array( 'message' => __( 'Question deleted successfully.', 'hairy-hair' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Error deleting question.', 'hairy-hair' ) ) );
        }
    }

    /**
     * Handles the AJAX request to get the edit form data for a question.
     */
    public function ajax_get_question_edit_form() {
        check_ajax_referer( 'hairy_quiz_admin_nonce', 'nonce' );

        $question_id = isset( $_POST['question_id'] ) ? absint( $_POST['question_id'] ) : 0;

        if ( ! $question_id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid question ID.', 'hairy-hair' ) ) );
        }

        $question = $this->wpdb->get_row( $this->wpdb->prepare(
            "SELECT * FROM {$this->core_manager->get_table_questions()} WHERE ID = %d",
            $question_id
        ) );

        if ( ! $question ) {
            wp_send_json_error( array( 'message' => __( 'Question not found.', 'hairy-hair' ) ) );
        }

        $answers = $this->wpdb->get_results( $this->wpdb->prepare(
            "SELECT ID, answer_text, answer_label FROM {$this->core_manager->get_table_answers()} WHERE question_id = %d ORDER BY answer_label ASC", // Fetch answer_label
            $question_id
        ) );

        wp_send_json_success( array(
            'question' => $question,
            'answers'  => $answers, // Answers now include labels
        ) );
    }

    /**
     * Renders the Quiz Results admin page.
     */
    public function render_quiz_results_page() {
        
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        $total_results = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->core_manager->get_table_quiz_results()}");

        $all_results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->core_manager->get_table_quiz_results()} ORDER BY submission_date DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );
        ?>
        <?php add_thickbox(); ?>
        <div class="wrap">
            <h1><?php esc_html_e('Quiz Results', 'hairy-hair'); ?></h1>

            <?php if ($all_results): ?>
                <table class="wp-list-table widefat fixed striped hairy-quiz-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('DOB', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('Uplaoded File', 'hairy-hair'); ?></th>
                            <!-- <th><?php esc_html_e('Recommended Products IDs', 'hairy-hair'); ?></th> -->
                            <th><?php esc_html_e('Date', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('Details', 'hairy-hair'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_results as $result): ?>
                            <tr>
                                <td>
                                    <div><b><?php echo esc_html($result->first_name . ' ' . $result->last_name); ?></b> (<?php echo esc_html(ucfirst($result->gender)); ?>)</div>
                                    <?php echo esc_html($result->phone); ?> <br><?php echo esc_html($result->email); ?>
                                </td> 
                                <td>
                                    <?php
                                    $quiz_results = json_decode($result->quiz_results, true);
                                    if (!empty($quiz_results)) {
                                        foreach ($quiz_results as $q) {
                                            if($q[0]['id'] == 'File') :  ?>
                                                    <a href="<?php echo esc_url($q[1]['answer']); ?>" target="_blank">
                                                        <img style="width:50px" src="<?php echo esc_url($q[1]['answer']); ?>" alt="<?php esc_attr_e('Uploaded File', 'hairy-hair'); ?>" style="max-width: 100%; height: auto;">
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                        }
                                    } else {
                                        esc_html_e('No answers recorded.', 'hairy-hair');
                                    }
                                    ?>
                                </td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($result->dob))); ?></td> 
                                <!-- <td>
                                    <?php
                                        $recommended_products = json_decode($result->recommended_products, true);

                                        if (isset($recommended_products[0]) && is_array($recommended_products[0])) {
                                            $recommended_products = $recommended_products[0];
                                        }

                                        echo "<ul>";

                                        foreach ($recommended_products as $type => $products) {
                                            echo "<li><strong>" . htmlspecialchars($type) . "</strong><ul>";
                                            
                                            foreach ($products as $item) {
                                                echo "<li>";
                                                echo "<b>" . htmlspecialchars($item['product']) . "</b>";
                                                echo " <span>" . $item['score'] . " / 7</span>";
                                                echo "</li>";
                                                break;
                                            }

                                            echo "</ul></li>";
                                        }

                                        echo "</ul>";
                                    ?>
                                </td> -->
                                <td><?php echo esc_html(date_i18n(get_option('date_format') , strtotime($result->submission_date))); ?></td>
                                <td> 
                                    <a href="#TB_inline?width=600&height=550&inlineId=quiz-details-<?php echo esc_attr($result->ID); ?>" class="thickbox"><i class="dashicons dashicons-visibility"></i></a>
 

                                    <div class="quiz_details" id="quiz-details-<?php echo esc_attr($result->ID); ?>" style="display:none;">
                                        <?php
                                        $quiz_results = json_decode($result->quiz_results, true);
                                        if (!empty($quiz_results)) {
                                            echo '<div>';
                                            foreach ($quiz_results as $q) { 
                                                
                                                ?>
                                                <div class="quiz-answer-item">
                                                    <?php if($q[0]['id'] != 'File') : ?>
                                                    <div><strong><?php echo esc_html(($q[0]['id'])); ?></strong></div>
                                                    <span><?php echo esc_html($q[1]['answer']); ?></span>
                                                    <?php else : ?>
                                                        <a href="<?php echo esc_url($q[1]['answer']); ?>" target="_blank">
                                                            <img style="width:200px" src="<?php echo esc_url($q[1]['answer']); ?>" alt="<?php esc_attr_e('Uploaded File', 'hairy-hair'); ?>" style="max-width: 100%; height: auto;">
                                                        </a>
                                                    <?php endif; ?>
                                                    <hr>
                                                </div>
                                                <?php
                                            }
                                            echo '</div>';
                                        } else {
                                            esc_html_e('No answers recorded.', 'hairy-hair');
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table> 

                <?php 
                $total_pages = ceil($total_results / $per_page);
                if ( $total_pages > 1 ) :
                    ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php echo esc_html( $total_results ) . ' ' . esc_html__( 'items', 'hairy-hair' ); ?></span>
                            <span class="pagination-links">
                                <?php
                                // First page
                                if ( $current_page > 1 ) {
                                    echo '<a class="first-page button" href="' . esc_url( add_query_arg( 'paged', 1 ) ) . '"><span class="screen-reader-text">First page</span>«</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
                                }

                                // Previous page
                                if ( $current_page > 1 ) {
                                    echo '<a class="prev-page button" href="' . esc_url( add_query_arg( 'paged', $current_page - 1 ) ) . '"><span class="screen-reader-text">Previous page</span>‹</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
                                }

                                echo '<span class="paging-input">';
                                echo '<span class="tablenav-paging-text">';
                                echo esc_html( $current_page ) . ' ' . esc_html__( 'of', 'hairy-hair' ) . ' ' . esc_html( $total_pages );
                                echo '</span></span>';

                                // Next page
                                if ( $current_page < $total_pages ) {
                                    echo '<a class="next-page button" href="' . esc_url( add_query_arg( 'paged', $current_page + 1 ) ) . '"><span class="screen-reader-text">Next page</span>›</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
                                }

                                // Last page
                                if ( $current_page < $total_pages ) {
                                    echo '<a class="last-page button" href="' . esc_url( add_query_arg( 'paged', $total_pages ) ) . '"><span class="screen-reader-text">Last page</span>»</a>';
                                } else {
                                    echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

 
            <?php else: ?>
                <p><?php esc_html_e('No quiz results found yet.', 'hairy-hair'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }


    /**
     * Retrieves the quiz type based on question ID.
     * This might be moved to core if needed by frontend or other admin parts.
     *
     * @param int $question_id The ID of the question.
     * @return string|null The quiz type or null if not found.
     */
    public function get_quiz_type_by_question_id( $question_id ) {
        $question = $this->wpdb->get_row(
            $this->wpdb->prepare( "SELECT quiz_type FROM {$this->core_manager->get_table_questions()} WHERE ID = %d", $question_id )
        );
        return $question ? $question->quiz_type : null;
    }
}