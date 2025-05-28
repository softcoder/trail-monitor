<?php
/** Shortcode Controller
 * @Package: 		com.vsoft.trailmonitor
 * @File			shortcode.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

  /** Creates the embedded youtube HTML
 * @param array $attributes
 * @param string $content
 * @return string
 */
function vstm_sc_youtube ($attributes, $content = null) {
	// ***** Get Attributes & Data *****
	$attr_defaults = array('src' => '', 'width' => '300', 'height' => '300');
	$atts = shortcode_atts($attr_defaults, $attributes);

	$youtube_src = $atts['src'];
	if (!str_contains($youtube_src, '/embed/')) {
		// https://www.youtube.com/embed/E8x8VqCPn5g?si=yMYXa9MXVmxYJcNv
		// https://youtu.be/E8x8VqCPn5g
		$youtube_src = end(explode('/', $youtube_src));
		$youtube_src = 'https://www.youtube.com/embed/'.$youtube_src;
	}

	$output  = '<iframe width="'.$atts['width'].'" height="'.$atts['height'].'" src="'.$youtube_src.'" ';
	$output .= '	title="YouTube video player" frameborder="0" ';
	$output .= '	allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" ';
	$output .= '	referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>';
	$output .= '</iframe>';

	return $output;
}

 /** Creates the non admin based table list HTML
 * @param array $attributes
 * @param string $content
 * @return string
 */
function vstm_sc_table_list ($attributes, $content = null) {
	// ***** Load Models, Helpers and Libraries *****
	require_once('models/trails_model.php');
	if (!isset($vstm_Trails_Model))
		$vstm_Trails_Model = new vstm_Trails_Model();

	// ***** Get Attributes & Data *****
	$attr_defaults = array('show_images' => 'yes', 'color_text' => 'yes', 'box_shadow' => 'yes', 'small_images' => 'no', 'border' => 'no');
	$atts = shortcode_atts($attr_defaults, $attributes);
	if ('yes' == strtolower($atts['show_images'])) $show_images = true; else $show_images = false;
	if ('yes' == strtolower($atts['color_text'])) $color_text = true; else $color_text = false;
	if ('yes' == strtolower($atts['box_shadow'])) $box_shadow = true; else $box_shadow = false;
	if ('yes' == strtolower($atts['small_images'])) $small_images = true; else $small_images = false;
	if ('yes' == strtolower($atts['border'])) $border = true; else $border = false;
	
	$trail_list = $vstm_Trails_Model->get_list_for_shortcode();

	// ***** View *****
	$output = '<table class="vstm_table1" '.($border ? 'border="1"' : '').'>';
	$output .= '<thead><tr>';
	$output .= '<td>Photo</td>';
	$output .= '<td>Trail Name</td>';
	$output .= '<td>Hike Date</td>';
	$output .= '<td>Trail Status</td>';
	$output .= '<td>Comment</td>';
	$output .= '<td>Submitted By</td>';
	$output .= '</tr></thead>';

	if (!empty($trail_list)) {
		if ($small_images) {
			$img_class = 'vstm_sc_sm_trail_img';
		} else {
			$img_class = 'vstm_sc_trail_img';
		}
		foreach ($trail_list as $trail) {
			if ($color_text && !empty($trail['color'])) {
				$color_str = ' style="color: ' . htmlspecialchars($trail['color']) . '"';
			} else {
				$color_str = '';
			}
			$output .= '<tr class="vstm_sc_trail"' . $color_str . '>';

			// *** Image ***
			$output .= '<td class="' . $img_class . '">';
			if ($show_images) {
				if (!empty($trail['image_id'])) {
					$image_alt = htmlspecialchars(get_post_meta($trail['image_id'], '_wp_attachment_image_alt', true));
					$image_page_url = get_attachment_link($trail['image_id']);
					$output .= '<a href="' . $image_page_url . '" target="_blank">';
					
					$attrs = array(
						'alt' => $image_alt
					);
					if ($box_shadow) {
						$attrs['class'] = 'vstm_box_shadow';
					}
			
					$output .= wp_get_attachment_image( $trail['image_id'], [100,60], false, $attrs );
					$output .= '</a>';

				} else {
					$output .= '<img src="' . plugins_url('images/trail-placeholder.png', __FILE__) . '" alt="">';
				}
			}
			$output .= '</td>';
			
			// *** Name & Link ***
			$output .= '<td>';
			if (!empty($trail['link'])) {
				$output .= '<a href="' . htmlspecialchars($trail['link']) . '" target="_blank"' . $color_str . '>';
			}
			$output .= htmlspecialchars($trail['name']);
			if (!empty($trail['link'])) {
				$output .= '</a>';
			}
			$output .= '</td>';

			// *** Hike Date ***
			$output .= '<td>' . gmdate("Y-M-d", strtotime($trail['visitdate'])) . '</td>';

			// *** Status ***
			$output .= '<td class="vstm_sc_status">' . htmlspecialchars($trail['status']) . '</td>';

			// *** Comment ***
			$output .= '<td><p>' . str_replace(array("\r\n", "\n", "\r"), ' ', nl2br( do_shortcode($trail['comment']))) . '</p></td>';

			

			// *** Submitter ***
			$output .= '<td>' . htmlspecialchars($trail['submitter_name']) . '</td>';
			
			$output .= '</tr>';
		}
	}
	$output .= '</table>';
	return $output;
}

/** Creates the small non-admin table list HTML
 * @param array $attributes
 * @param string $content
 * @return string
 */
function vstm_sc_table ($attributes, $content = null) {
	// ***** Load Models, Helpers and Libraries *****
	require_once('models/trails_model.php');
	if (!isset($vstm_Trails_Model))
		$vstm_Trails_Model = new vstm_Trails_Model();

	// ***** Get Attributes & Data *****
	$attr_defaults = array('show_images' => 'yes', 'color_text' => 'yes', 'box_shadow' => 'yes', 'small_images' => 'no');
	$atts = shortcode_atts($attr_defaults, $attributes);
	if ('yes' == strtolower($atts['show_images'])) $show_images = true; else $show_images = false;
	if ('yes' == strtolower($atts['color_text'])) $color_text = true; else $color_text = false;
	if ('yes' == strtolower($atts['box_shadow'])) $box_shadow = true; else $box_shadow = false;
	if ('yes' == strtolower($atts['small_images'])) $small_images = true; else $small_images = false;
	
	$trail_list = $vstm_Trails_Model->get_list_for_shortcode();

	// ***** View *****
	$output = '<table class="vstm_sc">';
	if (!empty($trail_list)) {
		if ($small_images) {
			$img_class = 'vstm_sc_sm_trail_img';
		} else {
			$img_class = 'vstm_sc_trail_img';
		}
		foreach ($trail_list as $trail) {
			if ($color_text && !empty($trail['color'])) {
				$color_str = ' style="color: ' . htmlspecialchars($trail['color']) . '"';
			} else {
				$color_str = '';
			}
			$output .= '<tr class="vstm_sc_trail"' . $color_str . '>';

			// *** Image ***
			if ($show_images) {
				$output .= '<td class="' . $img_class . '">';
				if (!empty($trail['image_id'])) {
					$image_alt = htmlspecialchars(get_post_meta($trail['image_id'], '_wp_attachment_image_alt', true));
					$image_page_url = get_attachment_link($trail['image_id']);
					$output .= '<a href="' . $image_page_url . '" target="_blank">';
					
					$attrs = array(
						'alt' => $image_alt
					);
					if ($box_shadow) {
						$attrs['class'] = 'vstm_box_shadow';
					}
					$output .= wp_get_attachment_image( $trail['image_id'], [100,60], false, $attrs );
					
					$output .= '</a>';
				} else {
					$output .= '<img src="' . plugins_url('images/trail-placeholder.png', __FILE__) . '" alt="">';
				}
				$output .= '</td>';
			}
			
			// *** Name & Link ***
			$output .= '<td class="vstm_sc_title">';
			if (!empty($trail['link'])) {
				$output .= '<a href="' . htmlspecialchars($trail['link']) . '" target="_blank"' . $color_str . '>';
			}
			$output .= htmlspecialchars($trail['name']) . ':';
			if (!empty($trail['link'])) {
				$output .= '</a>';
			}
			$output .= '</td>';

			// *** Status ***
			$output .= '<td class="vstm_sc_status">' . htmlspecialchars($trail['status']) . '</td>';
			
			$output .= '</tr>';
		}
	}
	$output .= '</table>';
	
	$vstm_notes_sc = get_option('vstm_notes_sc');
	if (!empty($vstm_notes_sc))
		$output .= '<p>' . htmlspecialchars(get_option('vstm_notes_sc')) . '</p>';

	return $output;
}

/** Creates the non-admin block HTML
 * @param array $attributes
 * @param string $content
 * @return string
 */
function vstm_sc_blocks ($attributes, $content = null) {
	// ***** Load Models, Helpers and Libraries *****
	if (!isset($vstm_Trails_Model)) {
		require_once('models/trails_model.php');
		$vstm_Trails_Model = new vstm_Trails_Model();
	}

	// ***** Get Attributes & Data *****
	$attr_defaults = array('show_images' => 'yes', 'color_text' => 'yes', 'box_shadow' => 'yes', 'small_images' => 'no');
	$atts = shortcode_atts($attr_defaults, $attributes);
	if ('yes' == strtolower($atts['show_images'])) $show_images = true; else $show_images = false;
	if ('yes' == strtolower($atts['color_text'])) $color_text = true; else $color_text = false;
	if ('yes' == strtolower($atts['box_shadow'])) $box_shadow = true; else $box_shadow = false;
	if ('yes' == strtolower($atts['small_images'])) $small_images = true; else $small_images = false;
	
	$trail_list = $vstm_Trails_Model->get_list_for_shortcode();

	// ***** View *****
	$output = '<div class="vstm_sc">';

	if (!empty($trail_list)) {
		if ($small_images) {
			$img_class = 'vstm_sc_sm_trail_img';
		} else {
			$img_class = 'vstm_sc_trail_img';
		}

		foreach ($trail_list as $trail) {
			if ($color_text && !empty($trail['color'])) {
				$color_str = ' style="color: ' . $trail['color'] . '"';
			} else {
				$color_str = '';
			}
			$output .= '<div class="vstm_scb"' . $color_str . '>';
			// *** Image ***
			if ($show_images) {
				$output .= '<div class="' . $img_class . '">';
				if (!empty($trail['image_id'])) {
					$image_alt = htmlspecialchars(get_post_meta($trail['image_id'], '_wp_attachment_image_alt', true));
					$image_page_url = get_attachment_link($trail['image_id']);
					$output .= '<a href="' . $image_page_url . '" target="_blank">';
					
					$attrs = array(
						'alt' => $image_alt
					);
					if ($box_shadow) {
						$attrs['class'] = 'vstm_box_shadow';
					}
					$output .= wp_get_attachment_image( $trail['image_id'], [100,60], false, $attrs );
					
					$output .= '</a>';
				} else {
					$output .= '<img src="' . plugins_url('images/trail-placeholder.png', __FILE__) . '">';
				}
				$output .= '</div>';
			}

			// *** Name & Link ***
			if (!empty($trail['link'])) {
				$output .= '<a href="' . esc_url($trail['link']) . '" target="_blank"' . $color_str . '>';
			}
			$output .= htmlspecialchars($trail['name']);
			if (!empty($trail['link'])) {
				$output .= '</a>';
			}

			// *** Status ***
			$output .= '<br>' . $trail['status'];
			$output .= '</div>';
		}
	}
	$output .= '</div>';
	
	$vstm_notes_sc = get_option('vstm_notes_sc');
	if (!empty($vstm_notes_sc))
		$output .= '<p>' . get_option('vstm_notes_sc') . '</p>';

	return $output;
}

/** Creates the non-admin submit trail status HTML
 * @param array $attributes
 * @param string $content
 * @return string
 */
function vstm_sc_submit ($attributes, $content = null) {
	ob_start();

	// ***** Load Models, Helpers and Libraries *****
	require_once('models/trails_model.php');
	if (!isset($vstm_Trails_Model))
		$vstm_Trails_Model = new vstm_Trails_Model();

	// ***** Security Check *****
	// Anyone can submit a new trail status report for review

	// ***** Load Models, Helpers and Libraries *****
	$vstm_Trails_Model = new vstm_Trails_Model();
	require_once(VSTM_ROOT_PATH . 'helpers/view_helper.php');
	require_once(VSTM_ROOT_PATH . 'helpers/filter_helper.php');

	$message_list = array();
	
	// ***** Form Submitted *****
	if (isset($_POST['_wpnonce'])) {
		vstm_verify_recaptcha();

		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));

		$failure = false;
		// *** Validate & Check ***
		// * Trail Id and Nonce *
		//$trail_id = vstm_get_request_int('trail_id');
		// Currently shortcode user can only add trail statuses, NO EDIT
		$trail_id = null;
		//if (empty($trail_id)) { 
			//check_admin_referer('trail_add');
		if ( !wp_verify_nonce( $nonce_var, 'trail_add' ) ) {
			wp_die( esc_html(__( 'Security check', 'vstm-trail-monitor' )) ); 
		}
		//} 
		
		// * Name Is Required */
		$name = vstm_get_request_string('trail_name', null, false, 'user', $nonce_var, 'trail_add');
		if (empty($name)) {
			$message_list[] = ['Name is Required', 3, 3];
			$failure = true;
		}

		// * Visit Date Is Required *
		$visitdate = vstm_get_request_string('visitdate', null, false, 'user', $nonce_var, 'trail_add');
		if (empty($visitdate)) {
			$message_list[] = ['Visit Date is Required', 3, 3];
			$failure = true;
		}
		
		// * Yes/No Fields - Defaults to Yes *
		if (empty($_POST['show_shortcode']))
			$show_shortcode = 0;
		else
			$show_shortcode = 1;
		
		if (empty($_POST['show_widget']))
			$show_widget = 0;
		else
			$show_widget = 1;
		
		// * Sanitize Integer Fields *
		$sort_order = vstm_get_request_int('sort_order', 99, 'user', $nonce_var, 'trail_add');
		$image_id = vstm_getImagefromRequest();

		$status_id = vstm_get_request_int('status_id', null, 'user', $nonce_var, 'trail_add');
		//$approved = vstm_get_request_int('trail_approved');
		$approved = 0; // non admin users cannot set this value

		// * Link: Sanitize and Add http:// if Missing *
		if (!empty($_POST['link'])) {
			$link = wp_kses_post(wp_unslash($_POST['link']), FILTER_SANITIZE_STRING);
			if (0 != strncasecmp($link, "http://", 7) && 0 != strncasecmp($link, "https://", 8))
				$link = 'http://' . $link;
			$link = filter_var($link, FILTER_SANITIZE_URL);
		} else {
			$link = null;
		}

		// * Comment: Sanitize
		if (!empty($_POST['comment'])) {
			$comment = wp_kses_post(wp_unslash($_POST['comment']));
			
		} else {
			$comment = null;
		}

		// Submitter: Sanitize
		if (!empty($_POST['submitter_name'])) {
			$submitter = vstm_get_request_string('submitter_name', null, false, 'user', $nonce_var, 'trail_add');
		}
		else {
			$submitter = null;
		}

		// *** Update or Add ***
		if ($trail_id && !$failure) {
			$update_result = $vstm_Trails_Model->update($trail_id, $name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget, $show_shortcode, $status_id, $approved);
			if ($update_result === false) {
				$message_list[] = ['Error Updating ' . $name . ' (#' . $trail_id . ')', 3, 3];
				$failure = true;
			} else {
				$message_list[] = [$name . ' Updated', 1, 5];
			}
		} elseif (!$failure) {
			$add_result = $vstm_Trails_Model->add($name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget, $show_shortcode, $status_id, $approved);
			if ($add_result) {
				$message_list[] = [$name . ' Added', 1, 5];

				include(VSTM_ROOT_PATH . 'helpers/notifications.php');
				$msg_to_send = "One or more Trail Statuses have been added for review.<br>Trail Name [$name] Hike Date [$visitdate] Submitted by [$submitter]";
				$subject = 'New Trail Added for Review';
				vstm_sendNotification($msg_to_send, $subject, 'vstm_notification_last_sent_date');
			} else {
				$message_list[] = ['Error Adding Trail ' . $name, 3, 3];
				$failure = true;
			}
		}
		
		// *** Views - Back to Form or Trail List ***
		if ($failure) {
			if (!empty($trail_id)) {
				$record['trail_id'] = $trail_id;
			}
			$record['name'] = $name;
			$record['visitdate'] = $visitdate;
			$record['link'] = $link;
			$record['comment'] = $comment;
			$record['submitter_name'] = $submitter;
			$record['image_id'] = $image_id;
			$record['sort_order'] = $sort_order;
			$record['show_widget'] = $show_widget;
			$record['show_shortcode'] = $show_shortcode;
			$record['status_id'] = $status_id;
			$record['hidden'] = !$approved;
			include(VSTM_ROOT_PATH . 'views/trail_edit.php');
		} 
		else {
			$trail_id = vstm_get_request_int('trail', null, 'user', $nonce_var, 'trail_add');
			if (empty($trail_id)) {
				$record = ['name'=>'', 'visitdate'=>'', 'link'=>'', 'comment'=>'', 'submitter_name'=>'', 'image_id'=>'', 'sort_order'=>'', 'show_widget'=>'1', 'show_shortcode'=>'1', 'hidden'=>0, 'status_id'=>0];
			} 
			else {
				$record = $vstm_Trails_Model->get($trail_id);
			}
			// ***** Call View *****
			include(VSTM_ROOT_PATH . 'views/trail_edit.php');
			include(VSTM_ROOT_PATH . 'views/about.php');
		}
	} 
	else {
		// ***** No Form Submitted *****
		//$trail_id = vstm_get_request_int('trail');
		$trail_id = (isset($_REQUEST['trail']) ? vstm_filter_int_strict(sanitize_text_field(wp_unslash($_REQUEST['trail'])), null) : null);

		if (empty($trail_id)) {
			$record = ['name'=>'', 'visitdate'=>'', 'link'=>'', 'comment'=>'', 'submitter_name'=>'', 'image_id'=>'', 'sort_order'=>'', 'show_widget'=>'1', 'show_shortcode'=>'1', 'hidden'=>0, 'status_id'=>0];
		} else {
			$record = $vstm_Trails_Model->get($trail_id);
		}
		// ***** Call View *****
		include(VSTM_ROOT_PATH . 'views/trail_edit.php');
		include(VSTM_ROOT_PATH . 'views/about.php');
	}
	return ob_get_clean();
}

/** Manually handling the upload of the trail image for non admin users (shortcode submit form)
 * @return int - media library image id
 */
function vstm_getImagefromRequest() {
	$html_form_image_control_id = 'image_id';
	$html_form_image_file_upload_control_id = 'image_upload';

	if(isset($_POST['_wpnonce'])) {
		$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
		if ( !wp_verify_nonce( $nonce_var, 'trail_add' ) ) {
			wp_die( esc_html(__( 'Security check image upload', 'vstm-trail-monitor' )) ); 
		}
	}

	$image_id = vstm_get_request_int($html_form_image_control_id);
	if($image_id <= 0 && isset( $_FILES[ $html_form_image_file_upload_control_id ] )) {
		// WordPress environmet
		require( dirname(__FILE__) . '/../../../wp-load.php' );
		
		// it allows us to use wp_handle_upload() function
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$error = (isset($_FILES[$html_form_image_file_upload_control_id]['error']) ? wp_kses_post($_FILES[$html_form_image_file_upload_control_id]['error']) : UPLOAD_ERR_OK);
		if ($error != UPLOAD_ERR_OK) {
			switch ($error) {
				case UPLOAD_ERR_INI_SIZE:
					$error_message = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
					break;

				case UPLOAD_ERR_FORM_SIZE:
					$error_message = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
					break;

				case UPLOAD_ERR_PARTIAL:
					$error_message = 'The uploaded file was only partially uploaded.';
					break;

				case UPLOAD_ERR_NO_FILE:
					$error_message = 'No file was uploaded.';
					break;

				case UPLOAD_ERR_NO_TMP_DIR:
					$error_message = 'Missing a temporary folder.';
					break;

				case UPLOAD_ERR_CANT_WRITE:
					$error_message = 'Failed to write file to disk.';
					break;

				case UPLOAD_ERR_EXTENSION:
					$error_message = 'A PHP extension interrupted the upload.';
					break;

				default:
					$error_message = 'Unknown error';
				break;
			}
			if($error != UPLOAD_ERR_NO_FILE) {
				wp_die( esc_html("Image upload error (1): ". $error_message) );
			}
		}
			
		if($error != UPLOAD_ERR_NO_FILE) {
			$upload = wp_handle_upload( 
				$_FILES[ $html_form_image_file_upload_control_id ], 
				array( 'test_form' => false ) 
			);
			
			if( ! empty( $upload[ 'error' ] ) ) {
				wp_die( esc_html("Image upload error (2): ". $upload[ 'error' ]) );
			}
			
			// it is time to add our uploaded image into WordPress media library
			$attachment_id = wp_insert_attachment(
				array(
					'guid'           => $upload[ 'url' ],
					'post_mime_type' => $upload[ 'type' ],
					'post_title'     => basename( $upload[ 'file' ] ),
					'post_content'   => '',
					'post_status'    => 'inherit',
				),
				$upload[ 'file' ],
				0, true
			);
			
			if( is_wp_error( $attachment_id ) || ! $attachment_id ) {
				wp_die( esc_html('Upload attachment error: ' . $attachment_id) );
			}
			
			// update medatata, regenerate image sizes
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			
			wp_update_attachment_metadata(
				$attachment_id,
				wp_generate_attachment_metadata( $attachment_id, $upload[ 'file' ] )
			);
			$image_id = $attachment_id;
		}
	}
	return $image_id;
}

/** Google recatcha verification for avoiding spam
 */
function vstm_verify_recaptcha() {
	$options = get_option( 'vstm_options' );
	$secret_key = isset( $options[ 'vstm_google_recaptcha_api_secret_key' ] ) ? $options[ 'vstm_google_recaptcha_api_secret_key' ] : '';

	if( !empty($secret_key)) {
		if(isset($_POST['_wpnonce'])) {
			$nonce_var = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
			if ( !wp_verify_nonce( $nonce_var, 'trail_add' ) ) {
				wp_die( esc_html(__( 'Security check recaptcha', 'vstm-trail-monitor' )) ); 
			}
		}
	
		if (isset($_POST['g-recaptcha-response'])) {
			$secret = $secret_key;
			$response = wp_kses_post(wp_unslash($_POST['g-recaptcha-response'], FILTER_SANITIZE_STRING));
			$remote_ip = '';
			if(isset($_SERVER['REMOTE_ADDR'])) {
				$remote_ip = wp_kses_post(wp_unslash($_SERVER['REMOTE_ADDR'], FILTER_SANITIZE_STRING));
			}
			$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$response}&remoteip={$remote_ip}");
			$result = json_decode($request);

			if (!$result->success) {
				wp_die(esc_html(__('Error (1): Please complete the anti-spam CAPTCHA to submit your information.','vstm-trail-monitor')));
			}
		} else {
			wp_die(esc_html(__('Error (2): Please complete the anti-spam CAPTCHA to submit your information.','vstm-trail-monitor')));
		}
	}
}
