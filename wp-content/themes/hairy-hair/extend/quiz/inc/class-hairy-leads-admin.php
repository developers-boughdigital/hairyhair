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
        add_action( 'admin_post_hh_remove_lead', array( $this, 'handle_remove_lead' ) );
        add_filter( 'removable_query_args', function ( $args ) {
            return array_merge( $args, array( 'hh_notice', 'hh_lead' ) ); // strip notice params from the address bar
        } );

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
    /**
     * One explanation row. Current = the lead's current answer is "Yes". Historical = an explanation
     * kept from an earlier submission while the current answer is not "Yes"; shown separately so it
     * is never mistaken for a current answer.
     *
     * @param string      $topic   'Allergies' or 'Medications'.
     * @param string      $answer  Current Yes/No answer.
     * @param string|null $text    Stored explanation.
     */
    private static function render_explanation_row( $topic, $answer, $text ) {
        if ( null === $text || '' === trim( (string) $text ) ) {
            return;
        }
        if ( 'Yes' === $answer ) {
            echo '<tr class="hh-explanation-current"><td><strong>' . esc_html( $topic ) . ' explained: </strong></td><td>' . nl2br( esc_html( $text ) ) . '</td></tr>';
            return;
        }
        echo '<tr class="hh-explanation-historical" style="color:#646970;">'
            . '<td><strong>' . esc_html( $topic ) . ' explanation (historical): </strong></td>'
            . '<td style="border-left:3px solid #dba617;padding-left:8px;">'
            . '<em>' . esc_html( sprintf( 'Not part of the current answers. Current answer: "%s". Kept from an earlier submission.', (string) $answer ) ) . '</em><br>'
            . '<span style="font-style:italic;">' . nl2br( esc_html( $text ) ) . '</span>'
            . '</td></tr>';
    }

    /**
     * Columns shown in their own labelled rows; every other column is listed under "Other stored data".
     */
    const SHOWN_COLUMNS = array( 'ID', 'user_id', 'first_name', 'last_name', 'email', 'phone', 'dob', 'gender', 'address_line_1', 'address_line_2', 'city', 'state', 'postcode', 'receive_communication', 'consent_to_contact', 'hair_loss_duration', 'expacted_results', 'age_group', 'current_hair_loss', 'allergies_or_sensitivities', 'explain_allergies', 'taking_any_medications', 'explain_medications', 'high_blood_pressure', 'submission_date', 'zoho_id', 'zoho_sync_status', 'zoho_sync_attempts', 'zoho_sync_error', 'zoho_sync_at' );

    /**
     * One label/value row (value escaped; empty shown as an em dash).
     *
     * @param string $label Label.
     * @param mixed  $value Value.
     * @param bool   $raw   Value is already-escaped HTML.
     */
    private static function row( $label, $value, $raw = false ) {
        $empty = null === $value || '' === trim( (string) $value );
        echo '<tr><td style="width:38%;vertical-align:top;"><strong>' . esc_html( $label ) . ': </strong></td><td style="word-break:break-word;">'
            . ( $empty ? '<span style="color:#8c8f94;">&mdash;</span>' : ( $raw ? $value : esc_html( (string) $value ) ) )
            . '</td></tr>';
    }

    /**
     * Section heading row inside the details table.
     *
     * @param string $title Title.
     */
    private static function section( $title ) {
        echo '<tr><td colspan="2" style="background:#f0f0f1;padding:6px 10px;"><strong>' . esc_html( $title ) . '</strong></td></tr>';
    }

    /**
     * WordPress account for user_id: link for real users; legacy rows stored a Zoho Lead ID there.
     *
     * @param mixed $user_id Stored user_id.
     * @return string Escaped HTML.
     */
    private static function user_cell( $user_id ) {
        $user_id = (string) $user_id;
        if ( '' === $user_id || '0' === $user_id ) {
            return esc_html__( 'Guest (not linked to an account)', 'hairy-hair' );
        }
        $user = ( strlen( $user_id ) < 12 ) ? get_userdata( (int) $user_id ) : false;
        if ( $user ) {
            return '<a href="' . esc_url( get_edit_user_link( $user->ID ) ) . '">#' . esc_html( $user_id ) . ' ' . esc_html( $user->user_login ) . '</a> (' . esc_html( $user->user_email ) . ')';
        }
        if ( strlen( $user_id ) >= 12 ) {
            return esc_html( $user_id ) . ' <em style="color:#b32d2e;">' . esc_html__( '(not a WordPress user: legacy row with a Zoho Lead ID stored in user_id)', 'hairy-hair' ) . '</em>';
        }
        return esc_html( $user_id ) . ' <em style="color:#b32d2e;">' . esc_html__( '(user no longer exists)', 'hairy-hair' ) . '</em>';
    }

    /**
     * Readable value for a stored text column; JSON is pretty-printed. Escaped.
     *
     * @param mixed $value Stored value.
     * @return string Escaped HTML.
     */
    private static function stored_value( $value ) {
        $text    = (string) $value;
        $decoded = ( '' !== $text && in_array( $text[0], array( '{', '[' ), true ) ) ? json_decode( $text, true ) : null;
        if ( null !== $decoded ) {
            $text = wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        }
        return '<pre style="white-space:pre-wrap;margin:0;font-size:12px;max-height:240px;overflow:auto;">' . esc_html( $text ) . '</pre>';
    }

    /**
     * @param array $args Extra query args.
     * @return string Leads admin page URL.
     */
    public static function page_url( array $args = array() ) {
        return add_query_arg( array_merge( array( 'page' => 'hairy-leads-results' ), $args ), admin_url( 'admin.php' ) );
    }

    /**
     * @param mixed $user_id Stored user_id.
     * @return bool A real WordPress user (legacy rows stored Zoho Lead IDs in user_id).
     */
    public static function is_real_user_id( $user_id ) {
        $user_id = (string) $user_id;
        return '' !== $user_id && '0' !== $user_id && strlen( $user_id ) < 12 && (bool) get_userdata( (int) $user_id );
    }

    /**
     * Pending Zoho sync jobs (Action Scheduler and WP-Cron fallback) for the given lead IDs.
     *
     * @param int[] $lead_ids Lead IDs.
     * @return array<int, array<int, array>> lead ID => list of [source, id|timestamp, hook, args, group]
     */
    public static function pending_sync_jobs( array $lead_ids ) {
        $hook  = class_exists( 'HH_Zoho_Lead_Sync' ) ? HH_Zoho_Lead_Sync::JOB_HOOK : 'hh_lead_zoho_sync';
        $want  = array_flip( array_map( 'intval', $lead_ids ) );
        $found = array();
        if ( ! $want ) {
            return $found;
        }
        if ( function_exists( 'as_get_scheduled_actions' ) ) {
            $actions = as_get_scheduled_actions( array( 'hook' => $hook, 'status' => 'pending', 'per_page' => -1 ) );
            foreach ( (array) $actions as $action_id => $action ) {
                $args = $action->get_args();
                $lead = isset( $args[0] ) ? (int) $args[0] : 0;
                if ( isset( $want[ $lead ] ) ) {
                    $found[ $lead ][] = array( 'as', (int) $action_id, $hook, $args, $action->get_group() );
                }
            }
        }
        foreach ( (array) _get_cron_array() as $timestamp => $hooks ) {
            foreach ( (array) ( $hooks[ $hook ] ?? array() ) as $event ) {
                $lead = isset( $event['args'][0] ) ? (int) $event['args'][0] : 0;
                if ( isset( $want[ $lead ] ) ) {
                    $found[ $lead ][] = array( 'cron', (int) $timestamp, $hook, $event['args'], '' );
                }
            }
        }
        return $found;
    }

    /**
     * admin-post.php?action=hh_remove_lead (POST only). Deletes ONE local lead row after the
     * confirmation dialog. Administrators only, per-lead nonce. Never deletes WordPress users,
     * Zoho records, quiz results or other leads; cancels this lead's pending Zoho sync jobs.
     */
    public function handle_remove_lead() {
        if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
            wp_die( esc_html__( 'Invalid request.', 'hairy-hair' ), 405 );
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to remove leads.', 'hairy-hair' ), 403 );
        }
        $lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
        check_admin_referer( 'hh_remove_lead_' . $lead_id );
        require_once __DIR__ . '/class-hairy-leads-list-table.php';

        $back = Hairy_Leads_List_Table::read_filters( $_POST ); // read_filters() unslashes and validates
        $back = array_filter( array(
            's'         => $back['s'],
            'sync'      => $back['sync'],
            'date_from' => $back['date_from'],
            'date_to'   => $back['date_to'],
            'per_page'  => 20 === $back['per_page'] ? '' : $back['per_page'],
            'paged'     => isset( $_POST['paged'] ) && absint( $_POST['paged'] ) > 1 ? absint( $_POST['paged'] ) : '',
        ), 'strlen' );
        $done = function ( $code ) use ( $back, $lead_id ) {
            wp_safe_redirect( self::page_url( array_merge( $back, array( 'hh_notice' => $code, 'hh_lead' => $lead_id ) ) ) );
            exit;
        };

        global $wpdb;
        $table = $this->core_manager->get_table_leads_results();
        $row   = $lead_id ? $wpdb->get_row( $wpdb->prepare( "SELECT ID, email FROM {$table} WHERE ID = %d", $lead_id ) ) : null;
        if ( ! $row ) {
            $done( 'not_found' );
        }

        // Do not remove a lead while a submission or Zoho sync for this email is running.
        $email = strtolower( trim( (string) $row->email ) );
        $locks = array( 'hh_lead_' . md5( $email ), 'hh_zoho_' . md5( $email ) );
        $held  = array();
        foreach ( $locks as $lock ) {
            if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', $lock ) ) ) {
                foreach ( $held as $h ) {
                    $wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $h ) );
                }
                $done( 'busy' );
            }
            $held[] = $lock;
        }

        try {
            $deleted = $wpdb->delete( $table, array( 'ID' => $lead_id ), array( '%d' ) );
            if ( 1 !== $deleted ) {
                $code = 'failed';
            } else {
                // Row is gone: cancel its pending sync jobs (a job for a missing row would be a no-op anyway).
                foreach ( self::pending_sync_jobs( array( $lead_id ) )[ $lead_id ] ?? array() as $job ) {
                    if ( 'as' === $job[0] && class_exists( 'ActionScheduler' ) ) {
                        ActionScheduler::store()->cancel_action( $job[1] );
                    } elseif ( 'as' === $job[0] && function_exists( 'as_unschedule_action' ) ) {
                        as_unschedule_action( $job[2], $job[3], $job[4] );
                    } elseif ( 'cron' === $job[0] ) {
                        wp_unschedule_event( $job[1], $job[2], $job[3] );
                    }
                }
                error_log( sprintf( 'HairyHair lead #%d removed by admin user #%d.', $lead_id, get_current_user_id() ) );
                $code = 'removed';
            }
        } finally {
            foreach ( $held as $h ) {
                $wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $h ) );
            }
        }
        $done( $code );
    }

    /**
     * Hidden details popup for one lead (opened by the View button via Thickbox).
     *
     * @param object $result Lead row.
     */
    public function render_details_popup( $result ) {
        ?>
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
                                                <td><strong>Consent to contact: </strong></td>
                                                <td><?php
                                                    $consent = isset( $result->consent_to_contact ) ? $result->consent_to_contact : null;
                                                    echo esc_html( null === $consent ? 'Not recorded (submitted before consent was a separate choice)' : ( (int) $consent ? 'Yes' : 'No' ) );
                                                ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Hair loss duration: </strong></td>
                                                <td><?php echo esc_html($result->hair_loss_duration); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Expacted results: </strong></td>
                                                <td><?php echo esc_html($result->expacted_results); ?></td>
                                            </tr>
                                            <?php self::row( 'Age group', isset( $result->age_group ) ? $result->age_group : null ); ?>
                                            <?php self::row( 'Current hair loss', isset( $result->current_hair_loss ) ? $result->current_hair_loss : null ); ?>
                                            <tr>
                                                <td><strong>Allergies or sensitivities: </strong></td>
                                                <td><?php echo esc_html($result->allergies_or_sensitivities); ?></td>
                                            </tr>
                                            <?php self::render_explanation_row( 'Allergies', $result->allergies_or_sensitivities, isset( $result->explain_allergies ) ? $result->explain_allergies : null ); ?>
                                            <tr>
                                                <td><strong>Taking any medications: </strong></td>
                                                <td><?php echo esc_html($result->taking_any_medications); ?></td>
                                            </tr>
                                            <?php self::render_explanation_row( 'Medications', $result->taking_any_medications, isset( $result->explain_medications ) ? $result->explain_medications : null ); ?>
                                            <tr>
                                                <td><strong>High blood pressure: </strong></td>
                                                <td><?php echo esc_html($result->high_blood_pressure); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Submission Date: </strong></td>
                                                <td><?php echo esc_html($result->submission_date); ?></td>
                                            </tr>
                                            <?php
                                            self::section( 'Record and Zoho CRM' );
                                            self::row( 'Lead ID', $result->ID );
                                            self::row( 'WordPress account (user_id)', self::user_cell( $result->user_id ), true );
                                            self::row( 'Zoho Lead ID (zoho_id)', isset( $result->zoho_id ) ? $result->zoho_id : null );
                                            self::row( 'Zoho sync status', property_exists( $result, 'zoho_sync_status' ) ? ( null === $result->zoho_sync_status ? 'Not tracked (saved before sync tracking)' : $result->zoho_sync_status ) : null );
                                            self::row( 'Zoho sync attempts', isset( $result->zoho_sync_attempts ) ? $result->zoho_sync_attempts : null );
                                            self::row( 'Zoho sync error / review code', isset( $result->zoho_sync_error ) ? $result->zoho_sync_error : null );
                                            self::row( 'Zoho last sync attempt (UTC)', isset( $result->zoho_sync_at ) ? $result->zoho_sync_at : null );

                                            // Every other stored column (quiz_answers, quiz_results, recommended_products, future columns).
                                            $other = array_diff_key( get_object_vars( $result ), array_flip( self::SHOWN_COLUMNS ) );
                                            if ( $other ) {
                                                self::section( 'Other stored data' );
                                                foreach ( $other as $column => $value ) {
                                                    self::row( $column, ( null === $value || '' === trim( (string) $value ) ) ? null : self::stored_value( $value ), true );
                                                }
                                            }
                                            ?>
                                        </table>
                                    </div>
        <?php
    }

    public function render_leads_results_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to view leads.', 'hairy-hair' ), 403 );
        }
        require_once __DIR__ . '/class-hairy-leads-list-table.php';
        $filters = Hairy_Leads_List_Table::read_filters( $_GET );
        $table   = new Hairy_Leads_List_Table( $this, $filters );
        $table->prepare_items();
        $paged   = $table->get_pagenum();

        $notices = array(
            'removed'   => array( 'success', __( 'Lead #%d was removed. Its pending Zoho sync jobs were cancelled; WordPress users and Zoho CRM were not changed.', 'hairy-hair' ) ),
            'not_found' => array( 'warning', __( 'Lead #%d was not found. It may already have been removed.', 'hairy-hair' ) ),
            'busy'      => array( 'warning', __( 'Lead #%d is being saved or synced to Zoho right now. Nothing was removed; please try again in a moment.', 'hairy-hair' ) ),
            'failed'    => array( 'error', __( 'Lead #%d could not be removed. Nothing was changed.', 'hairy-hair' ) ),
        );
        $notice = isset( $_GET['hh_notice'] ) ? sanitize_key( $_GET['hh_notice'] ) : '';
        add_thickbox();
        ?>
        <style>
            #TB_window #TB_ajaxContent { overflow-y: auto !important; overflow-x: hidden; box-sizing: border-box; }
            #TB_window .lead-answer-item td { vertical-align: top; }
            .hh-filters { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 8px 12px; margin: 12px 0 4px; padding: 12px; background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; }
            .hh-filters .hh-field { display: flex; flex-direction: column; gap: 4px; }
            .hh-filters label { font-weight: 600; font-size: 12px; color: #50575e; }
            .hh-filters input[type="search"] { min-width: 240px; }
            .hh-filters .hh-buttons { display: flex; gap: 6px; align-items: center; }
            .hairy-leads-table .column-id { width: 64px; }
            .hairy-leads-table .column-dob { width: 110px; }
            .hairy-leads-table .column-zoho { width: 150px; }
            .hairy-leads-table .column-date { width: 150px; }
            .hairy-leads-table .column-action { width: 190px; }
            .hh-actions { display: flex; flex-wrap: wrap; gap: 6px; }
            .hh-actions .button .dashicons { font-size: 16px; width: 16px; height: 16px; vertical-align: text-bottom; }
            .hh-actions .hh-remove { color: #b32d2e; border-color: #b32d2e; }
            .hh-actions .hh-remove:hover, .hh-actions .hh-remove:focus { color: #fff; background: #b32d2e; border-color: #b32d2e; }
            .hh-muted { color: #646970; }
            .hh-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 12px; line-height: 1.6; background: #f0f0f1; color: #3c434a; white-space: nowrap; }
            .hh-badge--synced { background: #edfaef; color: #00691e; }
            .hh-badge--retry, .hh-badge--pending, .hh-badge--create_uncertain { background: #fcf9e8; color: #8a6100; }
            .hh-badge--needs_review { background: #f0f6fc; color: #135e96; }
            .hh-badge--failed { background: #fcf0f1; color: #8a2424; }
            .hh-empty { padding: 24px 8px; font-size: 14px; }
            .hh-modal[hidden] { display: none; }
            .hh-modal { position: fixed; inset: 0; z-index: 100100; background: rgba(0,0,0,.5); display: flex; align-items: center; justify-content: center; padding: 16px; }
            .hh-modal__box { background: #fff; border-radius: 4px; max-width: 520px; width: 100%; padding: 20px 24px; box-shadow: 0 6px 24px rgba(0,0,0,.25); }
            .hh-modal__box h2 { margin-top: 0; }
            .hh-modal__box ul { list-style: disc; margin-left: 20px; }
            .hh-modal__buttons { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
            .hh-modal .button-danger { background: #b32d2e; border-color: #b32d2e; color: #fff; }
            .hh-modal .button-danger:hover, .hh-modal .button-danger:focus { background: #8a2424; border-color: #8a2424; color: #fff; }
            .hh-id-mobile { display: none; font-weight: 600; }
            @media screen and (max-width: 782px) {
                .hairy-leads-table th.column-id, .hairy-leads-table td.column-id { display: none !important; }
                .hh-id-mobile { display: inline; }
                .hh-filters input[type="search"] { min-width: 0; width: 100%; }
                .hh-filters .hh-field { flex: 1 1 140px; }
                .hairy-leads-table .column-action { width: auto; }
            }
        </style>
        <div class="wrap hh-leads-wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e( 'Leads', 'hairy-hair' ); ?></h1>
            <hr class="wp-header-end">

            <?php if ( isset( $notices[ $notice ] ) ) : ?>
                <div class="notice notice-<?php echo esc_attr( $notices[ $notice ][0] ); ?> is-dismissible"><p><?php echo esc_html( sprintf( $notices[ $notice ][1], isset( $_GET['hh_lead'] ) ? absint( $_GET['hh_lead'] ) : 0 ) ); ?></p></div>
            <?php endif; ?>

            <form method="get" class="hh-filters" role="search" aria-label="<?php esc_attr_e( 'Filter leads', 'hairy-hair' ); ?>">
                <input type="hidden" name="page" value="hairy-leads-results">
                <div class="hh-field">
                    <label for="hh-s"><?php esc_html_e( 'Search', 'hairy-hair' ); ?></label>
                    <input type="search" id="hh-s" name="s" value="<?php echo esc_attr( $filters['s'] ); ?>" placeholder="<?php esc_attr_e( 'Lead ID, name or email', 'hairy-hair' ); ?>" maxlength="100">
                </div>
                <div class="hh-field">
                    <label for="hh-sync"><?php esc_html_e( 'Zoho sync', 'hairy-hair' ); ?></label>
                    <select id="hh-sync" name="sync">
                        <option value=""><?php esc_html_e( 'All statuses', 'hairy-hair' ); ?></option>
                        <?php foreach ( Hairy_Leads_List_Table::SYNC_FILTERS as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $filters['sync'], $value ); ?>><?php echo esc_html( $label ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="hh-field">
                    <label for="hh-from"><?php esc_html_e( 'From', 'hairy-hair' ); ?></label>
                    <input type="date" id="hh-from" name="date_from" value="<?php echo esc_attr( $filters['date_from'] ); ?>">
                </div>
                <div class="hh-field">
                    <label for="hh-to"><?php esc_html_e( 'To', 'hairy-hair' ); ?></label>
                    <input type="date" id="hh-to" name="date_to" value="<?php echo esc_attr( $filters['date_to'] ); ?>">
                </div>
                <div class="hh-field">
                    <label for="hh-pp"><?php esc_html_e( 'Per page', 'hairy-hair' ); ?></label>
                    <select id="hh-pp" name="per_page">
                        <?php foreach ( Hairy_Leads_List_Table::PER_PAGE_OPTIONS as $n ) : ?>
                            <option value="<?php echo esc_attr( $n ); ?>" <?php selected( $filters['per_page'], $n ); ?>><?php echo esc_html( $n ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="hh-buttons">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Filter', 'hairy-hair' ); ?></button>
                    <?php if ( $table->filters_active() || 20 !== $filters['per_page'] ) : ?>
                        <a class="button" href="<?php echo esc_url( self::page_url() ); ?>"><?php esc_html_e( 'Clear Filters', 'hairy-hair' ); ?></a>
                    <?php endif; ?>
                </div>
            </form>

            <form method="get" class="hh-table-form">
                <input type="hidden" name="page" value="hairy-leads-results">
                <?php foreach ( array( 's', 'sync', 'date_from', 'date_to' ) as $k ) : if ( '' !== $filters[ $k ] ) : ?>
                    <input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $filters[ $k ] ); ?>">
                <?php endif; endforeach; ?>
                <input type="hidden" name="per_page" value="<?php echo esc_attr( $filters['per_page'] ); ?>">
                <?php $table->display(); ?>
            </form>

            <div id="hh-remove-modal" class="hh-modal" hidden role="dialog" aria-modal="true" aria-labelledby="hh-remove-title">
                <div class="hh-modal__box">
                    <h2 id="hh-remove-title"><?php esc_html_e( 'Remove lead', 'hairy-hair' ); ?> #<span class="hh-remove-id"></span>?</h2>
                    <p><?php esc_html_e( 'This permanently deletes this one lead record from this website. It cannot be undone.', 'hairy-hair' ); ?></p>
                    <ul class="hh-remove-facts"></ul>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="hh-remove-form">
                        <input type="hidden" name="action" value="hh_remove_lead">
                        <input type="hidden" name="lead_id" value="">
                        <input type="hidden" name="_wpnonce" value="">
                        <?php foreach ( array( 's', 'sync', 'date_from', 'date_to' ) as $k ) : ?>
                            <input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( $filters[ $k ] ); ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="per_page" value="<?php echo esc_attr( $filters['per_page'] ); ?>">
                        <input type="hidden" name="paged" value="<?php echo esc_attr( $paged ); ?>">
                        <div class="hh-modal__buttons">
                            <button type="button" class="button hh-remove-cancel"><?php esc_html_e( 'Cancel', 'hairy-hair' ); ?></button>
                            <button type="submit" class="button button-danger hh-remove-confirm"><?php esc_html_e( 'Confirm Remove', 'hairy-hair' ); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script>
            jQuery( function ( $ ) {
                // Size the details popup to the current window before Thickbox opens it (max 700 x 650).
                $( '.hairy-quiz-table' ).on( 'click', 'a.thickbox', function () {
                    var w = Math.max( 320, Math.min( 700, window.innerWidth - 60 ) );
                    var h = Math.max( 250, Math.min( 650, window.innerHeight - 120 ) );
                    this.href = this.href.replace( /width=\d+/, 'width=' + w ).replace( /height=\d+/, 'height=' + h );
                } );

                // Remove: confirmation dialog first; only "Confirm Remove" submits the POST form.
                var $modal = $( '#hh-remove-modal' ), $form = $modal.find( '.hh-remove-form' ), $opener = null;
                function closeModal() {
                    $modal.prop( 'hidden', true );
                    $form.find( '[name="lead_id"], [name="_wpnonce"]' ).val( '' );
                    if ( $opener ) { $opener.trigger( 'focus' ); }
                }
                $( '.hairy-quiz-table' ).on( 'click', '.hh-remove', function () {
                    var d = $( this ).data(), facts = [];
                    $opener = $( this );
                    facts.push( d.user > 0 ? 'The linked WordPress account #' + d.user + ' is NOT deleted.' : 'This lead is not linked to a WordPress account.' );
                    facts.push( d.zoho ? 'The Zoho CRM Lead is NOT deleted (Zoho is not contacted).' : 'This lead has no Zoho CRM Lead ID.' );
                    if ( d.others > 0 ) { facts.push( d.others + ' other submission(s) with the same email stay unchanged.' ); }
                    facts.push( d.jobs > 0 ? d.jobs + ' scheduled Zoho sync job(s) for this lead will be cancelled.' : 'No scheduled Zoho sync jobs for this lead.' );
                    $modal.find( '.hh-remove-id' ).text( d.lead );
                    $modal.find( '.hh-remove-facts' ).empty().append( $.map( facts, function ( t ) { return $( '<li>' ).text( t ); } ) );
                    $form.find( '[name="lead_id"]' ).val( d.lead );
                    $form.find( '[name="_wpnonce"]' ).val( d.nonce );
                    $modal.find( '.hh-remove-confirm' ).prop( 'disabled', false );
                    $modal.prop( 'hidden', false );
                    $modal.find( '.hh-remove-cancel' ).trigger( 'focus' );
                } );
                $modal.on( 'click', '.hh-remove-cancel', closeModal );
                $modal.on( 'click', function ( e ) { if ( e.target === this ) { closeModal(); } } );
                $( document ).on( 'keydown', function ( e ) { if ( 'Escape' === e.key && ! $modal.prop( 'hidden' ) ) { closeModal(); } } );
                $form.on( 'submit', function () { $( this ).find( '.hh-remove-confirm' ).prop( 'disabled', true ); } );
            } );
        </script>
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