<?php
/** Submitted Data Validation Helper, OTG WP Plugins Common File
 * @Package: 		com.vsoft.trailmonitor
 * @File			helpers/validation_helper.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/

/** Cleans and Checks an Email Address
 * @param string $in
 * @return array
 */
function vstm_validate_email ($in) {
	if (empty($in))
		return ['email' => '', 'valid' => false, 'message' => 'Email address needs to be submitted.'];

	$valid = true;
	$message = '';

	if (!$email_domain = stristr($in, '@')) {
		$message = 'Email address needs a "@"!';
		$valid = false;
	} elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-]).*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $in)) {
		$message = 'Email address is missing something!';
		$valid = false;
	} elseif (!stristr($email_domain, '.')) {
		$message = 'Email address domain needs to be valid!';
		$valid = false;
	} elseif (!filter_var($in, FILTER_VALIDATE_EMAIL)) {
		$message = 'Email address needs to be valid!';
		$valid = false;
	}

	return ['valid' => $valid, 'message' => $message];
}
