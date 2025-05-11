<?php
/** Widget Classes
 * @Package: 		com.vsoft.trailmonitor
 * @File			widgets.php
 * @Author			VSoft Solutions
 * @Link			https://hiking.princegeorge.tech/software/trail-monitor-wordpress-plugin/
 * @copyright		(c) 2025, VSoft Solutions
 * @created			22/04/2025
 */
class vstm_widget extends WP_Widget {

	public function __construct () {
		$widget_ops = array(
				'classname' => 'vstm_widget',
				'description' => 'Trails Status'
		);
		parent::__construct('vstm_widget', 'Trails Status', $widget_ops);
	}

	/** Actual Widget Being Displayed
	 * @param array $args
	 * @param type $instance
	 */
	function widget ($args, $instance) {
		// ***** Load Models, Helpers and Libraries *****
		require_once('models/trails_model.php');
		$vstm_Trails_Model = new vstm_Trails_Model();

		// ***** Get Data *****
		$trail_list = $vstm_Trails_Model->get_list_for_widget();
		$vstm_notes_widget = get_option('vstm_notes_widget');
		if (!$vstm_notes_widget)
			$vstm_notes_widget = '';
		else
			$vstm_notes_widget = '<p>' . htmlspecialchars($vstm_notes_widget) . '</p>';

		// ***** View *****
		echo $args['before_widget'] . $args['before_title'] . $instance['title'] . $args['after_title'];
		?>
		<table class="vstm_sc">
		<?php
		if (!empty($trail_list))
			foreach ($trail_list as $trail) {
				if ('yes' == strtolower($instance['color_text']) && !empty($trail['color'])) {
					$color_str = ' style=" color: ' . htmlspecialchars($trail['color']) . '"';
				} else {
					$color_str = '';
				}
				?>
			<tr class="vstm_sc_trail"<?= $color_str ?>>
				<td class="vstm_sc_title">
				<?php if (!empty($trail['link'])) { ?>
					<a href="<?= esc_url($trail['link']) ?>" target="_blank"<?= $color_str ?>>
				<?php } ?>
					<?= htmlspecialchars($trail['name']) ?>:
				<?php if (!empty($trail['link'])) { ?>
					</a>
				<?php } ?>
				</td>

				<td class="vstm_sc_status"><?= htmlspecialchars($trail['status']) ?></td>
			</tr>
			<?php } ?>
		</table>
		<?= $vstm_notes_widget ?>
		<?php
		echo $args['after_widget'];
	}

	/** Form on the Widget Options in the Admin
	 * @param type $instance
	 */
	function form ($instance) {
		$defaults = ['title' => 'Trail Status', 'color_text' => 'yes'];
		$instance = wp_parse_args((array)$instance, $defaults);
		?>
		<p class="vstm_widget_form">
			<label for="<?= $this->get_field_id('title') ?>">Title:</label><br>
			<input type="text" name="<?= $this->get_field_name('title') ?>" 
					 id="<?= $this->get_field_id('title') ?> " value="<?= htmlspecialchars($instance['title']) ?>" size="20">
		</p>
		<p class="vstm_widget_form">
			<label>Color Text by Status:</label><br>
			<label for="<?= $this->get_field_id('color_text') ?>_yes" class="vstm_radio_right">Yes</label>
			<input id="<?= $this->get_field_id('color_text') ?>_yes" name="<?= $this->get_field_name('color_text') ?>" 
					 type="radio"<?php if ('yes' == $instance['color_text']) echo ' checked = "checked"'; ?> value="yes">
			<label for="<?= $this->get_field_id('color_text') ?>_no" class="vstm_radio_right">No</label>
			<input id="<?= $this->get_field_id('color_text') ?>_no" name="<?= $this->get_field_name('color_text') ?>" 
					 type="radio"<?php if ('no' == $instance['color_text']) echo ' checked = "checked"'; ?> value="no">
		</p>
		<?php
	}

	/**
	 * @param type $new_instance
	 * @param type $instance
	 * @return type
	 */
	function update ($new_instance, $instance) {
		$instance['title'] = $new_instance['title'];
		$instance['color_text'] = $new_instance['color_text'];
		return $instance;
	}

}
