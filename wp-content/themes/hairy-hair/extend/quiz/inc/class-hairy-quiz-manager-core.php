<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Core class for managing custom quiz functionality, including DB tables and shared logic.
 */
class Hairy_Quiz_Manager_Core {

    /**
     * @var Hairy_Quiz_Manager_Core $instance The single instance of the class.
     */
    protected static $instance = null;

    /**
     * @var wpdb $wpdb WordPress database access abstraction object.
     */
    protected $wpdb;

    /**
     * @var string $table_questions Name of the questions table.
     */
    protected $table_questions;

    /**
     * @var string $table_answers Name of the answers table.
     */
    protected $table_answers;

    /**
     * @var string $table_quiz_results Name of the quiz results table.
     */
    protected $table_quiz_results;

    /**
     * @var string Option name for storing quiz types.
     */
    const QUIZ_TYPES_OPTION = 'hairy_quiz_types';

    /**
     * @var array Allowed answer labels (A, B, C, D, E).
     */
    const ANSWER_LABELS = ['A', 'B', 'C', 'D', 'E', '0', 'lead'];

    /**
     * Private constructor to prevent direct instantiation.
     * Use Hairy_Quiz_Manager_Core::get_instance() instead.
     */
    protected function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;

        // Define custom table names with WordPress prefix
        $this->table_questions    = $this->wpdb->prefix . 'hh_questions';
        $this->table_answers      = $this->wpdb->prefix . 'hh_answers';
        $this->table_quiz_results = $this->wpdb->prefix . 'hh_quiz_results';
        $this->table_lead_results = $this->wpdb->prefix . 'hh_lead_results';
    }

    /**
     * Singleton pattern to ensure only one instance of the class exists.
     *
     * @return Hairy_Quiz_Manager_Core
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Creates custom database tables if they don't exist.
     * This method is called upon theme setup (or similar custom activation point).
     */
    // public static function activate() {
    //     $instance = self::get_instance();
    //     $instance->_create_tables(); // Call the non-static method
    // }

    /**
     * Internal method to create database tables.
     * Encapsulated to be called by activate() and potentially by other setup routines.
     */
    private function create_tables() {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $charset_collate = $this->wpdb->get_charset_collate();
 
  
        $sql = [];

        $sql[] = "CREATE TABLE {$this->table_questions} (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            quiz_type VARCHAR(255) NOT NULL DEFAULT '',
            answer_type VARCHAR(255) NOT NULL DEFAULT '',
            question_sequence INT(11) UNSIGNED NOT NULL DEFAULT 0,
            question TEXT NOT NULL,
            note TEXT,
            PRIMARY KEY (ID),
            KEY quiz_type (quiz_type)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$this->table_answers} (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            question_id BIGINT(20) UNSIGNED NOT NULL,
            answer_text TEXT NOT NULL,
            answer_label VARCHAR(1) NULL DEFAULT NULL,
            PRIMARY KEY (ID),
            KEY question_id (question_id),
            KEY answer_label (answer_label)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$this->table_quiz_results} (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NULL DEFAULT NULL,
            email VARCHAR(255) UNSIGNED NULL DEFAULT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            dob DATE NOT NULL,
            gender VARCHAR(20) NOT NULL,
            quiz_answers LONGTEXT NOT NULL,
            quiz_results LONGTEXT NOT NULL,
            recommended_products LONGTEXT,
            submission_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (ID),
            KEY user_id (user_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$this->table_lead_results} (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
            first_name VARCHAR(255) NOT NULL DEFAULT '',
            last_name VARCHAR(255) NOT NULL DEFAULT '',
            email VARCHAR(255) NOT NULL DEFAULT '',
            phone VARCHAR(255) NOT NULL DEFAULT '',
            dob VARCHAR(255) NOT NULL DEFAULT '',
            gender VARCHAR(255) NOT NULL DEFAULT '',
            address_line_1 VARCHAR(255) NOT NULL DEFAULT '',
            address_line_2 VARCHAR(255) NOT NULL DEFAULT '',
            city VARCHAR(255) NOT NULL DEFAULT '',
            state VARCHAR(255) NOT NULL DEFAULT '',
            postcode VARCHAR(255) NOT NULL DEFAULT '',
            receive_communication VARCHAR(20) NOT NULL DEFAULT '',
            hair_loss_duration VARCHAR(20) NOT NULL DEFAULT '',
            expacted_results VARCHAR(20) NOT NULL DEFAULT '',
            allergies_or_sensitivities VARCHAR(20) NOT NULL DEFAULT '',
            taking_any_medications VARCHAR(20) NOT NULL DEFAULT '',
            high_blood_pressure VARCHAR(20) NOT NULL DEFAULT '',
            quiz_answers LONGTEXT NOT NULL,
            quiz_results LONGTEXT NOT NULL,
            recommended_products LONGTEXT NOT NULL,
            submission_date DATETIME NOT NULL,
            PRIMARY KEY (ID),
            KEY user_id (user_id)
        ) $charset_collate;";

        // echo "<pre>"; print_r($sql);exit;

        foreach ($sql as $query) {
            dbDelta($query);
        }

        // Insert sample questions
        $wpdb->query("INSERT INTO {$this->table_questions} (`ID`, `quiz_type`, `answer_type`, `question_sequence`, `question`, `note`) VALUES
            (6, 'Hair Treatment', 'options', 1, 'How long have you been experiencing hair loss?', 'Affects severity and type of treatment'),
            (7, 'Hair Treatment', 'options', 2, 'How would you describe your current level of hair loss?', 'Affects treatment intensity and eligibility'),
            (9, 'Hair Treatment', 'options', 3, 'What’s your goal for hair improvement?', 'Used to score treatments by goal'),
            (10, 'Hair Treatment', 'options', 4, 'Do you prefer...', 'Affects product type filtering'),
            (11, 'Hair Treatment', 'options', 5, 'How much time do you want to commit daily?', 'Rules out microneedling or complex topicals if under 2 mins'),
            (12, 'Hair Treatment', 'options', 6, 'What’s your monthly budget?', 'Filters out unaffordable options'),
            (13, 'Hair Treatment', 'options', 7, 'Do you want something non-prescription or are you open to a doctor\'s consult?', 'Filters based on product classification'),
            (14, 'Hair Treatment', 'options', 8, 'Do you experience any of the following health conditions?', 'Excludes products with contraindicated ingredients'),
            (15, 'Hair Treatment', 'options', 9, 'Are you pregnant or planning to be?', 'Excludes certain products with hormonal impact'),
            (16, 'Hair Treatment', 'options', 10, 'Are you interested in specialist or advanced treatments?', 'If Yes, direct to special treatment category with referral form'),
            (17, 'Hair Treatment', 'options', 11, 'What’s your age range?', 'Affects suitability of aggressive regrowth options'),
            (18, 'Hair Treatment', 'options', 12, 'What area(s) are you concerned about?', 'Used to match specific products')
        ");

        // Insert sample answers (only partial shown; add full list as needed)
        $wpdb->query("INSERT INTO {$this->table_answers} (`ID`, `question_id`, `answer_text`, `answer_label`) VALUES 
            (11, 6, 'Less than 6 months', 'A'),
            (12, 6, '6–12 months', 'B'),
            (13, 6, 'Over 1 year', 'C'),
            (14, 7, 'Mild thinning', 'A'),
            (15, 7, 'Moderate thinning', 'B'),
            (16, 7, 'Significant patches', 'C'),
            (17, 7, 'Advanced/balding', 'D'),
            (24, 9, 'Regrowth', 'A'),
            (25, 9, 'Prevent further loss', 'B'),
            (26, 9, 'Thicker appearance', 'C'),
            (27, 9, 'Brow/lash regrowth', 'D'),
            (28, 10, 'A once-a-day pill', 'A'),
            (29, 10, 'A topical spray or foam', 'B'),
            (30, 10, 'A gummy vitamin', 'C'),
            (31, 10, 'A shampoo or conditioner', 'D'),
            (32, 10, 'I don’t mind', 'E'),
            (33, 11, '1 minute or less', 'A'),
            (34, 11, '2–5 minutes', 'B'),
            (35, 11, 'Open to longer if results are better', 'C'),
            (36, 12, 'Under $50', 'A'),
            (37, 12, '$51–$85', 'B'),
            (38, 12, '$86–$110', 'C'),
            (39, 12, '$111+', 'D'),
            (40, 13, 'Non-prescription only', 'A'),
            (41, 13, 'I’m okay with a doctor’s consult', 'B'),
            (42, 14, 'High blood pressure', 'A'),
            (43, 14, 'Hormonal disorders', 'B'),
            (44, 14, 'Allergic to medications', 'C'),
            (45, 14, 'None of the above', 'D'),
            (49, 15, 'Yes', '0'),
            (50, 15, 'No', '0'),
            (51, 15, 'Not applicable', 'C'),
            (52, 16, 'Yes (hair transplant, PRP, microneedling etc.)', '0'),
            (53, 16, 'No, not at this stage', '0'),
            (54, 17, 'Under 20', '0'),
            (55, 17, '20–30', '0'),
            (56, 17, '31–45', '0'),
            (57, 17, '46–60', '0'),
            (58, 17, '60+', '0'),
            (59, 18, 'Scalp', '0'),
            (60, 18, 'Eyebrows', '0'),
            (61, 18, 'Eyelashes', '0'),
            (62, 18, 'Beard', '0')
        ");
        if ( false === get_option( self::QUIZ_TYPES_OPTION ) ) {
            add_option( self::QUIZ_TYPES_OPTION, json_encode( array() ) );
        }
    }

    /**
     * Retrieves the list of quiz types from wp_options.
     *
     * @return array An array of quiz type strings.
     */
    public function get_quiz_types() { 
        if (get_option('quiz_tables') === false) {
            $this->create_tables();
            add_option('quiz_tables', 1);  
        }
        
        $quiz_types_json = get_option( self::QUIZ_TYPES_OPTION );
        return json_decode( $quiz_types_json, true ) ?: array();
    }

    /**
     * Updates the list of quiz types in wp_options.
     *
     * @param array $types The array of quiz type strings to save.
     * @return bool True on success, false on failure.
     */
    public function update_quiz_types( array $types ) {
        // Ensure unique values and sort them
        $types = array_unique( array_map( 'sanitize_text_field', $types ) );
        sort( $types );
        return update_option( self::QUIZ_TYPES_OPTION, json_encode( $types ) );
    }

    /**
     * Get questions table name.
     *
     * @return string
     */
    public function get_table_questions() {
        return $this->table_questions;
    }

    /**
     * Get answers table name.
     *
     * @return string
     */
    public function get_table_answers() {
        return $this->table_answers;
    }

    /**
     * Get quiz results table name.
     *
     * @return string
     */
    public function get_table_quiz_results() {
        return $this->table_quiz_results;
    }

    /**
     * Get quiz results table name.
     *
     * @return string
     */
    public function get_table_leads_results() {
        return $this->table_lead_results;
    }

    /**
     * Get the wpdb object.
     *
     * @return wpdb
     */
    public function get_wpdb() {
        return $this->wpdb;
    }

    /**
     * Get allowed answer labels.
     *
     * @return array
     */
    public function get_answer_labels() {
        return self::ANSWER_LABELS;
    }
}