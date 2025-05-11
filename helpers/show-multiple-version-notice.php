<?php
/** Show multiple plugin versions installed error
 * @Package: 		com.vsoft.trailmonitor
 * @File			helpers/notifications.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

if ( ! function_exists( 'vtsm_show_multiple_version_notice' ) ) {
	function vtsm_show_multiple_version_notice() {
		echo '<div class="error"><p>' . esc_html(__( 'Multiple versions of Trail Monitor are active. Please disable all extra versions of Trail Monitor.','vstm-trail-monitor' )) . '</p></div>';
	}

	add_action( 'all_admin_notices', 'vtsm_show_multiple_version_notice' );
}
