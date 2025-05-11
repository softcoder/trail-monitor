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
			'<input type="checkbox" name="bulk_action_list[]" value="<?= $record['status_id'] ?>" class="vstm_list_checkbox">',
			'<span id="vstm_inline_no_edit_<?= $record['status_id'] ?>_name"><?= htmlspecialchars($record['name']) ?></span>'
				+ '<input id="vstm_inline_edit_<?= $record['status_id'] ?>_name" value="<?= htmlspecialchars($record['name']) ?>" maxlength="50">',
			'<span id="vstm_inline_no_edit_<?= $record['status_id'] ?>_sort_order"><?= $record['sort_order'] ?></span>'
				+	'<?= vstm_number_select('vstm_inline_edit_' . $record['status_id'] . '_sort_order', 1, 20, $record['sort_order']) ?>',
			'<span id="vstm_inline_no_edit_<?= $record['status_id'] ?>_color"<?= $style ?>><?= $record['color'] ?></span>'
				+ '	<div id="vstm_inline_edit_<?= $record['status_id'] ?>_cbox"><input id="vstm_<?= $record['status_id'] ?>_color" value="<?= $record['color'] ?>" class="vstm_colorpicker"></div>',
			'<a href="javascript:void(0)" onclick="showInlineEditableFields(<?= $record['status_id'] ?>)" id="vstm_inline_no_edit_<?= $record['status_id'] ?>_edit" class="vstm_intable_button">Edit</a>'
				+	'<a href="javascript:void(0)" onclick="saveInlineEditableFields(<?= $record['status_id'] ?>)" id="vstm_inline_edit_<?= $record['status_id'] ?>_save" class="vstm_intable_button">Save</a>'
				+	'<a href="javascript:void(0)" onclick="hideInlineEditableFields(<?= $record['status_id'] ?>)" id="vstm_inline_edit_<?= $record['status_id'] ?>_cancel" class="vstm_intable_button">Cancel</a>'
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
	<?= vstm_display_messages($message_list) ?>

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
			<?= vstm_select_input_sort_order('vstm_sort_order') ?>
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