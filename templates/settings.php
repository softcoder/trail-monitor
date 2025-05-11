<?php
/** Settings Page
 * @Package: 		com.vsoft.trailmonitor
 * @File			templates/settings.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/
?>
<?php
/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function vstm_settings_init() {
	// Register a new setting for "vstm" page.
	register_setting( 'vstm', 'vstm_options' );

	// Register a new section in the "vstm" page.
	add_settings_section('vstm_section_settings', __( 'Settings', 'Trail Monitor' ), 'vstm_section_settings_callback','vstm');

	// Register a new field in the "vstm_section_settings" section, inside the "vstm" page.
	add_settings_field('vstm_field_post_category',
		__( 'Hiking Post Category', 'Trail Monitor' ),
		'vstm_field_post_category_cb', 'vstm', 'vstm_section_settings',
		array(
			'label_for'        => 'vstm_field_post_category',
			'class'            => 'wporg_row',
			'vstm_custom_data' => 'custom',
		)
	);
    
	add_settings_field('vstm_google_recaptcha_api_key',
		__( 'Google Recaptcha Site API Key', 'Trail Monitor' ),
		'vstm_field_google_recaptcha_api_key_cb', 'vstm', 'vstm_section_settings',
		array(
			'label_for'        => 'vstm_google_recaptcha_api_key',
			'class'            => 'wporg_row',
			'vstm_custom_data' => 'custom',
		)
	);

	add_settings_field('vstm_google_recaptcha_api_secret_key',
		__( 'Google Recaptcha Secret API Key', 'Trail Monitor' ),
		'vstm_field_google_recaptcha_api_secret_key_cb', 'vstm', 'vstm_section_settings',
		array(
			'label_for'        => 'vstm_google_recaptcha_api_secret_key',
			'class'            => 'wporg_row',
			'vstm_custom_data' => 'custom',
		)
	);

	add_settings_field('vstm_notifications_email_send_from',
		__( 'Send Notifications FROM Email Address', 'Trail Monitor' ),
		'vstm_field_notifications_email_send_from_cb', 'vstm', 'vstm_section_settings',
		array(
			'label_for'        => 'vstm_notifications_email_send_from',
			'class'            => 'wporg_row',
			'vstm_custom_data' => 'custom',
		)
	);

	add_settings_field('vstm_notifications_email_send_to',
		__( 'Send Notifications to Email Address', 'Trail Monitor' ),
		'vstm_field_notifications_email_send_to_cb', 'vstm', 'vstm_section_settings',
		array(
			'label_for'        => 'vstm_notifications_email_send_to',
			'class'            => 'wporg_row',
			'vstm_custom_data' => 'custom',
		)
	);

}

/**
 * Register our vstm_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'vstm_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function vstm_section_settings_callback( $args ) {

//    echo '<div id="debug1">';
//    print_r($args);
//    echo '</div>';
}

/**
 * Post Category field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function vstm_field_post_category_cb( $args ) {

//    echo '<div id="debug2">';
//    print_r($args);
//    echo '</div>';

	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'vstm_options' );

//    echo '<div id="debug3">';
//    print_r($options);
//    echo '</div>';

?>
    <label>*Hiking Trail 'Category Type' to Monitor Statuses:</label>
    <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
			data-custom="<?php echo esc_attr( $args['vstm_custom_data'] ); ?>"
			name="vstm_options[<?php echo esc_attr( $args['label_for'] ); ?>]" required="required">
<?php

    $selected_category_id = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : -1;
    $categories = get_categories( array(
        'orderby' => 'name',
        'order'   => 'ASC'
    ) );

    foreach( $categories as $category ) { 
?>
        <option value="<?= $category->term_id ?>"
<?php   if ($category->term_id == $selected_category_id) { ?>
		    selected="selected"
<?php	} ?>					
        ><?= $category->name ?>
        </option>
<?php }	?>
    </select>
<?php
}

function vstm_field_google_recaptcha_api_key_cb( $args ) {
        $options = get_option( 'vstm_options' );
        $google_recaptcha_api_key = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '';
    ?>
        <label>Google Recaptcha API Key v2 (anti-spam):</label>
        <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
                data-custom="<?php echo esc_attr( $args['vstm_custom_data'] ); ?>"
                name="vstm_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
                value="<?= $google_recaptcha_api_key ?>">
    <?php
}

function vstm_field_google_recaptcha_api_secret_key_cb( $args ) {
    $options = get_option( 'vstm_options' );
    $google_recaptcha_api_secret_key = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '';
?>
    <label>Google Recaptcha API Secret Key v2 (anti-spam):</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['vstm_custom_data'] ); ?>"
            name="vstm_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
            value="<?= $google_recaptcha_api_secret_key ?>">
<?php
}

function vstm_field_notifications_email_send_from_cb( $args ) {
    $options = get_option( 'vstm_options' );
    $notifications_email_send_from = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '';
?>
    <label>Send Notifications 'FROM' Email Address:</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['vstm_custom_data'] ); ?>"
            name="vstm_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
            value="<?= $notifications_email_send_from ?>"><br>
    <label>Trail Monitor will send notifications from this email address. Leave blank to use the WordPress default.</label>
<?php
}

function vstm_field_notifications_email_send_to_cb( $args ) {
    $options = get_option( 'vstm_options' );
    $notifications_email_send_to = isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '';
?>
    <label>Send Notifications 'TO' Email Address:</label>
    <input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['vstm_custom_data'] ); ?>"
            name="vstm_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
            value="<?= $notifications_email_send_to ?>">
<?php
}

/**
 * Add the top level menu page.
 */
function vstm_options_page() {
	add_menu_page('VSTM', 'Trail Monitor Settings', 'manage_options', 'vstm', 'vstm_options_page_html');
}

/**
 * Register our vstm_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'vstm_options_page' );

/**
 * Top level menu callback function
 */
function vstm_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'vstm_messages', 'vstm_message', __( 'Settings Saved', 'vstm' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'vstm_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "vstm"
			settings_fields( 'vstm' );
			// output setting sections and their fields
			// (sections are registered for "vstm", each field is registered to a specific section)
			do_settings_sections( 'vstm' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}
?>