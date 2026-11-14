<?php
/**
 * Versioned schema migrations for {$wpdb->prefix}hh_lead_results.
 *
 * Runs on every request (after_setup_theme), so uploading new theme files is enough: no theme
 * switch or reactivation is needed. When the stored version is current the cost is one
 * autoloaded get_option().
 *
 * Safety rules:
 * - Existing tables are only ever changed with ALTER TABLE ... ADD COLUMN for columns that are
 *   missing. Nothing is dropped, truncated, recreated, renamed or retyped, and no row data is
 *   written. dbDelta() is only used to create the table when it does not exist (fresh install).
 * - The version option is bumped only after every required column has been re-read from the
 *   database and found.
 * - A MySQL named lock stops two requests migrating at once; a failure backs off for a few
 *   minutes instead of retrying on every request.
 * - Log lines contain table/column names and the database error only, never row data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hairy_Quiz_DB_Migrations {

	/** Schema version this code expects. Absent option = legacy, unversioned table (1). */
	const DB_VERSION = 5;

	const OPTION      = 'hairy_quiz_lead_db_version';
	const RETRY_KEY   = 'hairy_quiz_lead_db_migration_retry';
	const RETRY_DELAY = 300; // Seconds to wait after a failed attempt.
	const LOCK_NAME   = 'hairy_quiz_lead_db_migration';

	/**
	 * Versioned steps: version => method. Each step must be idempotent.
	 *
	 * @var array<int,string>
	 */
	protected static $steps = array(
		2 => 'step_2_add_missing_lead_columns',
		3 => 'step_3_add_sync_state_columns',
		4 => 'step_4_add_step2_step3_columns',
		5 => 'step_5_add_lead_status',
	);

	/**
	 * @return string Lead table name.
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'hh_lead_results';
	}

	/**
	 * Every column the theme and the hairyhair-registration mu-plugin read or write, with the
	 * definition used by CREATE TABLE. Types match the live table (the medical/consent answers are
	 * VARCHAR(200) there; the old VARCHAR(20) in the theme would have truncated answers).
	 *
	 * 'add' overrides the definition when the column has to be added to an existing table with
	 * rows, where NOT NULL without a usable default would be unsafe.
	 *
	 * @return array<string,array{create:string,add?:string}>
	 */
	public static function lead_columns() {
		$text255 = array( 'create' => "VARCHAR(255) NOT NULL DEFAULT ''" );
		$text200 = array( 'create' => "VARCHAR(200) NOT NULL DEFAULT ''" );
		$long    = array( 'create' => 'LONGTEXT NOT NULL', 'add' => 'LONGTEXT NULL' );

		return array(
			'user_id'                    => array( 'create' => 'BIGINT(20) UNSIGNED NOT NULL DEFAULT 0' ),
			'first_name'                 => $text255,
			'last_name'                  => $text255,
			'email'                      => $text255,
			'phone'                      => $text255,
			'dob'                        => $text255,
			'gender'                     => $text255,
			'address_line_1'             => $text255,
			'address_line_2'             => $text255,
			'city'                       => $text255,
			'state'                      => $text255,
			'postcode'                   => $text255,
			'receive_communication'      => $text200,
			'hair_loss_duration'         => $text200,
			'expacted_results'           => $text200,
			'allergies_or_sensitivities' => $text200,
			'taking_any_medications'     => $text200,
			'high_blood_pressure'        => $text200,
			'quiz_answers'               => $long,
			'quiz_results'               => $long,
			'recommended_products'       => $long,
			'submission_date'            => array( 'create' => 'DATETIME NOT NULL', 'add' => 'DATETIME NULL DEFAULT NULL' ),
			'zoho_id'                    => array( 'create' => 'VARCHAR(50) NULL DEFAULT NULL' ), // Zoho CRM Lead ID (v2).
			// v3: Zoho sync state per submission (retry, review, manual reprocessing). NULL = not tracked (legacy row).
			'zoho_sync_status'           => array( 'create' => 'VARCHAR(32) NULL DEFAULT NULL' ),
			'zoho_sync_attempts'         => array( 'create' => 'SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0' ),
			'zoho_sync_error'            => array( 'create' => 'VARCHAR(64) NULL DEFAULT NULL' ), // Error/review code only, never payloads.
			'zoho_sync_at'               => array( 'create' => 'DATETIME NULL DEFAULT NULL' ),
			// v4: lead form Step 2 / Step 3 fields (definitions as in the 2026-11-12 implementation).
			'age_group'                  => array( 'create' => "VARCHAR(50) NOT NULL DEFAULT ''" ),
			'current_hair_loss'          => array( 'create' => "VARCHAR(100) NOT NULL DEFAULT ''" ),
			'explain_allergies'          => array( 'create' => 'TEXT NULL DEFAULT NULL' ),   // NULL = not given / answer No.
			'explain_medications'        => array( 'create' => 'TEXT NULL DEFAULT NULL' ),
			'consent_to_contact'         => array( 'create' => 'TINYINT(1) NULL DEFAULT NULL' ), // NULL = recorded before consent was a real choice.
			// v5: quiz progress (logical name Lead_Status): Pending = Step 1 saved, Completed = Step 3 saved.
			// Added with no default so existing rows are not marked Pending; step 5 back-fills them, then sets the default.
			'lead_status'                => array( 'create' => "VARCHAR(20) NULL DEFAULT 'Pending'", 'add' => 'VARCHAR(20) NULL DEFAULT NULL' ),
		);
	}

	/**
	 * CREATE TABLE statement for fresh installs (dbDelta format: two spaces before PRIMARY KEY).
	 *
	 * @return string
	 */
	public static function lead_table_sql() {
		global $wpdb;
		$lines = array( 'ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT' );
		foreach ( self::lead_columns() as $name => $def ) {
			$lines[] = $name . ' ' . $def['create'];
		}
		$lines[] = 'PRIMARY KEY  (ID)';
		$lines[] = 'KEY user_id (user_id)';
		$lines[] = 'KEY zoho_sync_status (zoho_sync_status)';
		$lines[] = 'KEY email (email)';
		$lines[] = 'KEY lead_status (lead_status)';
		$lines[] = 'KEY submission_date (submission_date)';

		return 'CREATE TABLE ' . self::table() . " (\n" . implode( ",\n", $lines ) . "\n) " . $wpdb->get_charset_collate() . ';';
	}

	/**
	 * @return int Installed schema version (1 = legacy table without a version option).
	 */
	public static function installed_version() {
		$version = get_option( self::OPTION, false );
		return false === $version ? 1 : (int) $version;
	}

	/**
	 * @return bool Whether the installed schema is current (safe to write v2 columns such as zoho_id).
	 */
	public static function is_current() {
		return self::installed_version() >= self::DB_VERSION;
	}

	/**
	 * Hooked on after_setup_theme. Cheap no-op when the schema is current.
	 */
	public static function maybe_migrate() {
		if ( self::is_current() || get_transient( self::RETRY_KEY ) ) {
			return;
		}
		self::migrate();
	}

	/**
	 * Runs pending steps under a lock.
	 *
	 * @return bool True when the schema is current afterwards.
	 */
	public static function migrate() {
		global $wpdb;

		// Another request is migrating: let it finish; this request carries on unchanged.
		if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', self::LOCK_NAME ) ) ) {
			return false;
		}

		try {
			if ( ! self::table_exists() ) {
				return self::create_table();
			}

			$installed = self::installed_version();
			foreach ( self::$steps as $version => $method ) {
				if ( $version <= $installed ) {
					continue;
				}
				if ( ! call_user_func( array( __CLASS__, $method ) ) ) {
					set_transient( self::RETRY_KEY, 1, self::RETRY_DELAY );
					return false;
				}
				update_option( self::OPTION, (string) $version, true );
				self::log( sprintf( 'schema migrated to version %d.', $version ) );
			}
			return true;
		} finally {
			$wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', self::LOCK_NAME ) );
		}
	}

	/**
	 * v2: adds any lead column that is missing (on the live site: zoho_id). Never alters or
	 * removes existing columns and never touches row values (new columns get their default).
	 *
	 * @return bool True once every required column is confirmed present.
	 */
	protected static function step_2_add_missing_lead_columns() {
		global $wpdb;
		$table = self::table();

		foreach ( self::lead_columns() as $name => $def ) {
			if ( self::column_exists( $name ) ) {
				continue;
			}
			$sql = sprintf( 'ALTER TABLE `%s` ADD COLUMN `%s` %s', $table, $name, isset( $def['add'] ) ? $def['add'] : $def['create'] );
			if ( false === $wpdb->query( $sql ) ) {
				self::log( sprintf( 'adding column %s failed: %s', $name, $wpdb->last_error ) );
				// Fall through: the verification below reports every column still missing.
			} else {
				self::log( sprintf( 'added column %s.', $name ) );
			}
		}

		$missing = self::missing_columns();
		if ( $missing ) {
			self::log( 'migration to version 2 not completed; missing column(s): ' . implode( ', ', $missing ) );
			return false;
		}
		return true;
	}

	/**
	 * v3: Zoho sync-state columns (added by the same add-missing-columns routine as v2) and an
	 * index on zoho_sync_status for retry/review listings. Additive only; existing rows get
	 * NULL status (= untracked legacy row) and 0 attempts.
	 *
	 * @return bool True once columns and index are confirmed present.
	 */
	protected static function step_3_add_sync_state_columns() {
		global $wpdb;
		if ( ! self::step_2_add_missing_lead_columns() ) {
			return false;
		}
		$table = self::table();
		if ( ! self::index_exists( 'zoho_sync_status' ) ) {
			if ( false === $wpdb->query( "ALTER TABLE `{$table}` ADD INDEX `zoho_sync_status` (`zoho_sync_status`)" ) ) {
				self::log( 'adding index zoho_sync_status failed: ' . $wpdb->last_error );
			} else {
				self::log( 'added index zoho_sync_status.' );
			}
		}
		if ( ! self::index_exists( 'zoho_sync_status' ) ) {
			self::log( 'migration to version 3 not completed; missing index zoho_sync_status' );
			return false;
		}
		return true;
	}

	/**
	 * v4: Step 2 (age_group, current_hair_loss) and Step 3 (explain_allergies, explain_medications,
	 * consent_to_contact) columns, via the same add-missing-columns routine. Additive only; existing
	 * rows get '' / NULL, so older leads are never treated as having consented.
	 *
	 * @return bool
	 */
	protected static function step_4_add_step2_step3_columns() {
		return self::step_2_add_missing_lead_columns();
	}

	/**
	 * v5: lead_status column (quiz progress), back-fill of historical rows, default 'Pending' for new
	 * rows, and indexes for email lookup, status filtering and date ordering.
	 *
	 * Back-fill rule (only rows whose lead_status is still NULL; nothing else is changed): before
	 * this version a lead row was only written when the whole questionnaire was submitted, with the
	 * five original Step 2/3 answers required. Rows that have all five are therefore 'Completed';
	 * any row missing one stays NULL (shown as "Unknown" for admin review). No unique email index is
	 * added: existing duplicates have not been resolved.
	 *
	 * @return bool
	 */
	protected static function step_5_add_lead_status() {
		global $wpdb;
		if ( ! self::step_2_add_missing_lead_columns() ) {
			return false;
		}
		$table = self::table();

		$filled = $wpdb->query( "UPDATE `{$table}` SET lead_status = 'Completed' WHERE lead_status IS NULL AND hair_loss_duration <> '' AND expacted_results <> '' AND allergies_or_sensitivities <> '' AND taking_any_medications <> '' AND high_blood_pressure <> ''" );
		if ( false === $filled ) {
			self::log( 'lead_status back-fill failed: ' . $wpdb->last_error );
			return false;
		}
		self::log( sprintf( 'lead_status back-filled as Completed for %d historical row(s).', (int) $filled ) );

		$default = $wpdb->get_row( $wpdb->prepare( 'SHOW COLUMNS FROM `' . $table . '` LIKE %s', 'lead_status' ) );
		if ( $default && 'Pending' !== $default->Default ) {
			if ( false === $wpdb->query( "ALTER TABLE `{$table}` ALTER COLUMN `lead_status` SET DEFAULT 'Pending'" ) ) {
				self::log( 'setting lead_status default failed: ' . $wpdb->last_error );
				return false;
			}
		}

		foreach ( array( 'email', 'lead_status', 'submission_date' ) as $index ) {
			if ( ! self::index_exists( $index ) && false === $wpdb->query( "ALTER TABLE `{$table}` ADD INDEX `{$index}` (`{$index}`)" ) ) {
				self::log( sprintf( 'adding index %s failed: %s', $index, $wpdb->last_error ) );
			}
		}
		foreach ( array( 'email', 'lead_status', 'submission_date' ) as $index ) {
			if ( ! self::index_exists( $index ) ) {
				self::log( 'migration to version 5 not completed; missing index ' . $index );
				return false;
			}
		}
		return true;
	}

	/**
	 * @param string $name Index name.
	 * @return bool
	 */
	public static function index_exists( $name ) {
		global $wpdb;
		return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW INDEX FROM `' . self::table() . '` WHERE Key_name = %s', $name ) );
	}

	/**
	 * Fresh install: creates the table with the full current schema.
	 *
	 * @return bool
	 */
	protected static function create_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( self::lead_table_sql() );

		if ( ! self::table_exists() || self::missing_columns() ) {
			self::log( 'creating table failed: ' . $wpdb->last_error );
			set_transient( self::RETRY_KEY, 1, self::RETRY_DELAY );
			return false;
		}
		update_option( self::OPTION, (string) self::DB_VERSION, true );
		self::log( sprintf( 'table created at schema version %d.', self::DB_VERSION ) );
		return true;
	}

	/**
	 * @return string[] Required columns not present in the live table (read from the database).
	 */
	public static function missing_columns() {
		global $wpdb;
		$existing = $wpdb->get_col( 'SHOW COLUMNS FROM `' . self::table() . '`', 0 );
		$existing = array_map( 'strtolower', (array) $existing );
		return array_values( array_diff( array_keys( self::lead_columns() ), $existing ) );
	}

	/**
	 * @param string $name Column name.
	 * @return bool
	 */
	public static function column_exists( $name ) {
		global $wpdb;
		return (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW COLUMNS FROM `' . self::table() . '` LIKE %s', $name ) );
	}

	/**
	 * @return bool
	 */
	public static function table_exists() {
		global $wpdb;
		$table = self::table();
		return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
	}

	/**
	 * @param string $message Schema-only message (no personal data).
	 */
	protected static function log( $message ) {
		error_log( 'HairyHair quiz DB (' . self::table() . '): ' . $message );
	}
}
