<?php
/** List View
 * @Package: 		com.vsoft.trailmonitor
 * @File			view/update.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/
?>
<script>
	jQuery(function() {
		jQuery(".vstm_trail_status").checkboxradio({
			icon: false
		 });
	});
</script>
<div class="wrap vstm_adminmain">
	<h2>Trail Status | Update Status</h2>
	<?= vstm_display_messages($message_list) ?>

	<form method="post">
		<input type="submit" class="button-primary" value="<?= 'Save Changes' ?>">
		<?php wp_nonce_field('update'); ?>
<?php if (!empty($trail_list)) foreach ($trail_list as $trail) { ?>
		<p>
			<span class="vstm_trail_name"><?= htmlspecialchars($trail['name']) ?></span>
	<?php foreach ($status_list as $status) { ?>
			<label for="vstm_<?= $trail['trail_id'] . '_' . $status['status_id'] ?>" class="vstm_trail_status_l"><?= htmlspecialchars($status['name']) ?></label>
			<input type="radio" id="vstm_<?= $trail['trail_id'] . '_' . $status['status_id'] ?>" name="t_<?= $trail['trail_id'] ?>" value="<?= $status['status_id'] ?>" style="margin-left: 7px;"<?php if ($status['status_id'] == $trail['status_id']) echo $checked_text ?> class="vstm_trail_status">
	<?php } ?>
		</p>
<?php } ?>
		<p class="vstm_form1">
			<label>Notes for Shortcode<br>(HTML Allowed)</label>
			<textarea name="vstm_notes_sc"><?= $notes_sc ?></textarea>
		</p>
		<p class="vstm_form1">
			<label>Notes for Widget<br>(HTML Allowed, 150 Characters or less)</label>
			<textarea name="vstm_notes_widget" maxlength="350" style="height: 75px;"><?= htmlspecialchars($notes_widget) ?></textarea>
		</p>

		<input type="submit" class="button-primary" value="Save Changes">
	</form>

</div>