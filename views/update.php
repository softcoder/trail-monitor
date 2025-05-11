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
	<?php echo wp_kses_post(vstm_display_messages($message_list)) ?>

	<form method="post">
		<input type="submit" class="button-primary" value="<?php echo 'Save Changes' ?>">
		<?php wp_nonce_field('update'); ?>
<?php if (!empty($trail_list)) foreach ($trail_list as $trail) { ?>
		<p>
			<span class="vstm_trail_name"><?php echo esc_html($trail['name']) ?></span>
	<?php foreach ($status_list as $status) { ?>
			<label for="vstm_<?php echo esc_html($trail['trail_id'] . '_' . $status['status_id']) ?>" class="vstm_trail_status_l"><?php echo esc_html($status['name']) ?></label>
			<input type="radio" id="vstm_<?php echo esc_html($trail['trail_id'] . '_' . $status['status_id']) ?>" name="t_<?php echo esc_html($trail['trail_id']) ?>" value="<?php echo esc_html($status['status_id']) ?>" style="margin-left: 7px;"<?php if ($status['status_id'] == $trail['status_id']) echo wp_kses_post($checked_text) ?> class="vstm_trail_status">
	<?php } ?>
		</p>
<?php } ?>
		<p class="vstm_form1">
			<label>Notes for Shortcode<br>(HTML Allowed)</label>
			<textarea name="vstm_notes_sc"><?php echo esc_textarea($notes_sc) ?></textarea>
		</p>
		<p class="vstm_form1">
			<label>Notes for Widget<br>(HTML Allowed, 150 Characters or less)</label>
			<textarea name="vstm_notes_widget" maxlength="350" style="height: 75px;"><?php echo esc_textarea($notes_widget) ?></textarea>
		</p>

		<input type="submit" class="button-primary" value="Save Changes">
	</form>

</div>