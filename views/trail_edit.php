<?php
/** Trail Record Edit View
 * @Package: 		com.vsoft.trailmonitor
 * @File			view/trail_edit.php
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
?>
<br>
<div class="wrap <?php	if (is_admin()) echo 'vstm_adminmain'; else echo 'wp-core-ui'; ?>">
	<h2>Trail Update | <?php if (!empty($record['trail_id'])) echo "Edit"; else echo "Add"; ?> Status</h2>
	<?php echo wp_kses_post(vstm_display_messages($message_list)) ?>
<?php
    wp_enqueue_script( 'vstm-trail-edit-script', plugins_url('../public/js/trail_edit.js', __FILE__), array(), VSTM_VER, true);

	// ***** Load Models, Helpers and Libraries *****
	require_once(VSTM_ROOT_PATH . 'models/status_model.php');
	$vstm_Status_Model = new vstm_Status_Model();

	// ***** Get Data *****
	$status_list = $vstm_Status_Model->get_list();
?>
	<form name="form1" id="form1" method="post" class="vstm_form1" style="display: inline-block; max-width: 550px;" onsubmit="return verifyRecaptchaNotEmpty()" enctype="multipart/form-data">

<?php if (empty($record['trail_id'])) wp_nonce_field('trail_add'); else wp_nonce_field('trail_edit_' . $record['trail_id']); ?>
<?php if (!empty($record['trail_id'])) { ?>
		<input type="hidden" name="trail_id" value="<?php echo esc_html($record['trail_id']) ?>">
<?php } ?>		
		<input type="hidden" id="image_id" name="image_id" value="<?php echo esc_html($record['image_id']) ?>">
		<p>
			<label>*Trail Name:</label>
			<input type="text" name="trail_name" id="trail_name" maxlength="<?php echo esc_html(VSTM_MAX_FIELD_LENGTH_NAME) ?>" value="<?php echo esc_html($record['name']) ?>" list="trailName" required="required">
			<datalist id="trailName">
<?php
			$options = get_option( 'vstm_options' );
			$selected_category_id = isset( $options[ 'vstm_field_post_category' ] ) ? $options[ 'vstm_field_post_category' ] : -1;

			$arguments = array(
				"numberposts" => -1,
				"category" => $selected_category_id,
				"orderby" => "post_title",
				"order" => "ASC"
			);
			$post_list = get_posts($arguments);
			foreach($post_list as $post) { ?>
				<option value="<?php echo esc_html($post->post_title) ?>" trailurl="<?php echo esc_url(get_permalink($post->ID)) ?>"><?php echo esc_html($post->post_title) ?></option>
<?php		} ?>
			</datalist>
		</p>
		<p>
			<label>*Trail Hike Date:</label>
			<input type="date" name="visitdate" id="visitdate" 
<?php 		if (!empty($record['visitdate'])) { ?>			
				value="<?php echo esc_html(gmdate("Y-m-d", strtotime($record['visitdate']))) ?>" 
<?php		} ?>			
			required="required">
		</p>
		<p>
			<label>Trail Detail Link:</label>
			<input type="text" name="link" id="link" maxlength="<?php echo esc_html(VSTM_MAX_FIELD_LENGTH_LINK) ?>" value="<?php echo esc_html($record['link']) ?>">
		</p>
		<p>
			You may embed youtube videos in the comment using the format below:<br>
			[vstm-trail-status-youtube width="300" height="300" src="https://youtu.be/E8x8VqCPn5g"]<br><br>
			<label>Comment:</label>
			<textarea name="comment" maxlength="<?php echo esc_attr(VSTM_MAX_FIELD_LENGTH_COMMENT) ?>"><?php echo esc_textarea($record['comment']) ?></textarea>
		</p>		
		<p>
			<label>Submitter (Your Name):</label>
			<input type="text" name="submitter_name" maxlength="<?php echo esc_html(VSTM_MAX_FIELD_LENGTH_SUBMITTER) ?>" value="<?php echo esc_html($record['submitter_name']) ?>">
		</p>		
		<p>
			<label>Recent Photo:</label>
			<?php	if (is_admin()) { ?>
			<input type="button" name="image_button" id="upload-btn" class="button-secondary" value="Set Image">
			<?php	} else { 
				$max_upload_size = wp_max_upload_size();
				if ( ! $max_upload_size ) {
					$max_upload_size = 0;
					$max_upload_size_in_mb = 0;
				}
				else {
					$max_upload_size_in_mb = round($max_upload_size / 1024 / 1024);
				}
				?>
			<p><span style="color:red;">Maximum photo upload size: <?php echo esc_html($max_upload_size_in_mb) ?> MB</span></p>
			<p><span id="image_upload_size" name="image_upload_size" style="color:blue;"></span></p>
			<input type="file" name="image_upload" id="image_upload" class="button-secondary" value="Set Image">
			<?php	} ?>
		</p>
		<p>
			<label>*Trail Status:</label>
			<select name="status_id" id="status_id" required="required">
<?php 			if (!empty($status_list)) foreach ($status_list as $record_status) { ?>
				<option value="<?php echo esc_html($record_status['status_id']) ?>" 
<?php 				if (!empty($record['status_id']) && $record['status_id'] == $record_status['status_id']) { ?>
					selected="selected"
<?php				} ?>					
					><?php echo esc_html($record_status['name']) ?></option>
<?php			} ?>
			</select>
		</p>

<?php	if (is_admin()) { ?>
		<p>
			<label for="trail_approved">Trail Submission Approved:</label>
			<input type="checkbox" id="trail_approved" name="trail_approved" value="1" <?php if (!$record['hidden']) echo wp_kses_post($checked_text) ?>>
		</p>
	<p>
			<label>Sort Order:</label>
			<input type="number" name="sort_order" maxlength="20" value="<?php echo esc_html($record['sort_order']) ?>">
		</p>
		<p>
			<label for="show_widget">Show on Widgets:</label>
			<input type="checkbox" id="show_widget" name="show_widget" value="1" <?php if ($record['show_widget']) echo wp_kses_post($checked_text) ?>>
		</p>
		<p>
			<label for="show_shortcode">Show on Shortcodes:</label>
			<input type="checkbox" name="show_shortcode" value="1" <?php if ($record['show_shortcode']) echo wp_kses_post($checked_text) ?>>
		</p>
<?php	} else { ?>
		<input type="hidden" name="sort_order" value="<?php echo esc_html($record['sort_order']) ?>">
		<input type="hidden" id="show_widget" name="show_widget" value="<?php if ($record['show_widget']) echo '1'; ?>">
		<input type="hidden" name="show_shortcode" value="<?php if ($record['show_shortcode']) echo '1'; ?>">
<?php	} ?>

		<p style="text-align: center;">
			<input type="submit" class="button-primary" value="Save" id="submit-button" name="submit-button">
<?php	if (is_admin()) { ?>
			<a href="admin.php?page=trail-status-2-list" class='button-primary' style="margin-left: 17px;">Back to List</a>
<?php	} ?>
		</p>
<?php
		// When user selects a trail name, auto populate the link to that trail if the link field is blank
		wp_add_inline_script( 'vstm-trail-edit-script', 'autoPopulateTrailLink()' );
		wp_add_inline_script( 'vstm-trail-edit-script', 'checkImageUploadeSize()' );
?>		
		

<?php   if (!is_admin()) {
			$options = get_option( 'vstm_options' );
			$site_key = isset( $options[ 'vstm_google_recaptcha_api_key' ] ) ? $options[ 'vstm_google_recaptcha_api_key' ] : '';
			if( !empty($site_key)) { ?>		
				<div class="g-recaptcha" data-sitekey="<?php echo esc_html($site_key) ?>"></div>
<?php		}
		} ?>

	</form>

<?php
if (!empty($record['image_id'])) {
	$image_thumb_url = wp_get_attachment_thumb_url($record['image_id']);
	$image_alt = htmlspecialchars(get_post_meta($record['image_id'], '_wp_attachment_image_alt', true));
?>
	<img id="vstm_trail_image" src="<?php echo esc_url($image_thumb_url) ?>" alt="<?php echo esc_html($image_alt) ?>" style="display: inline-block; vertical-align: top;  margin: 31px; width: 150px; height: 150px; box-shadow: 4px 4px 4px #555;">
<?php } ?>	

</div>