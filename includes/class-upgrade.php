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
	}
}
