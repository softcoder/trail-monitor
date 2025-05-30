<?php
/** Trail Management List View
 * @Package: 		com.vsoft.trailmonitor
 * @File			view/trail_list.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/
?>
<?php 
		if (!defined('ABSPATH')) {
			exit;
		}

		wp_enqueue_script( 'vstm-trail-list-script', plugins_url('/trail_list.js', __FILE__), array(), VSTM_VER, true);

		// ***** Load Models, Helpers and Libraries *****
		require_once(VSTM_ROOT_PATH . 'models/status_model.php');
		$vstm_Status_Model = new vstm_Status_Model();

		$status_lookup = array();
		// ***** Get Data *****
		$status_list = $vstm_Status_Model->get_list();

		// Build a Lookup table based on StatusID
		if (!empty($status_list)) {
			foreach ($status_list as $status) {
				$status_lookup[$status['status_id']] = $status['name'];
			}
		}

		$trail_unapproved_count = 0;
		if (!empty($table_data)) foreach ($table_data as $record) { 
			$status_name = (array_key_exists($record['status_id'], $status_lookup) ? $status_lookup[$record['status_id']] : '');
			//$comment = str_replace(array("\r\n", "\n", "\r"), ' ', nl2br( $record['comment']));
			$comment = $record['comment'];

			if($record['hidden']) {
				$trail_unapproved_count++;
			}

			wp_add_inline_script( 'vstm-trail-list-script', 'addTableData("' .
					esc_html($record['trail_id']) . '", "' . 
					esc_html($record['name'])     .	'", "' . 
					esc_html(gmdate("Y-m-d", strtotime($record['visitdate']))) . '", "' .
					esc_html(gmdate("Y-m-d", strtotime($record['created'])))   . '", "' . 
					esc_url($record['link'])            . '", "' .
					esc_js($comment)                    . '", "' .
					esc_html($record['submitter_name']) . '", "' . 
					esc_url(wp_get_attachment_thumb_url($record['image_id'])) . '", "' .
					esc_html($status_name)                                    . '", "' .
					vstm_display_yes_no(!$record['hidden'],true)     . '", "' . 
					esc_html($record['sort_order'])                           . '", "' .
					vstm_display_yes_no($record['show_widget'],true) . '", "' .
					vstm_display_yes_no($record['show_shortcode'],true) .
				'");' );
        } 

		wp_add_inline_script( 'vstm-trail-list-script', 'buildTableData();');
		?>

<div class="wrap">
	<h2>Trail Status | List &nbsp; <a href="admin.php?page=trail-status-2-add" class="add-new-h2">Add New</a> &nbsp; <a href="admin.php?page=trail-status-2-list" class="add-new-h2">Refresh</a></h2>
	<?php echo esc_html(vstm_display_messages($message_list)) ?>

	<b>Total trails needing approval: 
		<?php if($trail_unapproved_count > 0) { ?>
			<a href="admin.php?page=trail-status-2-approve-list" class="add-new-h2">
		<?php } ?>
		<?php echo esc_html($trail_unapproved_count) ?>
		<?php if($trail_unapproved_count > 0) { ?>
			</a>
		<?php } ?>
	</b><br>
	<form method="post" action="admin.php?page=trail-status-2-list">
		<?php wp_nonce_field('trails_bulk'); ?>
		<div class="tablenav">
			<select name="action" id="bulk-action-selector-top">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="trail_not_approved">Unapprove Trail</option>
				<option value="trail_approved">Approve Trail</option>
				<option value="hide_on_widget">Hide on Widget</option>
				<option value="show_on_widget">Show on Widget</option>
				<option value="hide_on_shortcode">Hide on Shortcode</option>
				<option value="show_on_shortcode">Show on Shortcode</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" id="doaction" class="button action" value="Apply">
		</div>
		
		<table id="table" class="vstm_table1">
			<thead><tr>
				<td><input id="cb-select-all-1" type="checkbox"></td>
				<td>Name</td>
				<td>Visit Date</td>
				<td>Create Date</td>
				<td>Link</td>
				<td>Comment</td>
				<td>Submitter</td>
				<td>Image</td>
				<td>Status</td>
				<td>Approved</td>
				<td>Sort Order</td>
				<td>Show on Widgets</td>
				<td>Show on Shortcodes</td>
			</tr></thead>
		</table>
		
		<div class="tablenav">
			<select name="action" id="bulk-action-selector-bottom">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="trail_not_approved">Trail Not Approved</option>
				<option value="trail_approved">Trail Approved</option>
				<option value="hide_on_widget">Hide on Widget</option>
				<option value="show_on_widget">Show on Widget</option>
				<option value="hide_on_shortcode">Hide on Shortcode</option>
				<option value="show_on_shortcode">Show on Shortcode</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" id="doaction" class="button action" value="Apply">
		</div>
	</form>
</div>