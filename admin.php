<?php
/** Admin Page Controller
 * @Package: 		com.vsoft.trailmonitor
 * @File			admin.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once(VSTM_ROOT_PATH . 'templates/common.php');

/** Creates the Trail List Approvals Page and Handles Bulk Actions and Reordering
 */
function vstm_trail_list_approval_page () {
	vstm_trail_list_page (true);
}

/** Creates the Trail List Page and Handles Bulk Actions and Reordering
 */
function vstm_trail_list_page ($show_unapproved_only=false) {
	// ***** Security Check *****
	if (!current_user_can('publish_pages')) {
		wp_die(esc_html(__('You do not have sufficient permissions to access this page.','vstm-trail-monitor')));
	}

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'models/trails_model.php');
	$vstm_Trails_Model = new vstm_Trails_Model();
	require_once(VSTM_ROOT_PATH . 'helpers/view_helper.php');
	
	$message_list = array();
	
	// ***** Run Bulk Actions if Submitted *****
	//$bulk_action_list = vstm_get_request_int_array('bulk_action_list', 'admin', $_POST['_wpnonce'], 'trails_bulk');
	$bulk_action_list = vstm_get_request_int_array();
	if (isset($_POST['_wpnonce']) && !empty($bulk_action_list)) {
		// *** Security ***
		check_admin_referer('trails_bulk');
		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
		$action = vstm_get_request_string('action', null, false, 'admin', $nonce_var, 'trails_bulk');

		$trail_list = $vstm_Trails_Model->get_names_list();
		
		// *** Run The Action ***
		switch ($action) {
			case 'delete':
				foreach ($bulk_action_list as $trail_id) {
					if ($vstm_Trails_Model->delete($trail_id))
						$message_list[] = [$trail_list[$trail_id] . ' Deleted', 1,5];
					else 
						$message_list[] = ['There was an error deleting ' . $trail_list[$trail_id], 3, 2];
				}
				break;
			case 'trail_not_approved':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_approved($trail_id, 0);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Not Approved', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
				break;
			case 'trail_approved':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_approved($trail_id, 1);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Approved', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
				break;
	
			case 'hide_on_widget':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_show_widget($trail_id, 0);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Hidden on Widget', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
				break;
			case 'show_on_widget':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_show_widget($trail_id, 1);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Un-Hidden on Widget', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
				break;
			case 'hide_on_shortcode':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_show_shortcode($trail_id, 0);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Hidden on Shortcode', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
				break;
			case 'show_on_shortcode':
				foreach ($bulk_action_list as $trail_id) {
					$result = $vstm_Trails_Model->set_show_shortcode($trail_id, 1);
					if ($result)
						$message_list[] = [$trail_list[$trail_id] . ' Un-Hidden on Shortcode', 1, 5];
					elseif (false === $result)
						$message_list[] = ['There was an error updating ' . $trail_list[$trail_id], 3, 2];
				}
			 	break;
			default:
				$message_list[] = ['There was an UNKNOWN action detected [' . $action .']', 3, 2];
		}
	}
	
	// ***** Get Data *****
	$table_data = $vstm_Trails_Model->get_list(($show_unapproved_only ? 1 : null));

	// ***** Call View *****
	include(VSTM_ROOT_PATH . 'views/trail_list.php');
}

/** Add/Edit Trail Info Page
 */
function vstm_trail_edit_page () {
	// ***** Security Check *****
	if (!current_user_can('publish_pages')) {
		wp_die(esc_html(__('You do not have sufficient permissions to access this page.','vstm-trail-monitor')));
	}

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'models/trails_model.php');
	$vstm_Trails_Model = new vstm_Trails_Model();
	require_once(VSTM_ROOT_PATH . 'helpers/view_helper.php');
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');

	$message_list = array();
	
	// ***** Form Submitted *****
	if (isset($_POST['_wpnonce'])) {
		$failure = false;
		// *** Validate & Check ***
		// * Trail Id and Nonce *

		$nonce_action = '';
		if (empty($_REQUEST['trail_id'])) { 
			$nonce_action = 'trail_add';
		} else {
			$trail_id = vstm_filter_int_strict(sanitize_text_field(wp_unslash($_REQUEST['trail_id'])), null);
			$nonce_action = 'trail_edit_' . $trail_id;
		}

		// *** Security ***
		check_admin_referer($nonce_action);

		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
		$trail_id = vstm_get_request_int('trail_id', null, 'admin', $nonce_var, $nonce_action);
		
		// * Name Is Required *
		$name = vstm_get_request_string('trail_name', null, false, 'admin', $nonce_var, $nonce_action);
		if (empty($name)) {
			$message_list[] = ['Name is Required', 3, 3];
			$failure = true;
		}

		// * Visit Date Is Required *
		$visitdate = vstm_get_request_string('visitdate', null, false, 'admin', $nonce_var, $nonce_action);
		if (empty($visitdate)) {
			$message_list[] = ['Visit Date is Required', 3, 3];
			$failure = true;
		}
		
		// * Yes/No Fields - Defaults to Yes *
		if (empty($_POST['show_shortcode']))
			$show_shortcode = 0;
		else
			$show_shortcode = 1;
		
		if (empty($_POST['show_widget']))
			$show_widget = 0;
		else
			$show_widget = 1;
		
		// * Sanitize Integer Fields *
		$sort_order = vstm_get_request_int('sort_order', 99, 'admin', $nonce_var, $nonce_action);
		$image_id = vstm_get_request_int('image_id', null, 'admin', $nonce_var, $nonce_action);
		$status_id = vstm_get_request_int('status_id', null, 'admin', $nonce_var, $nonce_action);
		$approved = vstm_get_request_int('trail_approved', null, 'admin', $nonce_var, $nonce_action);

		// * Link: Sanitize and Add http:// if Missing *
		if (!empty($_POST['link'])) {
			$link = wp_kses_post(wp_unslash($_POST['link']));
			if (0 != strncasecmp($link, "http://", 7) && 0 != strncasecmp($link, "https://", 8))
				$link = 'http://' . $link;
			$link = filter_var($link, FILTER_SANITIZE_URL);
		} else {
			$link = null;
		}

		// * Comment: Sanitize
		if (!empty($_POST['comment'])) {
			//$comment = filter_var(wp_unslash(trim($_POST['comment']), FILTER_SANITIZE_STRING));
			$comment = wp_kses_post(wp_unslash($_POST['comment']));
			
		} else {
			$comment = null;
		}

		// Submitter: Sanitize
		if (!empty($_POST['submitter_name'])) {
			$submitter = vstm_get_request_string('submitter_name', null, false, 'admin', $nonce_var, $nonce_action);
		}
		else {
			$submitter = null;
		}

		// *** Update or Add ***
		if ($trail_id && !$failure) {
			$update_result = $vstm_Trails_Model->update($trail_id, $name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget, $show_shortcode, $status_id, $approved);
			if ($update_result === false) {
				$message_list[] = ['Error Updating ' . $name . ' (#' . $trail_id . ')', 3, 3];
				$failure = true;
			} else {
				$message_list[] = [$name . ' Updated', 1, 5];
			}
		} elseif (!$failure) {
			$add_result = $vstm_Trails_Model->add($name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget, $show_shortcode, $status_id, $approved);
			if ($add_result) {
				$message_list[] = [$name . ' Added', 1, 5];
			} else {
				$message_list[] = ['Error Adding Trail ' . $name, 3, 3];
				$failure = true;
			}
		}
		
		// *** Views - Back to Form or Trail List ***
		if ($failure) {
			if (!empty($trail_id)) {
				$record['trail_id'] = $trail_id;
			}
			$record['name'] = $name;
			$record['visitdate'] = $visitdate;
			$record['link'] = $link;
			$record['comment'] = $comment;
			$record['submitter_name'] = $submitter;
			$record['image_id'] = $image_id;
			$record['sort_order'] = $sort_order;
			$record['show_widget'] = $show_widget;
			$record['show_shortcode'] = $show_shortcode;
			$record['status_id'] = $status_id;
			$record['hidden'] = !$approved;
			include(VSTM_ROOT_PATH . 'views/trail_edit.php');
		} else {
			// ***** View - Trail List *****
			$table_data = $vstm_Trails_Model->get_list();
			include(VSTM_ROOT_PATH . 'views/trail_list.php');
		}
	} 
	else {
		// ***** No Form Submitted *****
		$trail_id = (isset($_REQUEST['trail']) ? vstm_filter_int_strict(sanitize_text_field(wp_unslash($_REQUEST['trail'])), null) : null);
		//$trail_id = vstm_get_request_int('trail');
		
		if (empty($trail_id)) {
			$record = ['name'=>'', 'visitdate'=>'', 'link'=>'', 'comment'=>'', 'submitter_name'=>'', 'image_id'=>'', 'sort_order'=>'', 'show_widget'=>'1', 'show_shortcode'=>'1', 'hidden'=>0, 'status_id'=>0];
		} else {
			$record = $vstm_Trails_Model->get($trail_id);
		}
		// ***** Call View *****
		include(VSTM_ROOT_PATH . 'views/trail_edit.php');
		include(VSTM_ROOT_PATH . 'views/about.php');
	}
}

/** List of Statuses Page with Add Form
 */
function vstm_status_list_page () {
	// ***** Security Check *****
	if (!current_user_can('publish_pages')) {
		wp_die(esc_html(__('You do not have sufficient permissions to access this page.','vstm-trail-monitor')));
	}

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'models/status_model.php');
	$vstm_Status_Model = new vstm_Status_Model();
	require_once(VSTM_ROOT_PATH . 'helpers/view_helper.php');
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');
	
	$message_list = array();
	
	// ***** Run Bulk Actions if Submitted *****
	//$bulk_action_list = vstm_get_request_int_array('bulk_action_list', 'admin', $_POST['_wpnonce'], 'status_bulk');
	$bulk_action_list = vstm_get_request_int_array('bulk_action_list');
	if (isset($_POST['_wpnonce']) && !empty($bulk_action_list)) {
		// *** Security ***
		check_admin_referer('status_bulk');
		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
		$action = vstm_get_request_string('action', null, false, 'admin', $nonce_var, 'status_bulk');
		
		// *** Run The Action ***
		switch ($action) {
			case 'delete':
				$statuses_deleted = 0;
				foreach ($bulk_action_list as $status_id) {
					if ($vstm_Status_Model->delete($status_id)) {
						$statuses_deleted++;
					} else {
						$message_list[] = ["Could not delete status #$status_id.", 3, 3];
					}
				}
				if (1 == $statuses_deleted)
					$message_list[] = ["Status deleted.", 1, 5];
				else 
					$message_list[] = ["$statuses_deleted statuses deleted.", 1, 5];
				break;
		} 
	}
	
	// ***** Add New Status if Submitted *****
	if (isset($_POST['action']) && 'add' == $_POST['action']) {
		//check_admin_referer('status_add');
		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));

		// *** Check for Name ***
		$name = vstm_get_request_string('vstm_name', null, false, 'admin', $nonce_var, 'status_add');
		if (empty($name)) {
			$message_list[] = ['The status needs a name.', 3, 3];
		} else {
			// *** Set Default Values if Not Submitted ***
			$new_status_sort_order = vstm_get_request_int('vstm_sort_order', 1, 'admin', $nonce_var, 'status_add');
			$new_status_color = vstm_get_request_string('vstm_color', 'black', false, 'admin', $nonce_var, 'status_add');

			$add_result = $vstm_Status_Model->add($name, $new_status_sort_order, $new_status_color);
			if ($add_result) $message_list[] = ['Status Added', 1, 5];
			else $message_list[] = ['Problem Adding Status', 3, 3];
		}
	}
	
	// ***** Get Data *****
	$status_list = $vstm_Status_Model->get_list();

	// ***** Call View *****
	include(VSTM_ROOT_PATH . 'views/status_list.php');
	include(VSTM_ROOT_PATH . 'views/about.php');
}

/** Ajax Request Handler for Inline Status Updates
 */
function vstm_update_status () {	
	// ***** Security Check *****
	if (!current_user_can('publish_pages')) {
		wp_die(esc_html(__('You do not have sufficient permissions to access this page.','vstm-trail-monitor')));
	}
	check_ajax_referer('status_bulk', 'wp_nonce');

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');

	// ***** Post Security *****
	$nonce_var = (isset($_POST['wp_nonce']) ? sanitize_text_field(wp_unslash($_POST['wp_nonce'])) : null);

	$status_id = vstm_get_request_int('status_id', null, 'admin-ajax', $nonce_var, 'status_bulk');
	$name = vstm_get_request_string('name', null, false, 'admin-ajax', $nonce_var, 'status_bulk');
	$sort_order = vstm_get_request_int('sort_order', 99, 'admin-ajax', $nonce_var, 'status_bulk');
	$color = vstm_get_request_string('color', 'black', false, 'admin-ajax', $nonce_var, 'status_bulk');
	
	// ***** Update Database if Status Id is Set *****
	if (empty($status_id)) {
		$result = false;
	} else {
		require_once(VSTM_ROOT_PATH . 'models/status_model.php');
		$vstm_Status_Model = new vstm_Status_Model();
		$result = $vstm_Status_Model->update($status_id, $name, $sort_order, $color);
	}
	
	// ***** Output to Browser *****
	if ($result)
		echo 'true';
	else
		echo 'false';
	wp_die();
}

/** Update the Current Status of Trails Page
 */
function vstm_update () {
	// ***** Security Check *****
	if (!current_user_can('publish_posts')) {
		wp_die(esc_html(__('You do not have sufficient permissions to access this page.','vstm-trail-monitor')));
	}

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'models/trails_model.php');
	$vstm_Trails_Model = new vstm_Trails_Model();
	require_once(VSTM_ROOT_PATH . 'models/status_model.php');
	$vstm_Status_Model = new vstm_Status_Model();
	require_once(VSTM_ROOT_PATH . 'helpers/view_helper.php');
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');

	$message_list = array();
	
	// ***** Update Status if Form Submitted *****
	$trail_list = $vstm_Trails_Model->get_trail_names();
	
	if (isset($_POST['_wpnonce'])) {
		check_admin_referer('update');
		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));

		foreach ($trail_list as $trail) {
			$status_id = vstm_get_request_int('t_' . $trail['trail_id'], null, 'admin', $nonce_var, 'update');
			if (isset($status_id))
				$vstm_Trails_Model->set_status($trail['trail_id'], $status_id);
		}
		if(isset($_POST['vstm_notes_sc'])) {
			update_option('vstm_notes_sc', wp_kses_post( filter_var( wp_unslash( $_POST['vstm_notes_sc']))));
		}
		if(isset($_POST['vstm_notes_widget'])) {
			update_option('vstm_notes_widget', wp_kses_post(filter_var(wp_unslash( $_POST['vstm_notes_widget']))));
		}
		$message_list[] = ['Statuses Updated', 1, 3];
	}
		
	// ***** Get Fresh Data *****
	$trail_list = $vstm_Trails_Model->get_trail_names();
	$status_list = $vstm_Status_Model->get_list();
	$notes_sc = get_option('vstm_notes_sc');
	$notes_widget = get_option('vstm_notes_widget');

	// ***** Call View *****
	include(VSTM_ROOT_PATH . 'views/update.php');
	include(VSTM_ROOT_PATH . 'views/about.php');
}

/** Registers the Admin Pages with WordPress
 */
function vstm_admin () {
	$top_level_menu = 'vstm'; // 'trail-status-2'
	//add_menu_page('Trail Status', 'Trail Status', 'publish_posts', 'trail-status-2', 'vstm_update', '', 3.4);
	add_submenu_page($top_level_menu, 'Trail Status Update', 'Update Status', 'publish_posts', 'trail-status-2', 'vstm_update');
	add_submenu_page($top_level_menu, 'Trail Status List', 'Trail List', 'publish_pages', 'trail-status-2-list', 'vstm_trail_list_page');
	add_submenu_page($top_level_menu, 'Trail Status Add', 'Add Trail', 'publish_pages', 'trail-status-2-add', 'vstm_trail_edit_page');
	add_submenu_page($top_level_menu, 'Trail Status Statuses', 'Status List', 'publish_pages', 'trail-status-2-statuses', 'vstm_status_list_page');
	add_submenu_page($top_level_menu, 'Trail Status Edit', '', 'publish_pages', 'trail-status-2-edit', 'vstm_trail_edit_page');
	add_submenu_page($top_level_menu, 'Trail Status Pending Approval List', 'Pending Approvals', 'publish_pages', 'trail-status-2-approve-list', 'vstm_trail_list_approval_page');
}

