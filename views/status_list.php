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

<?php 
	if (!defined('ABSPATH')) {
		exit;
	}

	wp_enqueue_script( 'vstm-status-list-script', plugins_url('/status_list.js', __FILE__), array(), VSTM_VER, true); 
?>

<?php 
	if (!empty($status_list)) foreach ($status_list as $record) {
		
		$style = '';
		if (!empty($record['color'])) 
			//$style = ' style="color: ' . $record['color'] . '"';
			$style = " style='color: " . $record['color'] . "'";

		//status_id, trail_name, sort_order, style
		wp_add_inline_script( 'vstm-status-list-script', 'addTableData("' .
					esc_html($record['status_id']) . '", "' . 
					esc_html($record['name'])     .	'", "' . 
					esc_html($record['sort_order']) . '", "' .
					wp_kses_post($style) . '", "' .
					wp_kses_post($record['color']) .
			'");' );
	} 

	wp_add_inline_script( 'vstm-status-list-script', 'buildTableData();');
?>
	
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