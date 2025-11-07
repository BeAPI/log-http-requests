<?php
/**
 * Plugin Name: Log HTTP Requests
 * Description: Log all those pesky WP HTTP requests
 * Version: 1.5.0
 * Author: FacetWP, LLC
 * Author URI: https://facetwp.com/
 *
 * Copyright 2023 FacetWP, LLC
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package Log_HTTP_Requests
 */

defined( 'ABSPATH' ) || exit;

// Load main plugin class.
require_once __DIR__ . '/includes/class-main.php';

/**
 * Get the main plugin instance.
 *
 * @since 1.0.0
 *
 * @return Log_HTTP_Requests The plugin instance.
 */
function LHR() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- Established function name.
	return Log_HTTP_Requests::instance();
}

// Initialize plugin.
LHR();
