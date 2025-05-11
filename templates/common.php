<?php
/** Common Shared Code
 * @Package: 		com.vsoft.trailmonitor
 * @File			templates/common.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

 /** Loads Scripts and Style Sheets Used in the Admin and Shortcode Pages
 * wp_enqueue_media Long Form to Go Around Bugs 
 */
function vstm_load_styles_and_scripts () {
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');
	$mode = get_user_option('media_library_mode', get_current_user_id()) ? get_user_option('media_library_mode', get_current_user_id()) : 'grid';
	$modes_list = ['grid', 'list'];
	$new_mode = vstm_get_request_string('mode');
	if (!empty($new_mode) && in_array($new_mode, $modes_list)) {
		update_user_option(get_current_user_id(), 'media_library_mode', $new_mode);
		$mode = $new_mode;
	}
	if (!empty($_SERVER['PHP_SELF']) && 'upload.php' === basename($_SERVER['PHP_SELF']) && 'grid' !== $mode) {
		wp_dequeue_script('media');
	}
	wp_enqueue_media();

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-checkboxradio');
	wp_register_style('jquery-ui', plugins_url('../public/css/jquery-ui.css', __FILE__));
	wp_enqueue_style('jquery-ui');

	wp_register_style('vstm_datatables_css', plugins_url('../datatables.min.css', __FILE__));
	wp_enqueue_style('vstm_datatables_css');
	wp_enqueue_script('vstm_datatables', plugins_url('../datatables.min.js', __FILE__));
		
    if (is_admin()) {    
	    wp_enqueue_style('wp-color-picker'); 
        wp_enqueue_script('vstm_script', plugins_url('../admin.js', __FILE__), ['wp-color-picker'], VSTM_VER, true);
    }
    else {
        wp_enqueue_style( 'vstm_wp-color-picker-css', admin_url() . 'css/color-picker.css', [], VSTM_VER, true);
        wp_enqueue_script( 'vstm_wp-color-picker', admin_url() . 'js/color-picker.js', [], VSTM_VER, true);
        wp_enqueue_style( 'vstm_script_wp_button_styles', includes_url() . 'css/buttons.css', [], VSTM_VER, true);
        
        wp_enqueue_script('vstm_script', plugins_url('../admin.js', __FILE__), ['vstm_wp-color-picker'], VSTM_VER, true);    
    }
}
