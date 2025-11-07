/**
 * Log HTTP Requests admin scripts.
 *
 * @package Log_HTTP_Requests
 */

(function ($) {
	$(
		function () {

			// Refresh.
			LHR.refresh = function () {
				$( '.lhr-refresh' ).text( 'Refreshing...' ).attr( 'disabled', 'disabled' );

				$.post(
					ajaxurl,
					{
						'action': 'lhr_query',
						'_wpnonce': LHR.nonce,
						'data': LHR.query_args
					},
					function (data) {
						LHR.response = data;

						var html = '';
						$.each(
							data.rows,
							function (idx, row) {
								var runtime   = parseFloat( row.runtime );
								var css_class = (runtime > 1) ? ' warn' : '';
								css_class     = (runtime > 2) ? ' error' : css_class;
								html         += '<tr>';
								html         += '<td class="field-url"><div><a href="javascript:;" data-id="' + idx + '">' + row.url + '</a></div></td>';
								html         += '<td class="field-status-code">' + row.status_code + '</td>';
								html         += '<td class="field-runtime' + css_class + '">' + row.runtime + '</td>';
								html         += '<td class="field-date" title="' + row.date_raw + '">' + row.date_added + '</td>';
								html         += '</tr>';
							}
						);
						$( '.lhr-listing tbody' ).html( html );
						$( '.lhr-pager' ).html( data.pager );
						$( '.lhr-refresh' ).text( 'Refresh' ).removeAttr( 'disabled' );

						// Update search input with current search term.
						if (LHR.query_args.search) {
							$( '.lhr-search-input' ).val( LHR.query_args.search );
							$( '.lhr-clear-search' ).show();
						} else {
							$( '.lhr-clear-search' ).hide();
						}
					},
					'json'
				);
			}

			// Clear.
			LHR.clear = function () {
				$( '.lhr-clear' ).text( 'Clearing...' ).attr( 'disabled', 'disabled' );

				$.post(
					ajaxurl,
					{
						'action': 'lhr_clear',
						'_wpnonce': LHR.nonce
					},
					function (data) {
						$( '.lhr-listing tbody' ).html( '' );
						$( '.lhr-clear' ).text( 'Clear log' ).removeAttr( 'disabled' );
					},
					'json'
				);
			}

			LHR.show_details = function (action) {
				var id = LHR.active_id;

				if ('next' == action && id < LHR.response.rows.length - 1) {
					id = id + 1;
				} else if ('prev' == action && id > 0) {
					id = id - 1;
				}

				LHR.active_id = id;

				var data = LHR.response.rows[id];
				$( '.http-url' ).text( data.url );
				$( '.http-request-id' ).text( id );
				$( '.http-request-args' ).text( JSON.stringify( JSON.parse( data.request_args ), null, 2 ) );
				$( '.http-response' ).text( JSON.stringify( JSON.parse( data.response ), null, 2 ) );
				$( '.http-backtrace' ).text( data.backtrace || 'No backtrace available' );
				$( '.media-modal' ).addClass( 'open' );
				$( '.media-modal-backdrop' ).addClass( 'open' );
			}

			// Page change.
			$( document ).on(
				'click',
				'.lhr-page:not(.active)',
				function () {
					LHR.query_args.page = parseInt( $( this ).attr( 'data-page' ) );
					LHR.refresh();
				}
			);

			// Search functionality.
			$( document ).on(
				'click',
				'.lhr-search-button',
				function () {
					var search_term       = $( '.lhr-search-input' ).val().trim();
					LHR.query_args.search = search_term;
					LHR.query_args.page   = 1;
					LHR.refresh();

					if (search_term) {
						$( '.lhr-clear-search' ).show();
					} else {
						$( '.lhr-clear-search' ).hide();
					}
				}
			);

			// Clear search.
			$( document ).on(
				'click',
				'.lhr-clear-search',
				function () {
					$( '.lhr-search-input' ).val( '' );
					LHR.query_args.search = '';
					LHR.query_args.page   = 1;
					LHR.refresh();
					$( this ).hide();
				}
			);

			// Search on Enter key.
			$( document ).on(
				'keypress',
				'.lhr-search-input',
				function (e) {
					if (13 == e.keyCode) {
						e.preventDefault();
						$( '.lhr-search-button' ).click();
					}
				}
			);

			// Open detail modal.
			$( document ).on(
				'click',
				'.field-url a',
				function () {
					LHR.active_id = parseInt( $( this ).attr( 'data-id' ) );
					LHR.show_details( 'curr' );
				}
			);

			// Close modal window.
			$( document ).on(
				'click',
				'.media-modal-close',
				function () {
					var $this = $( this );

					if ($this.hasClass( 'prev' ) || $this.hasClass( 'next' )) {
						var action = $this.hasClass( 'prev' ) ? 'prev' : 'next';
						LHR.show_details( action );
						return;
					}

					$( '.media-modal' ).removeClass( 'open' );
					$( '.media-modal-backdrop' ).removeClass( 'open' );
					$( document ).off( 'keydown.lhr-modal-close' );
				}
			);

			$( document ).keydown(
				function (e) {

					if ( ! $( '.media-modal' ).hasClass( 'open' )) {
						return;
					}

					if (-1 < $.inArray( e.keyCode, [27, 38, 40] )) {
						e.preventDefault();

						if (27 == e.keyCode) { // esc.
							$( '.media-modal-close' ).click();
						} else if (38 == e.keyCode) { // up.
							$( '.media-modal-close.prev' ).click();
						} else if (40 == e.keyCode) { // down.
							$( '.media-modal-close.next' ).click();
						}
					}
				}
			);

			// Ajax.
			LHR.refresh();
		}
	);
})( jQuery );
