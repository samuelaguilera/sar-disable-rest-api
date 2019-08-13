<?php
/**
 * Plugin Name: Disable REST API for Real
 * Description: Really prevents the REST API from handling requests (default) or require user to be logged in.
 * Author: Samuel Aguilera
 * Version: 2.1.1
 * Author URI: http://www.samuelaguilera.com
 * Text Domain: sar-disable-rest-api
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Disable REST API for Real
 */

/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get option value to determine what to do.
$sar_rest_api_mode = get_option( 'sar_disable_rest_api', '' );

if ( 'logged' !== $sar_rest_api_mode ) { // Disable REST API if option is not set to Logged In Only.

	// Remove REST API filters (including HTTP header and link tags).
	add_filter( 'init', 'sar_disable_rest_api' );

	/**
	 * Removing REST API stuff.
	 */
	function sar_disable_rest_api() {

		// REST API filters.
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		// Note this header is also set by serve_request() function. The following removes it only in non REST API pages.
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		remove_action( 'auth_cookie_malformed', 'rest_cookie_collect_status' );
		remove_action( 'auth_cookie_expired', 'rest_cookie_collect_status' );
		remove_action( 'auth_cookie_bad_username', 'rest_cookie_collect_status' );
		remove_action( 'auth_cookie_bad_hash', 'rest_cookie_collect_status' );
		remove_action( 'auth_cookie_valid', 'rest_cookie_collect_status' );
		remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );

		// REST API actions.
		remove_action( 'init', 'rest_api_init' );
		remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
		remove_action( 'rest_api_init', 'register_initial_settings', 10 );
		remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );
		remove_action( 'parse_request', 'rest_api_loaded' );

		// Turn off jsonp REST callback support.
		add_filter( 'rest_jsonp_enabled', '__return_false' );

		// Remove links for /wp-json/oembed/ endpoints.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	}

	// Return a 404 error for any request sent to REST API endpoints.
	add_action(
		'template_redirect',
		function() {
			global $wp;

			// Only if wp-json is the start of the slug.
			if ( strpos( $wp->request, 'wp-json' ) === 0 ) {
				wp_die( 'Page not found.', '404 - Page not found', 404 );
			}
		}
	);

}

if ( 'logged' === $sar_rest_api_mode ) { // Require user to be logged in to use REST API.

	add_filter(
		'rest_authentication_errors',
		function( $result ) {
			if ( ! empty( $result ) ) {
				return $result;
			}
			if ( ! is_user_logged_in() ) {
				return new WP_Error( 'rest_not_logged_in', wp_filter_nohtml_kses( __( 'REST API requests allowed only for logged in users.', 'sar-disable-rest-api' ) ), array( 'status' => rest_authorization_required_code() ) );
			}
			return $result;
		}
	);

}

// Option handling.
add_filter( 'admin_init', 'sar_disable_rest_api_settings' );

/**
 * Register plugin's setting field.
 */
function sar_disable_rest_api_settings() {

	register_setting( 'general', 'sar_disable_rest_api', 'esc_attr' );
	add_settings_field( 'sar_disable_rest_api', '<label for="sar_disable_rest_api">' . wp_filter_nohtml_kses( __( 'REST API', 'sar-disable-rest-api' ) ) . '</label>', 'sar_disable_rest_api_settings_html', 'general' );
}

/**
 * Output for plugin's setting.
 */
function sar_disable_rest_api_settings_html() {
	global $sar_rest_api_mode;

	// Set to off as default if option was not set by the user (for previous version and new installations).
	$sar_rest_api_mode = empty( $sar_rest_api_mode ) ? $sar_rest_api_mode = 'off' : $sar_rest_api_mode;

	?>
	<select name="sar_disable_rest_api" title="REST API">
		<option value="off" <?php selected( $sar_rest_api_mode, 'off' ); ?>><?php esc_html_e( 'Disabled', 'sar-disable-rest-api' ); ?></option>
		<option value="logged" <?php selected( $sar_rest_api_mode, 'logged' ); ?>><?php esc_html_e( 'Logged In Only', 'sar-disable-rest-api' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Choose Disabled to completely disable access to WordPress REST API or Logged In Only to keep REST API access enabled but require the user to be logged in to accept the requests.', 'sar-disable-rest-api' ); ?></p>
	<?php
}
