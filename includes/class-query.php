<?php
/**
 * Query class for Log HTTP Requests plugin.
 *
 * @package Log_HTTP_Requests
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handles database queries for HTTP request logs.
 *
 * @since 1.0.0
 */
class LHR_Query {

	/**
	 * WordPress database object.
	 *
	 * @since 1.0.0
	 * @var wpdb
	 */
	public $wpdb;

	/**
	 * The SQL query.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $sql;

	/**
	 * Pagination arguments.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $pager_args;


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->wpdb = $GLOBALS['wpdb'];
	}


	/**
	 * Get results from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Query arguments.
	 * @return array Array of results.
	 */
	public function get_results( $args ) {
		$defaults = [
			'page'     => 1,
			'per_page' => 50,
			'orderby'  => 'date_added',
			'order'    => 'DESC',
			'search'   => '',
		];

		$args = array_merge( $defaults, $args );

		$output   = [];
		$orderby  = in_array( $args['orderby'], [ 'url', 'runtime', 'date_added' ], true ) ? $args['orderby'] : 'date_added';
		$order    = in_array( $args['order'], [ 'ASC', 'DESC' ], true ) ? $args['order'] : 'DESC';
		$page     = (int) $args['page'];
		$per_page = (int) $args['per_page'];
		$limit    = ( ( $page - 1 ) * $per_page ) . ',' . $per_page;

		$this->sql = "
            SELECT
                SQL_CALC_FOUND_ROWS
                id, url, request_args, response, runtime, date_added
            FROM {$this->wpdb->prefix}lhr_log
            ORDER BY $orderby $order, id DESC
            LIMIT $limit
        ";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- Dynamic ORDER BY and LIMIT, validated inputs.
		$results = $this->wpdb->get_results( $this->sql, ARRAY_A );

		$total_rows  = (int) $this->wpdb->get_var( 'SELECT FOUND_ROWS()' );
		$total_pages = ceil( $total_rows / $per_page );

		$this->pager_args = [
			'page'        => $page,
			'per_page'    => $per_page,
			'total_rows'  => $total_rows,
			'total_pages' => $total_pages,
		];

		foreach ( $results as $row ) {
			$row['status_code'] = '-';
			$response           = json_decode( $row['response'], true );
			if ( ! empty( $response['response']['code'] ) ) {
				$row['status_code'] = (int) $response['response']['code'];
			}
			$row['runtime']    = round( $row['runtime'], 4 );
			$row['date_raw']   = $row['date_added'];
			$row['date_added'] = LHR()->time_since( $row['date_added'] );
			$row['url']        = esc_url( $row['url'] );
			$output[]          = $row;
		}

		return $output;
	}


	/**
	 * Truncate the log table.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function truncate_table() {
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->wpdb->query( "TRUNCATE TABLE {$this->wpdb->prefix}lhr_log" );
	}


	/**
	 * Generate pagination HTML.
	 *
	 * @since 1.0.0
	 *
	 * @return string Pagination HTML.
	 */
	public function paginate() {
		$params = $this->pager_args;

		$output      = '';
		$page        = (int) $params['page'];
		$per_page    = (int) $params['per_page'];
		$total_rows  = (int) $params['total_rows'];
		$total_pages = (int) $params['total_pages'];

		// Only show pagination when > 1 page.
		if ( 1 < $total_pages ) {

			if ( 3 < $page ) {
				$output .= '<a class="lhr-page first-page" data-page="1">&lt;&lt;</a>';
			}
			if ( 1 < ( $page - 10 ) ) {
				$output .= '<a class="lhr-page" data-page="' . ( $page - 10 ) . '">' . ( $page - 10 ) . '</a>';
			}
			for ( $i = 2; $i > 0; $i-- ) {
				if ( 0 < ( $page - $i ) ) {
					$output .= '<a class="lhr-page" data-page="' . ( $page - $i ) . '">' . ( $page - $i ) . '</a>';
				}
			}

			// Current page.
			$output .= '<a class="lhr-page active" data-page="' . $page . '">' . $page . '</a>';

			for ( $i = 1; $i <= 2; $i++ ) {
				if ( $total_pages >= ( $page + $i ) ) {
					$output .= '<a class="lhr-page" data-page="' . ( $page + $i ) . '">' . ( $page + $i ) . '</a>';
				}
			}
			if ( $total_pages > ( $page + 10 ) ) {
				$output .= '<a class="lhr-page" data-page="' . ( $page + 10 ) . '">' . ( $page + 10 ) . '</a>';
			}
			if ( $total_pages > ( $page + 2 ) ) {
				$output .= '<a class="lhr-page last-page" data-page="' . $total_pages . '">&gt;&gt;</a>';
			}
		}

		return $output;
	}
}
