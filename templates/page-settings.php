<?php
/**
 * Settings page template.
 *
 * @package Log_HTTP_Requests
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<script>
var LHR = {
	response: [],
	query_args: {
		'orderby': 'id',
		'order': 'DESC',
		'page': 1,
		'search': ''
	},
	nonce: '<?php echo esc_js( wp_create_nonce( 'lhr_nonce' ) ); ?>'
};
</script>

<div class="wrap">
	<h3><?php echo esc_html__( 'Log HTTP Requests', 'log-http-requests' ); ?></h3>

	<div class="lhr-toolbar">
		<div class="lhr-search-box">
			<input type="text" class="lhr-search-input" placeholder="<?php echo esc_attr__( 'Search URLs...', 'log-http-requests' ); ?>" value="">
			<button class="button lhr-search-button"><?php echo esc_html__( 'Search', 'log-http-requests' ); ?></button>
			<button class="button lhr-clear-search" style="display:none;"><?php echo esc_html__( 'Clear', 'log-http-requests' ); ?></button>
		</div>
		<div class="lhr-actions">
			<button class="button lhr-clear" onclick="LHR.clear()"><?php echo esc_html__( 'Clear log', 'log-http-requests' ); ?></button>
			<button class="button lhr-refresh" onclick="LHR.refresh()"><?php echo esc_html__( 'Refresh', 'log-http-requests' ); ?></button>
		</div>
	</div>

	<div class="lhr-pager"></div>
	<table class="widefat lhr-listing">
		<thead>
			<tr>
				<td><?php echo esc_html__( 'URL', 'log-http-requests' ); ?></td>
				<td title="<?php echo esc_attr__( 'HTTP response code', 'log-http-requests' ); ?>"><?php echo esc_html__( 'Status', 'log-http-requests' ); ?></td>
				<td title="<?php echo esc_attr__( 'seconds', 'log-http-requests' ); ?>"><?php echo esc_html__( 'Runtime', 'log-http-requests' ); ?></td>
				<td><?php echo esc_html__( 'Date Added', 'log-http-requests' ); ?></td>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	<div class="lhr-pager"></div>
</div>

<!-- Modal window -->

<div class="media-modal">
	<button class="button-link media-modal-close prev"><span class="media-modal-icon"></span></button>
	<button class="button-link media-modal-close next"><span class="media-modal-icon"></span></button>
	<button class="button-link media-modal-close"><span class="media-modal-icon"></span></button>
	<div class="media-modal-content">
		<div class="media-frame">
			<div class="media-frame-title">
				<h1><?php echo esc_html__( 'HTTP Request', 'log-http-requests' ); ?></h1>
			</div>
			<div class="media-frame-content">
				<div class="modal-content-wrap">
					<h3><?php echo esc_html__( 'URL', 'log-http-requests' ); ?></h3>
					<div>
						[<span class="http-request-id"></span>]
						<span class="http-url"></span>
					</div>
					<div class="wrapper">
						<div class="box">
							<h3><?php echo esc_html__( 'Request', 'log-http-requests' ); ?></h3>
							<div class="http-request-args"></div>
						</div>
						<div class="box">
							<h3><?php echo esc_html__( 'Response', 'log-http-requests' ); ?></h3>
							<div class="http-response"></div>
						</div>
					</div>
					<div class="backtrace-section">
						<h3><?php echo esc_html__( 'Backtrace', 'log-http-requests' ); ?></h3>
						<div class="http-backtrace"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="media-modal-backdrop"></div>
