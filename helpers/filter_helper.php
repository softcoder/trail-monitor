<?php
/** Submitted Data Filter Helper, OTG WP Plugins Common File
 * @Package: 		com.vsoft.trailmonitor
 * @File			helpers/filter_helper.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/

/** Gets and Cleans a Post Value
 * @param string $field
 * @param string $default
 * @param boolean $allow_tags
 * @return string
 */
function vstm_get_request_string ($field, $default=null, $allow_tags = false) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_string(wp_unslash($_REQUEST[$field]), false, $allow_tags, $default);
}

/** Gets and Cleans a Textarea Post
 * @param string $field
 * @param string $default
 * @return string|null
 */
function vstm_get_request_texarea ($field, $default = null) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_string(wp_unslash($_REQUEST[$field]), true, false, $default);
}

/** Checks the Variable and Returns It as an Integer or Null
 * @param string $field
 * @param int $default
 * @return int|null
 */
function vstm_get_request_int ($field, $default=null) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_int_strict($_REQUEST[$field], $default);
}

/** Checks the Submitted Parameters and Returns It as a Float
 * @param string $field
 * @param float $default
 * @return float
 */
function vstm_get_request_float ($field, $default = null) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_float($_REQUEST[$field], $default);
}

/** Gets and Cleans a Email Post Value
 * @param string $field
 * @param string $default
 * @return string|null
 */
function vstm_get_request_email ($field) {
	if (!isset($_REQUEST[$field])) return ['email' => '', 'valid' => false];
	return vstm_filter_email($_REQUEST[$field]);
}

/** Gets and Cleans a URL Value
 * @param string $field
 * @param string $default
 * @return string|null
 */
function vstm_get_request_link ($field, $default = null) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_link($_REQUEST[$field]);
}

/** Gets and Cleans a Boolean Post
 * @param string $field
 * @param string $default
 * @return string|null
 */
function vstm_get_request_boolean ($field, $default = null) {
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_boolean ($_REQUEST[$field], $default);
}

/** Get the Bulk Action List and Only Allows Integers in the List
 * @param string $field
 * @return array
 */
function vstm_get_request_str_array ($field) {
	if (!isset($_REQUEST[$field])) return array();
	return vstm_filter_str_array($_REQUEST[$field]);
}

/** Get the Bulk Action List and Only Allows Integers in the List
 * @return array
 */
function vstm_get_request_int_array ($field = 'bulk_action_list') {
	if (!isset($_REQUEST[$field])) return array();
	return vstm_filter_int_array($_REQUEST[$field]);
}

/** Cleans a String Value
 * @param string $in
 * @param boolean $allow_new_line
 * @param boolean $allow_tags (PHP and HTML Tags)
 * @param string $default
 * @return string
 */
function vstm_filter_string ($in, $allow_new_line=false, $allow_tags=false, $default=null) {
	if ($allow_new_line)
		$out = trim(filter_var($in, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH));
	else
		$out = trim(filter_var($in, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW));
	if (!$allow_tags)
		$out = strip_tags($out);

	if (empty($out))
		$out = $default;

	return $out;
}

/** Cleans an Array of String Values and Their Keys
 * @param array $in
 * @return array
 */
function vstm_filter_str_array ($in) {
	$out = array();
	if (!empty($in)) foreach ($in as $key => $value) {
		$out[trim(filter_var(wp_unslash($key), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW))] = trim(filter_var(wp_unslash($value), FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW));
	}
	return $out;
}

/** Cleans and Checks an Email Address
 * @param string $in
 * @return array
 */
function vstm_filter_email ($in) {
	return filter_var(trim($in), FILTER_SANITIZE_EMAIL);
}

/** Gets and Cleans a URL Value
 * Adds in http:// if missing
 * @param string $in
 * @param string $default
 * @return string|null
 */
function vstm_filter_link ($in) {
	$link = trim($in);
	if (0 != strncasecmp($link, "http://", 7) && 0 != strncasecmp($link, "https://", 8))
		$link = 'http://' . $link;
	return filter_var($link, FILTER_SANITIZE_URL);
}

/** Cleans Submitted Value and Returns It As a Integer
 * Fraction portion removed then everything except numbers, + & - are removed.
 * @param int $in
 * @param int $default
 * @return int|null
 */
function vstm_filter_int ($in) {
	$filtered = preg_replace('/(\..*)/', '', $in);
	return (int)filter_var($filtered, FILTER_SANITIZE_NUMBER_INT);
}

/** Filters the Values in an Array into Integers
 * Keys are not changed. Fraction portion removed then everything except numbers, + & - are removed.
 * @return array
 */
function vstm_filter_int_array ($in) {
	$out = array();
	if (!empty($in)) foreach ($in as $key => $value) {
		$out[$key] = (int)filter_var(preg_replace('/(\..*)/', '', $value), FILTER_SANITIZE_NUMBER_INT);
	}
	return $out;
}

/** Checks Submitted Variable and Returns It as an Integer or Null
 * If the submitted value is not an integer or integer in string type, default is returned
 * @param int $in
 * @param int $default
 * @return int|null
 */
function vstm_filter_int_strict ($in, $default=null) {
	if (0 === intval($in))
		return 0;
	$value = trim($in);
	if (empty($value))
		return $default;

	if (!is_numeric($value) && !is_int($value)) {
		if (is_int($default) || is_numeric($default))
			$out = $default;
		else
			$out = null;
	} else {
		$out = (int)$value;
	}
	return $out;
}

/** Checks Submitted Keys and Variables and Returns Valid Pairs
 * Only allows integers or integers in string type
 * @param array $in
 * @return array
 */
function vstm_filter_int_strict_array ($in) {
	$out = array();
	if (!empty($in)) foreach ($in as $key => $value) {
		if ((is_int($key) || ctype_digit($key)) && (is_int($value) || ctype_digit($value))) {
			$out[$key] = (int)$value;
		}
	}
	return $out;
}

/** Removes Everything Except Numbers, +, - & . and Returns the Float Value
 * Anything after a second . is removed
 * @param string $in
 * @param float $default
 * @return float
 */
function vstm_filter_float ($in, $default=null) {
	$out = filter_var($in, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

	if (0.0 != $out && empty($out))
		return $default;

	return (float)$out;
}

/** Gets and Cleans a Variable to Boolean
 * Returns TRUE for 1, "1", "true", "on" and "yes" . Returns FALSE for 0, "0", "false", "off" and "no".
 * @param string $in
 * @param string $default
 * @return string|null
 */
function vstm_filter_boolean ($in, $default = null) {
	$out = filter_var($in, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
	if (null === $out)
		return $default;

	return $out;
}
