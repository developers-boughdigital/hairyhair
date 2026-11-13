<?php
/**
 * Leads table for wp-admin → Leads (hairy-leads-results), built on WP_List_Table.
 *
 * Server-side filtering and pagination; every query parameter is validated and every SQL value
 * goes through $wpdb->prepare(). Newest submissions first.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Hairy_Leads_List_Table extends WP_List_Table {

	const PER_PAGE_OPTIONS = array( 20, 50, 100 );

	/** Zoho sync status filter values => labels ('untracked' = NULL, rows saved before sync tracking). */
	const SYNC_FILTERS = array(
		'synced'           => 'Synced',
		'pending'          => 'Pending',
		'retry'            => 'Retry queued',
		'create_uncertain' => 'Create outcome unknown',
		'needs_review'     => 'Needs review',
		'failed'           => 'Failed',
		'skipped'          => 'Skipped (connector off)',
		'untracked'        => 'Not tracked (older leads)',
	);

	/** @var Hairy_Leads_Admin */
	protected $admin;

	/** @var array Validated filters. */
	public $filters;

	/** @var bool Whether the table has the sync-state columns. */
	protected $has_sync;

	/** @var int|null Current page after clamping to the available pages. */
	protected $clamped_page = null;

	/**
	 * Page number from ?paged=, clamped once the total is known.
	 *
	 * @return int
	 */
	public function get_pagenum() {
		return null !== $this->clamped_page ? $this->clamped_page : max( 1, absint( $_REQUEST['paged'] ?? 0 ) );
	}

	/** @var array lead ID => [other submissions with the same email, pending sync jobs]. */
	protected $context = array();

	/**
	 * @param Hairy_Leads_Admin $admin   Admin controller (renders the details popup).
	 * @param array             $filters From Hairy_Leads_List_Table::read_filters().
	 */
	public function __construct( $admin, array $filters ) {
		parent::__construct( array( 'singular' => 'lead', 'plural' => 'leads', 'ajax' => false ) );
		$this->admin   = $admin;
		$this->filters = $filters;
	}

	/**
	 * Validated filters from the request (GET). Unknown or malformed values fall back to defaults.
	 *
	 * @param array $src Usually $_GET.
	 * @return array{s: string, sync: string, date_from: string, date_to: string, per_page: int}
	 */
	public static function read_filters( array $src ) {
		$s = isset( $src['s'] ) && is_string( $src['s'] ) ? trim( sanitize_text_field( wp_unslash( $src['s'] ) ) ) : '';
		$s = function_exists( 'mb_substr' ) ? mb_substr( $s, 0, 100 ) : substr( $s, 0, 100 );

		$sync = isset( $src['sync'] ) && is_string( $src['sync'] ) ? sanitize_key( $src['sync'] ) : '';
		if ( ! isset( self::SYNC_FILTERS[ $sync ] ) ) {
			$sync = '';
		}

		$date = function ( $key ) use ( $src ) {
			$v = isset( $src[ $key ] ) && is_string( $src[ $key ] ) ? $src[ $key ] : '';
			if ( preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $v, $m ) && checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
				return $v;
			}
			return '';
		};
		$from = $date( 'date_from' );
		$to   = $date( 'date_to' );
		if ( '' !== $from && '' !== $to && $from > $to ) {
			list( $from, $to ) = array( $to, $from );
		}

		$per_page = isset( $src['per_page'] ) ? absint( $src['per_page'] ) : 20;
		if ( ! in_array( $per_page, self::PER_PAGE_OPTIONS, true ) ) {
			$per_page = 20;
		}

		return array( 's' => $s, 'sync' => $sync, 'date_from' => $from, 'date_to' => $to, 'per_page' => $per_page );
	}

	/**
	 * @return bool Whether any filter (other than page size) is active.
	 */
	public function filters_active() {
		return '' !== $this->filters['s'] || '' !== $this->filters['sync'] || '' !== $this->filters['date_from'] || '' !== $this->filters['date_to'];
	}

	public function get_columns() {
		return array(
			'id'     => __( 'ID', 'hairy-hair' ),
			'name'   => __( 'Name', 'hairy-hair' ),
			'email'  => __( 'Email', 'hairy-hair' ),
			'dob'    => __( 'DOB', 'hairy-hair' ),
			'state'  => __( 'City / State', 'hairy-hair' ),
			'zoho'   => __( 'Zoho sync', 'hairy-hair' ),
			'date'   => __( 'Date', 'hairy-hair' ),
			'action' => __( 'Action', 'hairy-hair' ),
		);
	}

	protected function get_primary_column_name() {
		return 'name';
	}

	protected function get_table_classes() {
		return array_merge( parent::get_table_classes(), array( 'hairy-quiz-table', 'hairy-leads-table' ) );
	}

	/**
	 * WHERE clause and its prepare() arguments for the current filters.
	 *
	 * @return array{0: string, 1: array}
	 */
	protected function where() {
		global $wpdb;
		$where = array( '1=1' );
		$args  = array();
		$f     = $this->filters;

		if ( '' !== $f['s'] ) {
			$like  = '%' . $wpdb->esc_like( $f['s'] ) . '%';
			$parts = array( "CONCAT_WS(' ', first_name, last_name) LIKE %s", 'email LIKE %s' );
			$args  = array( $like, $like );
			if ( ctype_digit( $f['s'] ) ) {
				$parts[] = 'ID = %d';
				$args[]  = (int) $f['s'];
			}
			$where[] = '(' . implode( ' OR ', $parts ) . ')';
		}
		if ( '' !== $f['sync'] && $this->has_sync ) {
			if ( 'untracked' === $f['sync'] ) {
				$where[] = 'zoho_sync_status IS NULL';
			} else {
				$where[] = 'zoho_sync_status = %s';
				$args[]  = $f['sync'];
			}
		}
		if ( '' !== $f['date_from'] ) {
			$where[] = 'submission_date >= %s';
			$args[]  = $f['date_from'] . ' 00:00:00';
		}
		if ( '' !== $f['date_to'] ) {
			$where[] = 'submission_date <= %s';
			$args[]  = $f['date_to'] . ' 23:59:59';
		}
		return array( implode( ' AND ', $where ), $args );
	}

	public function prepare_items() {
		global $wpdb;
		$table          = HH_Leads_Admin_Table_Name::get();
		$this->has_sync = (bool) $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM `{$table}` LIKE %s", 'zoho_sync_status' ) );
		$per_page       = $this->filters['per_page'];

		list( $where, $args ) = $this->where();
		$count_sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where}";
		$total     = (int) ( $args ? $wpdb->get_var( $wpdb->prepare( $count_sql, $args ) ) : $wpdb->get_var( $count_sql ) );

		$pages  = max( 1, (int) ceil( $total / $per_page ) );
		$page   = min( max( 1, $this->get_pagenum() ), $pages );
		$this->clamped_page = $page; // out-of-range ?paged= shows the last page (no redirect)
		$offset = ( $page - 1 ) * $per_page;

		$this->items = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM `{$table}` WHERE {$where} ORDER BY submission_date DESC, ID DESC LIMIT %d OFFSET %d",
			array_merge( $args, array( $per_page, $offset ) )
		) );

		$this->set_pagination_args( array( 'total_items' => $total, 'per_page' => $per_page, 'total_pages' => $pages ) );
		$this->_column_headers = array( $this->get_columns(), array(), array(), $this->get_primary_column_name() );
		$this->load_context();
	}

	/**
	 * Per-row facts shown in the Remove confirmation: other submissions with the same email and
	 * pending Zoho sync jobs. Two queries for the whole page.
	 */
	protected function load_context() {
		global $wpdb;
		if ( ! $this->items ) {
			return;
		}
		$table  = HH_Leads_Admin_Table_Name::get();
		$emails = array_values( array_unique( array_map( function ( $r ) { return strtolower( trim( (string) $r->email ) ); }, $this->items ) ) );
		$counts = array();
		$place  = implode( ',', array_fill( 0, count( $emails ), '%s' ) );
		foreach ( (array) $wpdb->get_results( $wpdb->prepare( "SELECT LOWER(TRIM(email)) AS e, COUNT(*) AS c FROM `{$table}` WHERE LOWER(TRIM(email)) IN ({$place}) GROUP BY e", $emails ) ) as $row ) {
			$counts[ $row->e ] = (int) $row->c;
		}
		$jobs = Hairy_Leads_Admin::pending_sync_jobs( wp_list_pluck( $this->items, 'ID' ) );
		foreach ( $this->items as $r ) {
			$e = strtolower( trim( (string) $r->email ) );
			$this->context[ (int) $r->ID ] = array( max( 0, ( $counts[ $e ] ?? 1 ) - 1 ), count( $jobs[ (int) $r->ID ] ?? array() ) );
		}
	}

	public function no_items() {
		if ( $this->filters_active() ) {
			echo '<div class="hh-empty"><span class="dashicons dashicons-search" aria-hidden="true"></span> <strong>' . esc_html__( 'No leads match these filters.', 'hairy-hair' ) . '</strong> '
				. '<a href="' . esc_url( Hairy_Leads_Admin::page_url() ) . '">' . esc_html__( 'Clear filters', 'hairy-hair' ) . '</a></div>';
		} else {
			echo '<div class="hh-empty">' . esc_html__( 'No leads yet. Submissions from the “Let’s get started” form will appear here.', 'hairy-hair' ) . '</div>';
		}
	}

	protected function column_id( $item ) {
		return '<strong>#' . esc_html( $item->ID ) . '</strong>';
	}

	protected function column_name( $item ) {
		$gender = '' !== (string) $item->gender ? ' <span class="hh-muted">(' . esc_html( ucfirst( $item->gender ) ) . ')</span>' : '';
		return '<span class="hh-id-mobile">#' . esc_html( $item->ID ) . ' </span><strong>' . esc_html( trim( $item->first_name . ' ' . $item->last_name ) ) . '</strong>' . $gender . '<br><span class="hh-muted">' . esc_html( $item->phone ) . '</span>';
	}

	protected function column_email( $item ) {
		return esc_html( $item->email );
	}

	protected function column_dob( $item ) {
		$ts = strtotime( (string) $item->dob );
		return esc_html( $ts ? date_i18n( get_option( 'date_format' ), $ts ) : (string) $item->dob );
	}

	protected function column_state( $item ) {
		$parts = array_filter( array( trim( (string) $item->city ), trim( (string) $item->state ) ), 'strlen' );
		return $parts ? esc_html( implode( ', ', $parts ) ) : '<span class="hh-muted">&mdash;</span>';
	}

	protected function column_zoho( $item ) {
		$status = property_exists( $item, 'zoho_sync_status' ) ? $item->zoho_sync_status : null;
		$label  = null === $status ? 'Not tracked' : ( self::SYNC_FILTERS[ $status ] ?? $status );
		$class  = null === $status ? 'untracked' : sanitize_html_class( $status );
		return '<span class="hh-badge hh-badge--' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
	}

	protected function column_date( $item ) {
		$ts = strtotime( (string) $item->submission_date );
		return $ts ? esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $ts ) ) : '';
	}

	protected function column_action( $item ) {
		$id  = (int) $item->ID;
		$ctx = $this->context[ $id ] ?? array( 0, 0 );
		ob_start();
		?>
		<div class="hh-actions">
			<a href="#TB_inline?width=700&height=650&inlineId=lead-details-<?php echo esc_attr( $id ); ?>" class="thickbox button button-small hh-view" title="<?php echo esc_attr( sprintf( __( 'View details of lead #%d', 'hairy-hair' ), $id ) ); ?>">
				<span class="dashicons dashicons-visibility" aria-hidden="true"></span> <?php esc_html_e( 'View', 'hairy-hair' ); ?>
			</a>
			<button type="button" class="button button-small hh-remove"
				data-lead="<?php echo esc_attr( $id ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'hh_remove_lead_' . $id ) ); ?>"
				data-user="<?php echo esc_attr( Hairy_Leads_Admin::is_real_user_id( $item->user_id ) ? (int) $item->user_id : 0 ); ?>"
				data-zoho="<?php echo esc_attr( ( property_exists( $item, 'zoho_id' ) && '' !== trim( (string) $item->zoho_id ) ) ? '1' : '0' ); ?>"
				data-others="<?php echo esc_attr( $ctx[0] ); ?>"
				data-jobs="<?php echo esc_attr( $ctx[1] ); ?>"
				title="<?php echo esc_attr( sprintf( __( 'Remove lead #%d', 'hairy-hair' ), $id ) ); ?>">
				<span class="dashicons dashicons-trash" aria-hidden="true"></span> <?php esc_html_e( 'Remove', 'hairy-hair' ); ?>
			</button>
		</div>
		<?php
		$this->admin->render_details_popup( $item );
		return ob_get_clean();
	}

	protected function column_default( $item, $column_name ) {
		return isset( $item->$column_name ) ? esc_html( (string) $item->$column_name ) : '';
	}
}

/**
 * Table name helper shared by the list table and the remove handler.
 */
class HH_Leads_Admin_Table_Name {
	public static function get() {
		global $wpdb;
		return $wpdb->prefix . 'hh_lead_results';
	}
}
