<?php
/** Database Functions
 * @Package: 		com.vsoft.trailmonitor
 * @File			models/status_model.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */
class vstm_Status_Model {

	/** Returns the full list of statuses
	 * @global wpdb $wpdb
	 * @global string $table_statuses
	 * @return array
	 */
	function get_list () {
		global $wpdb, $table_statuses;

		return stripslashes_deep($wpdb->get_results($wpdb->prepare(
			"SELECT * FROM %i",
			$table_statuses
		), ARRAY_A));
	}	

	/** Adds a status
	 * @global wpdb $wpdb
	 * @global string $table_statuses
	 * @param string $name
	 * @param string $sort_order
	 * @param string $color
	 * @return boolean
	 */
	function add ($name, $sort_order, $color) {
		global $wpdb, $table_statuses;
		$result = $wpdb->insert(
				$table_statuses,
				['name'=>$name, 'sort_order'=>$sort_order, 'color'=>$color],
				['%s', '%d', '%s']
		);
		if (false === $result)
			error_log('vstm_Status_Model->add: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		else 
			$result = true;
		return $result;
	}

	/** Updates a status record
	 * @global wpdb $wpdb
	 * @global string $table_statuses
	 * @param int $status_id
	 * @param string $name
	 * @param string $sort_order
	 * @param string $color
	 * @return boolean
	 */
	function update ($status_id, $name, $sort_order, $color) {
		global $wpdb, $table_statuses;
		$result = $wpdb->update(
				$table_statuses,
				['name'=>$name, 'sort_order'=>$sort_order, 'color'=>$color],
				['status_id'=>$status_id],
				['%s', '%d', '%s'],
				['%d']
		);
		if (false === $result)
			error_log('vstm_Status_Model->update: ' . $wpdb->last_error . ' | ' . $wpdb->last_query);		
		else 
			$result = true;
		return $result;
	}

	/** Delete a Listing
	 * @global wpdb $wpdb
	 * @param int $id
	 */
	function delete ($id) {
		global $wpdb, $table_statuses;
		return $wpdb->delete($table_statuses, ['status_id'=>$id], ['%d']);
	}

}
