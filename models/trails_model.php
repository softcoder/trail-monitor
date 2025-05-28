<?php
/** Database Functions
 * @Package: 		com.vsoft.trailmonitor
 * @File			models/trail_model.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
*/
class vstm_Trails_Model {

	/** Returns the list of trails for the shortcode ordered by the order # then name.
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @global string $vstm_table_statuses
	 * @return array
	 */
	function get_list_for_shortcode () {
		global $wpdb, $vstm_table_trails, $vstm_table_statuses;
		return stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT trail_id, visitdate, %i.name, link, comment, submitter_name, image_id, %i.name AS status, %i.color AS color FROM %i
				JOIN %i ON %i.status_id = %i.status_id
				WHERE hidden = 0 AND show_shortcode = 1 ORDER BY %i.sort_order ASC, %i.visitdate DESC, %i.name ASC LIMIT 50;",
				$vstm_table_trails,
				$vstm_table_statuses,
				$vstm_table_statuses,
				$vstm_table_trails,
				$vstm_table_statuses,
				$vstm_table_statuses,
				$vstm_table_trails,
				$vstm_table_trails,
				$vstm_table_trails,
				$vstm_table_trails), 
				ARRAY_A));
	}

	/** Returns the list of trails for the shortcode ordered by the order # then name.
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @global string $vstm_table_statuses
	 * @return array
	 */
	function get_list_for_widget () {
		global $wpdb, $vstm_table_trails, $vstm_table_statuses;
		return stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT trail_id, visitdate, %i.name, link, comment, submitter_name, %i.name AS status, %i.color AS color FROM %i
			 JOIN %i ON %i.status_id = %i.status_id
			 WHERE hidden = 0 AND show_widget = 1 ORDER BY %i.sort_order ASC, %i.name ASC LIMIT 50;",
			$vstm_table_trails,
			$vstm_table_statuses,
			$vstm_table_statuses,
			$vstm_table_trails,
			$vstm_table_statuses,
			$vstm_table_statuses,
			$vstm_table_trails,
			$vstm_table_trails,
			$vstm_table_trails
		), ARRAY_A));
	}

	/** Gets all the trail names and ids for non-hidden trails
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @return array
	 */
	function get_trail_names () {
		global $wpdb, $vstm_table_trails;
		return stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT trail_id, name, status_id FROM %i WHERE hidden = 0",
			$vstm_table_trails), ARRAY_A));
	}

	/** Gets all the trail names and ids for non-hidden trails
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @return array
	 */
	function get_names_list () {
		global $wpdb, $vstm_table_trails;
		$result = stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT trail_id, name FROM %i",
			$vstm_table_trails), ARRAY_A));
		if (empty($result))
			return false;
		foreach ($result as $row) {
			$return[$row['trail_id']] = $row['name'];
		}
		return $return;
	}	
	
	/** Gets all a set of records for the list table
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $hidden
	 * @return type
	 */
	function get_list ($hidden = null) {
		global $wpdb, $vstm_table_trails;

		// ***** Build Where Statement *****
		$where = '';
		if (1 == $hidden)
			return stripslashes_deep($wpdb->get_results($wpdb->prepare(
				"SELECT * FROM %i WHERE hidden = 1",
				$vstm_table_trails
			), ARRAY_A));
		if (2 == $hidden)
			return stripslashes_deep($wpdb->get_results($wpdb->prepare(
				"SELECT * FROM %i WHERE hidden = 0",
				$vstm_table_trails
			), ARRAY_A));
		
		return stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT * FROM %i",
			$vstm_table_trails
		), ARRAY_A));
	}

	/** Returns All Fields in a Listing Record
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @return array
	 */
	function get ($trail_id) {
		global $wpdb, $vstm_table_trails;
		return stripslashes_deep($wpdb->get_row($wpdb->prepare(
			"SELECT * FROM %i WHERE trail_id = %d",
			$vstm_table_trails,
			(int)$trail_id
		), ARRAY_A));
	}

	/** Creates a New Listing
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param string $name
	 * @param string $visitdate
	 * @param string $link
	 * @param string $comment
	 * @param string $submitter
	 * @param int $image_id
	 * @param float $sort_order
	 * @param int $show_widget
	 * @param int $show_shortcode
	 * @param int $status_id
	 * @param int $approved
	 * @return boolean
	 */
	function add ($name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget = 1, $show_shortcode = 1, $status_id = 0, $approved = 0) {
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->insert(
				$vstm_table_trails,
				[
					'created' => current_time('mysql'),
					'visitdate' => $visitdate,
					'name' => $name,
					'link' => $link,
					'comment' => $comment,
					'submitter_name' => $submitter,
					'image_id' => $image_id,
					'sort_order' => $sort_order,
					'status_id' => $status_id,
					'hidden' => !$approved,
					'show_widget' => $show_widget,
					'show_shortcode' => $show_shortcode
				],
				['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->add: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);
		return $result;
	}

	/** Updates a Listing
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @param string $name
	 * @param string $visitdate
	 * @param string $link
	 * @param string $comment
	 * @param string $submitter
	 * @param int $image_id
	 * @param float $sort_order
	 * @param int $show_widget
	 * @param int $show_shortcode
	 * @param int $status_id
	 * @param int $approved
	 * @return boolean
	 */
	function update ($trail_id, $name, $visitdate, $link, $comment, $submitter, $image_id, $sort_order, $show_widget, $show_shortcode, $status_id, $approved) {
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->update(
				$vstm_table_trails, 
				[
					'visitdate' => $visitdate,
					'name' => $name,
					'link' => $link,
					'comment' => $comment,
					'submitter_name' => $submitter,
					'image_id' => $image_id,
					'sort_order' => $sort_order,
					'status_id' => $status_id,
					'hidden' => !$approved,
					'show_widget' => $show_widget,
					'show_shortcode' => $show_shortcode
				],
				['trail_id' => $trail_id],
				['%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d'],	
				['%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->add: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		return $result;
	}

	/** Delete a Listing
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $id
	 */
	function delete ($id) {
		global $wpdb, $vstm_table_trails;
		return $wpdb->delete($vstm_table_trails, ['trail_id' => $id], ['%d']);
	}

	/** Updates the Status of a Trail
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @param int $status_id
	 * @return boolean
	 */
	function set_status ($trail_id, $status_id) {
		// ***** Query *****
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->update(
				$vstm_table_trails,
				['status_id' => $status_id],
				['trail_id' => $trail_id],
				['%d'],
				['%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->set_status: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		return $result;
	}

	/** Set a listing approved status
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @param int $approved
	 * @return boolean
	 */
	function set_approved ($trail_id, $approved = 1) {
		// ***** Query *****
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->update(
				$vstm_table_trails,
				['hidden' => !$approved],
				['trail_id' => $trail_id],
				['%d'],
				['%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->set_approved: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		return $result;
	}
	
	/** Set a listing as shown on the widget
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @param int $show_widget
	 * @return boolean
	 */
	function set_show_widget ($trail_id, $show_widget = 1) {
		// ***** Query *****
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->update(
				$vstm_table_trails,
				['show_widget' => $show_widget],
				['trail_id' => $trail_id],
				['%d'],
				['%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->set_show_widget: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		return $result;
	}

	/** Set a listing as shown on the shortcode
	 * @global wpdb $wpdb
	 * @global string $vstm_table_trails
	 * @param int $trail_id
	 * @param int $show_shortcode
	 * @return boolean
	 */
	function set_show_shortcode ($trail_id, $show_shortcode = 1) {
		// ***** Query *****
		global $wpdb, $vstm_table_trails;
		$result = $wpdb->update(
				$vstm_table_trails,
				['show_shortcode' => $show_shortcode],
				['trail_id' => $trail_id],
				['%d'],
				['%d']
		);
		if (false === $result)
			error_log('vstm_Trails_Model->set_show_shortcode: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		return $result;
	}

}
