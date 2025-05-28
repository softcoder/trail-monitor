<?php
/** Notifications
 * @Package: 		com.vsoft.trailmonitor
 * @File			helpers/notifications.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */

 /** Logic related to sending notifications to admins related to this plugin
 *   (such as needing to approve submitted trail status updates)
 *   @param string $msg - The message body to send
 *   @param string $subject - The subject of the message
 *   @param string $daily_check_option - The name of the WordPress setting to extract the last send date from
 *                 (if this is specified we compare todays date to when this notification was last sent
 *                  and only send a notification 1 time per day, if null we send every time)
 */
function vstm_sendNotification($msg, $subject, $daily_check_option=null) {
	$options = get_option( 'vstm_options' );
	$email_recipient = isset( $options[ 'vstm_notifications_email_send_to' ] ) ? $options[ 'vstm_notifications_email_send_to' ] : '';
	if( !empty($email_recipient)) {

        $todays_date = gmdate("Y-M-d");
        $notify_last_sent = ($daily_check_option != null ? get_option( $daily_check_option ) : false);
        if($notify_last_sent === false || $notify_last_sent != $todays_date) {
            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
            );
            
            $from = isset( $options[ 'vstm_notifications_email_send_from' ] ) ? $options[ 'vstm_notifications_email_send_from' ] : get_option( 'admin_email' );
            $to = $email_recipient;
            $headers[] = "From: <{$from}>";
            $body = $msg;

            $email_sent = wp_mail( $to, 'Trail Monitor - ' . $subject, $body, $headers );
            if($email_sent) {
                update_option($daily_check_option, $todays_date);
            }
            else {
                error_log("VSTM-Plugin vstm_sendNotification FAILED to [$to] msg [$msg]");
            }
        }
    }
}