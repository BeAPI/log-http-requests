<?php
/**
 * Main plugin class.
 *
 * @package    Log_HTTP_Requests
 * @since      1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class that handles logging of HTTP requests.
 *
 * @since 1.0.0
 */
class Log_HTTP_Requests {
	/**
	 * The query object.
	 *
	 * @since 1.0.0
	 * @var LHR_Query
	 */
	public $query;

	/**
	 * The start time for request timing.
	 *
	 * @since 1.0.0
	 * @var float
	 */
	public $start_time;

	/**
	 * The plugin instance.
	 *
	 * @since 1.0.0
	 * @var Log_HTTP_Requests
	 */
	public static $instance;


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Setup variables.
		define( 'LHR_VERSION', '1.5.0' );
		define( 'LHR_DIR', dirname( __DIR__ ) );
		define( 'LHR_URL', plugins_url( '', __DIR__ ) );
		define( 'LHR_BASENAME', plugin_basename( __DIR__ . '/log-http-requests.php' ) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_filter( 'http_request_args', array( $this, 'start_timer' ) );
		add_action( 'http_api_debug', array( $this, 'capture_request' ), 10, 5 );
		add_action( 'lhr_cleanup_cron', array( $this, 'cleanup' ) );
		add_action( 'wp_ajax_lhr_query', array( $this, 'lhr_query' ) );
		add_action( 'wp_ajax_lhr_clear', array( $this, 'lhr_clear' ) );
	}


	/**
	 * Get the singleton instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Log_HTTP_Requests The plugin instance.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		include LHR_DIR . '/includes/class-upgrade.php';
		include LHR_DIR . '/includes/class-query.php';

		new LHR_Upgrade();
		$this->query = new LHR_Query();

		if ( ! wp_next_scheduled( 'lhr_cleanup_cron' ) ) {
			wp_schedule_single_event( time() + 86400, 'lhr_cleanup_cron' );
		}
	}


	/**
	 * Clean up old log entries.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cleanup() {
		global $wpdb;

		$now     = current_time( 'timestamp' );
		$expires = apply_filters( 'lhr_expiration_days', 1 );
		$expires = gmdate( 'Y-m-d H:i:s', strtotime( '-' . $expires . ' days', $now ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DELETE FROM {$wpdb->prefix}lhr_log WHERE date_added < '$expires'" );
	}


	/**
	 * Register admin menu.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_management_page( 'Log HTTP Requests', 'Log HTTP Requests', 'manage_options', 'log-http-requests', array( $this, 'settings_page' ) );
	}


	/**
	 * Display the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function settings_page() {
		include LHR_DIR . '/templates/page-settings.php';
	}


	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function admin_scripts( $hook ) {
		if ( 'tools_page_log-http-requests' === $hook ) {
			wp_enqueue_script( 'lhr', LHR_URL . '/assets/js/admin.js', array( 'jquery' ), LHR_VERSION, true );
			wp_enqueue_style( 'lhr', LHR_URL . '/assets/css/admin.css', array(), LHR_VERSION );
			wp_enqueue_style( 'media-views' );
		}
	}


	/**
	 * Validate AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function validate() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		check_ajax_referer( 'lhr_nonce' );
	}


	/**
	 * Handle query AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function lhr_query() {
		$this->validate();

		$data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce verified in validate(). Data sanitized by get_results().

		$output = array(
			'rows'  => LHR()->query->get_results( $data ),
			'pager' => LHR()->query->paginate(),
		);

		wp_send_json( $output );
	}


	/**
	 * Handle clear AJAX request.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function lhr_clear() {
		$this->validate();

		LHR()->query->truncate_table();
	}


	/**
	 * Start timer for request timing.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args HTTP request arguments.
	 * @return array Unmodified HTTP request arguments.
	 */
	public function start_timer( $args ) {
		$this->start_time = microtime( true );
		return $args;
	}


	/**
	 * Capture HTTP request data and log it.
	 *
	 * @since 1.0.0
	 *
	 * @param array|WP_Error $response    The HTTP response or WP_Error on failure.
	 * @param string         $context     Context under which the hook is fired.
	 * @param string         $transport   The HTTP transport used.
	 * @param array          $args        HTTP request arguments.
	 * @param string         $url         The request URL.
	 * @return void
	 */
	public function capture_request( $response, $context, $transport, $args, $url ) {
		global $wpdb;

		if ( false !== strpos( $url, 'doing_wp_cron' ) ) {
			return;
		}

		// Capture backtrace to identify the source of the request.
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary -- Used for logging purposes, not debugging.
		$backtrace_array = wp_debug_backtrace_summary( null, 0, false );
		$backtrace       = is_array( $backtrace_array ) ? implode( "\n", $backtrace_array ) : $backtrace_array;

		// False to ignore current row.
		$log_data = apply_filters(
			'lhr_log_data',
			array(
				'url'          => $url,
				'request_args' => wp_json_encode( $args ),
				'response'     => wp_json_encode( $response ),
				'backtrace'    => $backtrace,
				'runtime'      => ( microtime( true ) - $this->start_time ),
				'date_added'   => current_time( 'mysql' ),
			)
		);

		if ( false !== $log_data ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->insert( $wpdb->prefix . 'lhr_log', $log_data );
		}
	}


	/**
	 * Get human-readable time since a given time.
	 *
	 * @since 1.0.0
	 *
	 * @param string $time The time to calculate from.
	 * @return string Human-readable time difference.
	 */
	public function time_since( $time ) {
		$time   = current_time( 'timestamp' ) - strtotime( $time );
		$time   = ( $time < 1 ) ? 1 : $time;
		$tokens = array(
			31536000 => 'year',
			2592000  => 'month',
			604800   => 'week',
			86400    => 'day',
			3600     => 'hour',
			60       => 'minute',
			1        => 'second',
		);

		foreach ( $tokens as $unit => $text ) {
			if ( $time < $unit ) {
				continue;
			}
			$number_of_units = floor( $time / $unit );
			return $number_of_units . ' ' . $text . ( ( $number_of_units > 1 ) ? 's' : '' );
		}
	}
}
