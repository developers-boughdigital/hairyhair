<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Handles all admin-side functionalities for the quiz manager.
 */
class Hairy_Leads_Admin {

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

    }

    /**
     * Adds custom menu pages to the WordPress admin dashboard.
     */
    public function add_admin_menu() {
        add_submenu_page(
            'hairy-quiz-manager', // Parent slug
            __( 'Leads', 'hairy-hair' ), // Page title
            __( 'Leads', 'hairy-hair' ), // Menu title
            'manage_options', // Capability
            'hairy-leads-results', // Menu slug
            array( $this, 'render_leads_results_page' ) // Callback
        );
    }

    private function getGenderString(int $number): string
    {
        switch ($number) {
            case 1:
                return "Male";
            case 2:
                return "Female";
        }
    }
 

    /**
     * Renders the Quiz Results admin page.
     */
    public function render_leads_results_page() {
        
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        $total_results = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->core_manager->get_table_leads_results()}");

        $all_results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->core_manager->get_table_leads_results()} ORDER BY submission_date DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            )
        );
        ?> 
        <?php add_thickbox(); ?>
        <div class="wrap">
            <h1><?php esc_html_e('Leads', 'hairy-hair'); ?></h1>

            <?php if ($all_results): ?>
                <table class="wp-list-table widefat fixed striped hairy-quiz-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Name', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('DOB', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('City', 'hairy-hair'); ?></th> 
                            <th><?php esc_html_e('State', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('Date', 'hairy-hair'); ?></th>
                            <th><?php esc_html_e('Details', 'hairy-hair'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_results as $result): ?>
                            <tr>
                                <td>
                                    <div><b><?php echo esc_html($result->first_name); ?> <?php echo esc_html($result->last_name); ?></b> (<?php echo esc_html(ucfirst($result->gender)); ?>)</div>
                                    <?php echo esc_html($result->phone); ?>
                                </td> 
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($result->dob))); ?></td> 
                                <td><?php echo esc_html($result->city); ?></td> 
                                <td><?php echo esc_html($result->state); ?></td> 
                                
                                <td><?php echo esc_html(date_i18n(get_option('date_format') , strtotime($result->submission_date))); ?></td> 
                                <td> 
                                    <a href="#TB_inline?width=600&height=550&inlineId=lead-details-<?php echo esc_attr($result->ID); ?>" class="thickbox"><i class="dashicons dashicons-visibility"></i></a>
 

                                    <div class="lead_details" id="lead-details-<?php echo esc_attr($result->ID); ?>" style="display:none;">
                                        <table class="lead-answer-item" cellspacing="0" cellpadding="10" width="100%">
                                            <tr>
                                                <td><strong>Name: </strong></td>
                                                <td><?php echo esc_html($result->first_name); ?> <?php echo esc_html($result->last_name); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email: </strong></td>
                                                <td><?php echo esc_html($result->email); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Phone: </strong></td>
                                                <td><?php echo esc_html($result->phone); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>DOB: </strong></td>
                                                <td><?php echo esc_html($result->dob); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gender: </strong></td>
                                                <td><?php echo esc_html(ucfirst($result->gender)); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Address Line 1: </strong></td>
                                                <td><?php echo esc_html($result->address_line_1); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Address Line 2: </strong></td>
                                                <td><?php echo esc_html($result->address_line_2); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>City: </strong></td>
                                                <td><?php echo esc_html($result->city); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>State: </strong></td>
                                                <td><?php echo esc_html($result->state); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Postcode: </strong></td>
                                                <td><?php echo esc_html($result->postcode); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Receive Communication: </strong></td>
                                                <td><?php echo esc_html($result->receive_communication); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hair loss duration: </strong></td>
                                                <td><?php echo esc_html($result->hair_loss_duration); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Expacted results: </strong></td>
                                                <td><?php echo esc_html($result->expacted_results); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Allergies or sensitivities: </strong></td>
                                                <td><?php echo esc_html($result->allergies_or_sensitivities); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Taking any medications: </strong></td>
                                                <td><?php echo esc_html($result->taking_any_medications); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>High blood pressure: </strong></td>
                                                <td><?php echo esc_html($result->high_blood_pressure); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Submission Date: </strong></td>
                                                <td><?php echo esc_html($result->submission_date); ?></td>
                                            </tr>
                                            <tr>
                                        </table>
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