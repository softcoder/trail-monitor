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
 * @param string $field - name of the field to extract
 * @param string $default - the default value to return if null
 * @param boolean $allow_tags
 * @param string $verify_nonce_type - null (none), non-admin = non-admin user, admin = admin user
 * @param string $verify_nonce_value - the nonce request/post value to check
 * @param string $verify_nonce_action - the nonce action name to check
 * @return string
 */
function vstm_get_request_string ($field, $default=null, $allow_tags = false, 
                                  $verify_nonce_type=null, $verify_nonce_value=null, $verify_nonce_action=null) {
	if(isset($verify_nonce_type)) {
		if($verify_nonce_type == 'admin') {
			check_admin_referer( $verify_nonce_action );
		}
		else if($verify_nonce_type == 'admin-ajax') {
			check_ajax_referer($verify_nonce_action, 'wp_nonce');
		}
		else {
			if(!wp_verify_nonce( $verify_nonce_value, $verify_nonce_action)) {
				// wp_nonce_ays($verify_nonce_action)
				wp_die( esc_html(__( 'Security check', 'vstm-trail-monitor' )) ); 
			}
		}
	}
	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_string(sanitize_text_field(wp_unslash($_REQUEST[$field])), false, $allow_tags, $default);
}

/** Checks the Variable and Returns It as an Integer or Null
 * @param string $field
 * @param int $default
 * @param string $verify_nonce_type - null (none), non-admin = non-admin user, admin = admin user
 * @param string $verify_nonce_value - the nonce request/post value to check
 * @param string $verify_nonce_action - the nonce action name to check
 * @return int|null
 */
function vstm_get_request_int ($field, $default=null,
                               $verify_nonce_type=null, $verify_nonce_value=null, $verify_nonce_action=null) {
	if(isset($verify_nonce_type)) {
		if($verify_nonce_type == 'admin') {
			check_admin_referer( $verify_nonce_action );
		}
		else if($verify_nonce_type == 'admin-ajax') {
			check_ajax_referer($verify_nonce_action, 'wp_nonce');
		}
		else {
			if(!wp_verify_nonce( $verify_nonce_value, $verify_nonce_action)) {
				// wp_nonce_ays($verify_nonce_action)
				wp_die( esc_html(__( 'Security check', 'vstm-trail-monitor' )) ); 
			}
		}
	}

	if (!isset($_REQUEST[$field])) return $default;
	return vstm_filter_int_strict(sanitize_text_field(wp_unslash($_REQUEST[$field])), $default);
}

/** Get the Bulk Action List and Only Allows Integers in the List
 * 
 * @param string $field - name of the field to extract
 * @param string $verify_nonce_type - null (none), non-admin = non-admin user, admin = admin user
 * @param string $verify_nonce_value - the nonce request/post value to check
 * @param string $verify_nonce_action - the nonce action name to check

 * @return array
 */
function vstm_get_request_int_array ($field = 'bulk_action_list',
                                     $verify_nonce_type=null, $verify_nonce_value=null, $verify_nonce_action=null) {

	if(isset($verify_nonce_type)) {
		if($verify_nonce_type == 'admin') {
			check_admin_referer( $verify_nonce_action );
		}
		else if($verify_nonce_type == 'admin-ajax') {
			check_ajax_referer($verify_nonce_action, 'wp_nonce');
		}
		else {
			if(!wp_verify_nonce( $verify_nonce_value, $verify_nonce_action)) {
				// wp_nonce_ays($verify_nonce_action)
				wp_die( esc_html(__( 'Security check', 'vstm-trail-monitor' )) ); 
			}
		}
	}

	if (!isset($_REQUEST[$field])) 
		return array();
	
	$id_list = map_deep(wp_unslash($_REQUEST[$field]),'sanitize_text_field');
	return vstm_filter_int_array($id_list);
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
		$out = wp_strip_all_tags($out);

	if (empty($out))
		$out = $default;

	return $out;
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
	if (!empty($in)) {
		//var_dump($in);
		foreach ($in as $key => $value) {
			$out[$key] = (int)filter_var(preg_replace('/(\..*)/', '', $value), FILTER_SANITIZE_NUMBER_INT);
		}
		//var_dump($out);
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
