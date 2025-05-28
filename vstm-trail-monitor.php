<?php
/* Show Trail Information
  Plugin Name: Trail Monitor
  Plugin URI: https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
  Version: 1.4
  Requires at least: 6.2
  Requires PHP: 8.2
  Description: Show up to date information for hiking trails.
  Author: VSoft Solutions
  Author URI: https://hiking.princegeorge.tech/
  Copyright: (c) 2025, VSoft Solutions
  Package: com.vsoft.trailmonitor
  License: GPLv3
  Updated: 22/04/2025 Created: 22/04/2025
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Make sure we dont have multiple version of the same plugin installed
if ( isset( $vtsm_dir ) ) {
	include( dirname( __FILE__ ) . '/helpers/show-multiple-version-notice.php' );
	return;
}
$vtsm_dir = dirname( __FILE__ );

define('VSTM_VER', 1.4);

define('VSTM_ROOT_PATH',  plugin_dir_path(__FILE__));

define('VSTM_MAX_FIELD_LENGTH_NAME', 75);
define('VSTM_MAX_FIELD_LENGTH_LINK', 300);
define('VSTM_MAX_FIELD_LENGTH_COMMENT', 500);
define('VSTM_MAX_FIELD_LENGTH_SUBMITTER', 75);

// ****** Table Names *****
global $wpdb;
$vstm_table_trails = $wpdb->prefix . 'vstm_trails';
$vstm_table_statuses = $wpdb->prefix . 'vstm_statuses';

// ***** Register Stuff *****
register_activation_hook(__FILE__, 'vstm_install');
add_action('wp_loaded', 'vstm_scripts');
add_action('widgets_init', 'vstm_load_widgets');

if (is_admin()) {
	add_action('init', 'vstm_register_session');

	add_shortcode('vstm-trail-status-youtube', 'vstm_sc_youtube');

	// Setup Root Settings Menu
	require_once(VSTM_ROOT_PATH . 'templates/settings.php');
	// Setup Sub Menu Items
	require_once(VSTM_ROOT_PATH . 'admin.php');

	add_action('admin_enqueue_scripts', 'vstm_load_styles_and_scripts');
	add_action('admin_menu', 'vstm_admin');
	add_action('wp_ajax_vstm_update_status', 'vstm_update_status');
	
} else {
	//if (!session_id()) session_start(); // For storing list options

	add_action('wp_enqueue_scripts', 'vstm_enqueue_recaptcha');
		
	require_once(VSTM_ROOT_PATH . 'templates/common.php');
	add_action( 'wp_enqueue_scripts', 'vstm_load_styles_and_scripts');
	
	require_once(VSTM_ROOT_PATH . 'shortcode.php');
	add_shortcode('vstm-trail-status-list',    'vstm_sc_table_list');
	add_shortcode('vstm-trail-status',         'vstm_sc_table');
	add_shortcode('vstm-trail-status-blocks',  'vstm_sc_blocks');
	add_shortcode('vstm-trail-status-submit',  'vstm_sc_submit');
	add_shortcode('vstm-trail-status-youtube', 'vstm_sc_youtube');

	add_action( 'parse_request', function ( $wp ) {
		// Run your actions only if the current request path is exactly 'vstm-trail-submit'
		// as in https://example.com/vstm-trail-submit if the site URL is https://example.com
		//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		//	if ( 'vstm-trail-submit' === $wp->request ) {
				//echo "PARSE_REQUEST 1 [$wp->request]";
				//echo vstm_sc_submit(null,null);
				//echo do_shortcode('[trail-status-submit]');
				//exit;
			//}
		//}
	} );	
}

function vstm_register_session() {
	// For storing list options
	$status = session_status();
    if ( PHP_SESSION_NONE === $status ) {
        session_start();
    }
}

/** Load Google Recaptcha javascript
 */

function vstm_enqueue_recaptcha() {
	wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], VSTM_VER, true);
}

/** Register the Widgets
 */
function vstm_load_widgets() {
	require_once(VSTM_ROOT_PATH . 'widgets.php');
	register_widget('vstm_widget');
}

/** Load CSS and JS Files
 */
function vstm_scripts () {
	wp_register_style('vstm_css', plugins_url('vstm-trail-monitor.css', __FILE__), [], VSTM_VER);
	wp_enqueue_style('vstm_css');
}

/** Stuff to Do on Activation 
 * Add Tables to Database, Add basic statuses
 * @global type $wpdb
 */
function vstm_install () {
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	global $wpdb;
	$vstm_table_trails = $wpdb->prefix . 'vstm_trails';
	$vstm_table_statuses = $wpdb->prefix . 'vstm_statuses';
	$charset_collate = $wpdb->get_charset_collate();
	
	// ***** Add Tables *****
	$sql_trails = "CREATE TABLE $vstm_table_trails (
		trail_id mediumint(9) NOT NULL AUTO_INCREMENT,
		created timestamp,
		visitdate datetime NOT NULL,
		name varchar(75) NOT NULL,
		link varchar(300),
		comment varchar(500),
		submitter_name varchar(75),
		image_id int,
		sort_order float,
		show_widget tinyint(1) DEFAULT 1,
		show_shortcode tinyint(1) DEFAULT 1,
		status_id int,
		hidden TINYINT(1) NOT NULL DEFAULT '0',
		UNIQUE KEY trail_id (trail_id)
		) $charset_collate;";
	// automatically alters the table on upgrade scenarios
	$db_results = dbDelta($sql_trails);
	if (!empty($db_results)) {
		foreach ($db_results as $db_result) { 
			error_log("VSTM-Plugin Update table $vstm_table_trails: $db_result | $wpdb->last_error  | $wpdb->last_query");
		}
	}

	$sql_status = "CREATE TABLE $vstm_table_statuses (
		status_id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(75) NOT NULL,
		sort_order float,
		color varchar(20),
		UNIQUE KEY status_id (status_id)
		) $charset_collate;";
	// automatically alters the table on upgrade scenarios
	$db_results = dbDelta($sql_status);
	if (!empty($db_results)) {
		foreach ($db_results as $db_result) { 
			error_log("VSTM-Plugin Update table $vstm_table_statuses: $db_result | $wpdb->last_error  | $wpdb->last_query");
		}
	}
	
	// ***** Add Set of Statuses to Start With *****	
	$status_count = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(status_id) FROM %s",
		$vstm_table_statuses)
	);
	if (0 == $status_count) {
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 1, name = 'Unknown', sort_order = 1;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 5, name = 'Dry', sort_order = 5;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 7, name = 'Variable', sort_order=7;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 9, name = 'Wet', sort_order=9;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 11, name = 'Muddy', sort_order=11;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 13, name = 'Snow', sort_order=13;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 15, name = 'Snowshoe Packed', sort_order=15;",$vstm_table_statuses));
		$wpdb->query($wpdb->prepare("INSERT INTO %s SET status_id = 17, name = 'Icy', sort_order=``;",$vstm_table_statuses));
	}
}
