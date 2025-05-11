<?php
/** Helper Functions and Variables for Views
 * @Package: 		com.vsoft.trailmonitor
 * @File			helpers/view_helper.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/

$checked_text = ' checked="checked"';
$selected_text = ' selected="selected"';

/** Builds a On/Off Select
 * @param string $name
 * @param int $default
 */
function vstm_on_off_select ($name, $default) {
	$selected_text = ' selected="selected"';
	echo "<select name='".esc_html($name)."'>";
	echo '<option value="1"';
	if (1 == $default) echo wp_kses_post($selected_text);
	echo ">On</option>";
	echo '<option value="0"';
	if (0 == $default) echo wp_kses_post($selected_text);
	echo ">Off</option>";
	echo "</select>";
}

/** Builds a On/Off Select
 * @param string $name
 * @param int $default
 */
function vstm_yes_no_select ($name, $default) {
	$selected_text = ' selected="selected"';
	echo "<select name='".esc_html($name)."'>";
	echo '<option value="1"';
	if (1 == $default) echo wp_kses_post($selected_text);
	echo ">Yes</option>";
	echo '<option value="0"';
	if (0 == $default) echo wp_kses_post($selected_text);
	echo '>No</option>';
	echo '</select>';
}

/** Returns a True/False as a Yes/nNo in Color
 * @param boolean $value
 * @return string
 */
function vstm_display_yes_no ($value) {
	if ($value)
		return '<span style="color: green">Yes</span>';
	else
		return '<span style="color: red">No</span>';
}

/** Takes Messages and Displays Them
 * @param array $message_list
 * @return boolean (false if empty)
 */
function vstm_display_messages ($message_list) {
	// ***** End if Empty *****
	if (empty($message_list)) return false;

	// ***** Order by Third Field *****
	usort($message_list, function($a, $b) {
		 return $a[2] - $b[2];
	});
	foreach ($message_list as $message) {
		// ***** Set Class Second Field *****
		switch ($message[1]) {
			case 1:
				$class = 'vstm_success';
				break;
			case 2:
				$class = 'vstm_warning';
				break;
			case 3:
				$class = 'vstm_error';
				break;
			default:
				$class = 'vstm_message';
		}
		// ***** Print It *****
		echo "<p class='".wp_kses_post($class)."'>" . esc_html($message[0]) . '</p>';
	}
}
