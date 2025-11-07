<?php
/**
 * Upgrade class for Log HTTP Requests plugin.
 *
 * @package Log_HTTP_Requests
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin upgrades and database migrations.
 *
 * @since 1.0.0
 */
class LHR_Upgrade {

	/**
	 * Current plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version;

	/**
	 * Last installed plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $last_version;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->version      = LHR_VERSION;
		$this->last_version = get_option( 'lhr_version' );

		if ( version_compare( $this->last_version, $this->version, '<' ) ) {
			if ( version_compare( $this->last_version, '0.1.0', '<' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				$this->clean_install();
			} else {
				$this->run_upgrade();
			}

			update_option( 'lhr_version', $this->version );
		}
	}


	/**
	 * Perform a clean installation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function clean_install() {
		global $wpdb;

		$sql = "
        CREATE TABLE IF NOT EXISTS {$wpdb->prefix}lhr_log (
            id BIGINT unsigned not null auto_increment,
            url TEXT,
            request_args MEDIUMTEXT,
            response MEDIUMTEXT,
            backtrace MEDIUMTEXT,
            runtime VARCHAR(64),
            date_added DATETIME,
            PRIMARY KEY (id)
        ) DEFAULT CHARSET=utf8mb4";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Creating table, not dynamic query.
		$wpdb->query( $sql );
	}


	/**
	 * Run upgrade tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function run_upgrade() {
		global $wpdb;

		// Add backtrace column if it doesn't exist (for upgrades from pre-1.5.0).
		if ( version_compare( $this->last_version, '1.5.0', '<' ) ) {
			$table_name  = $wpdb->prefix . 'lhr_log';
			$column_name = 'backtrace';

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$column_exists = $wpdb->get_results(
				$wpdb->prepare(
					'SHOW COLUMNS FROM %i LIKE %s',
					$table_name,
					$column_name
				)
			);

			if ( empty( $column_exists ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN backtrace MEDIUMTEXT AFTER response" );
			}
		}
	}
}
