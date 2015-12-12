<?php
/*
Plugin Name: SAR Disable REST API
Description: Disable WP core REST API and remove its HTTP header and link tag
Author: Samuel Aguilera
Version: 1.0
Author URI: http://www.samuelaguilera.com
License: GPL3
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

// Remove HTTP header and link tag
add_filter( 'init', 'sar_remove_rest_api_headers' );

function sar_remove_rest_api_headers() {
	remove_action( 'wp_head',                    'rest_output_link_wp_head', 10 );
	remove_action( 'template_redirect',          'rest_output_link_header', 11 );
}

// Disable REST API 
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');

?>