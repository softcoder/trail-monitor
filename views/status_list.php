<?php
/** List View
 * @Package: 		com.vsoft.trailmonitor
 * @File			view/status_list.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/
?>
<script>
jQuery(document).ready(function () {
	var tableData = [
<?php if (!empty($status_list)) foreach ($status_list as $record) {
	$style = '';
	if (!empty($record['color'])) $style = ' style="color: ' . $record['color'] . '"';?> 
		[
			'<input type="checkbox" name="bulk_action_list[]" value="<?php echo esc_html($record['status_id']) ?>" class="vstm_list_checkbox">',
			'<span id="vstm_inline_no_edit_<?php echo esc_html($record['status_id']) ?>_name"><?php echo esc_html($record['name']) ?></span>'
				+ '<input id="vstm_inline_edit_<?php echo esc_html($record['status_id']) ?>_name" value="<?php echo esc_html($record['name']) ?>" maxlength="50">',
			'<span id="vstm_inline_no_edit_<?php echo esc_html($record['status_id']) ?>_sort_order"><?php echo esc_html($record['sort_order']) ?></span>'
				+ '<select id="<?php echo esc_html('vstm_inline_edit_' . $record["status_id"] . '_sort_order') ?>" name="<?php echo esc_html('vstm_inline_edit_' . $record["status_id"] . '_sort_order') ?>" class="">'
			<?php	for ($i = 1; $i < 20 + 1; $i++) {
						if ($record['sort_order'] == $i) { ?>
						+ '<option value="<?php echo esc_attr($i) ?>" selected="selected"><?php echo esc_attr($i) ?></option>'
			<?php		} else { ?>
						+ '<option value="<?php echo esc_attr($i) ?>"><?php echo esc_attr($i) ?></option>'
			<?php	    }  
		            } ?>
				+ '</select>',
			'<span id="vstm_inline_no_edit_<?php echo esc_html($record['status_id']) ?>_color"<?php echo wp_kses_post($style) ?>><?php echo wp_kses_post($record['color']) ?></span>'
				+ '	<div id="vstm_inline_edit_<?php echo esc_html($record['status_id']) ?>_cbox"><input id="vstm_<?php echo esc_html($record['status_id']) ?>_color" value="<?php echo wp_kses_post($record['color']) ?>" class="vstm_colorpicker"></div>',
			'<a href="javascript:void(0)" onclick="showInlineEditableFields(<?php echo esc_html($record['status_id']) ?>)" id="vstm_inline_no_edit_<?php echo esc_html($record['status_id']) ?>_edit" class="vstm_intable_button">Edit</a>'
				+	'<a href="javascript:void(0)" onclick="saveInlineEditableFields(<?php echo esc_html($record['status_id']) ?>)" id="vstm_inline_edit_<?php echo esc_html($record['status_id']) ?>_save" class="vstm_intable_button">Save</a>'
				+	'<a href="javascript:void(0)" onclick="hideInlineEditableFields(<?php echo esc_html($record['status_id']) ?>)" id="vstm_inline_edit_<?php echo esc_html($record['status_id']) ?>_cancel" class="vstm_intable_button">Cancel</a>'
		],
<?php } ?>
	];
    jQuery("#table").DataTable( {
		data: tableData,
		autoWidth: false,
		pageLength: 25,
		stateSave: true,
		columnDefs: [ 
			{orderable: false, targets: [0, 4]}
		],
		order: [[1, "asc"]]
	});
});
</script>
<div class="wrap vstm_adminmain">
	<h2>Trail Status | Status List &nbsp; <a href="#vstm_add" class="add-new-h2">Add New</a></h2>
	<?php echo wp_kses_post(vstm_display_messages($message_list)) ?>

<?php // ***** Current Status List ***** ?>	
	<form method="post" action="admin.php?page=trail-status-2-statuses" style="max-width: 600px;">
		<?php wp_nonce_field('status_bulk'); ?>

		<table id="table" class="vstm_table1">
			<thead><tr>
				<td><input id="cb-select-all-1" type="checkbox"></td>
				<td>Name</td>
				<td>Sort Order</td>
				<td>Color</td>
				<td></td>
			</tr></thead>
		</table>
		
		<div class="tablenav">
			<select name="action" id="bulk-action-selector-bottom">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" id="doaction" class="button action" value="Apply">
		</div>
	</form>
		
<?php
// ***** Add New Status Form if Under 20 *****
if (20 > sizeof($status_list)) {
?>
	<form id="vstm_add" method="post" action="admin.php?page=trail-status-2-statuses" class="vstm_form1">
		<?php wp_nonce_field('status_add'); ?>
		<input type="hidden" name="action" value="add">

		<h3>Add a New Status</h3>
		<p>There is a limit of 20.</p>
		<p>
			<label for="vstm_name">Name</label>
			<input id="vstm_name" name="vstm_name" type="text" maxlength="50">
		</p>
		<p>
			<label for="vstm_sort_order">Sort Order</label>
			<select id="vstm_sort_order" name="vstm_sort_order">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
			</select>
		</p>
		<p>
			<label for="vstm_color">Color (hex code)</label>
			<input id="vstm_color" name="vstm_color" type="text" maxlength="20">
		</p>
		<p>
			<input type="submit" value="Add It" class="button-primary">
		</p>
	</form>
<?php } else { ?>
	<h3>Max of 20 Statuses Added</h3>
<?php } ?>	
</div>