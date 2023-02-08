<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Paciente8159
 * @since             1.0.0
 * @package           JCEM_Admin_UI
 *
 * @wordpress-plugin
 * Plugin Name:       JCEM Admin UI
 * Plugin URI:        https://github.com/Paciente8159
 * Description:       This is Admin UI render for JCEM plugins.
 * Version:           1.0.1
 * Author:            Joao Martins
 * Author URI:        https://github.com/Paciente8159
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       jcem-admin-ui
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('JCEM_ADMIN_UI_PATH', plugin_dir_path(__FILE__));
define('JCEM_ADMIN_UI_URL', plugin_dir_url(__FILE__));

register_activation_hook(__FILE__, function(){

});

register_deactivation_hook(__FILE__, function(){
	
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('jcem_admin_ui_styles', JCEM_ADMIN_UI_URL . 'assets/css/jcem-admin-ui.css');
    wp_enqueue_script('jcem_admin_ui_scripts', JCEM_ADMIN_UI_URL . 'assets/js/jcem-admin-ui.js', true);
    wp_enqueue_media();
});

if (!function_exists('jcem_admin_ui_render_field_label')) {
	function jcem_admin_ui_render_field_label($type, $pluginslug, $id, $label)
	{
?>
		<label class="jcem-admin-ui-fieldlabel" for="<?php echo $id; ?>"><?php echo $label; ?></label>
		<?php
	}
}

if (!function_exists('jcem_admin_ui_get_value')) {
	function jcem_admin_ui_get_value($values, $key)
	{
		return isset($values[$key]) ? $values[$key] : '';
	}
}

if (!function_exists('jcem_admin_ui_render_field')) {
	function jcem_admin_ui_render_field($pluginslug, $fieldargs, $values, $value_callback = 'jcem_admin_ui_get_value')
	{
		$pluginslug = sanitize_title($pluginslug);
		$label = isset($fieldargs['label']) ? $fieldargs['label'] : '';
		$name = !empty($fieldargs['id']) ? $fieldargs['id'] : "";
		$id = $pluginslug . '-' . sanitize_title($name);
		$type = $fieldargs['input_type'];
		$classes = isset($fieldargs['classes']) ? $fieldargs['classes'] : '';
		$description = isset($fieldargs['description']) ? $fieldargs['description'] : '';
		$value = isset($fieldargs['value']) ? $fieldargs['value'] : $value_callback($values, $name);
		$input_val = isset($value) ? esc_attr(is_array($value) ? $value[0] : $value) : '';
		$attributes = isset($fieldargs['attributes']) ? $fieldargs['attributes'] : "";

		switch ($type) {
			case 'fieldset':
		?>
				<fieldset id="<?php echo $pluginslug; ?>-groupfield-<?php echo $id; ?>" class="jcem-admin-ui-groupfield <?php echo $pluginslug; ?>-groupfield <?php echo $classes; ?>">
					<legend><?php echo $label; ?></legend>
					<table id="<?php echo $pluginslug; ?>-table-<?php echo $id; ?>" class="jcem-admin-ui-table <?php echo $pluginslug; ?>-table">
						<tbody>
							<?php
							foreach ($fieldargs['fields'] as $field) {
							?><tr><?php
												if ($field['input_type'] != 'fieldset' &&  $field['input_type'] != 'div') {
													$field_label = isset($field['label']) ? $field['label'] : '';
													$field_name = !empty($field['id']) ? $field['id'] : "";
													$field_id = $pluginslug . '-' . sanitize_title($field_name); ?><td><label class="jcem-admin-ui-fieldlabel" for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label></td>
										<td><?php
												} else {
											?>
										<td colspan="2" class="jcem-admin-ui-single-cell">
										<?php
												}
												jcem_admin_ui_render_field($pluginslug, $field, $values, $value_callback); ?></td>
								<tr><?php
								}
									?>
						</tbody>
					</table>
				</fieldset>
			<?php
				break;
			case 'div':
			?>
				<div class="jcem-admin-ui-div <?php echo $pluginslug; ?>-div <?php echo $classes; ?>">
					<?php
					foreach ($fieldargs['fields'] as $field) {
						jcem_admin_ui_render_field($pluginslug, $field, $values, $value_callback);
					}
					?>
				</div>
			<?php
				break;
			case 'button':
			?>
				<button id="<?php echo $pluginslug; ?>-button-<?php echo $id; ?>" class="jcem-admin-ui-button <?php echo $pluginslug; ?>-button <?php echo $classes; ?>" <?php echo $attributes; ?>><?php echo $label; ?></button>
			<?php
				return;
			case 'span':
			?>
				<span class="<?php echo $classes; ?>"><?php echo $description; ?></span>
			<?php
				break;
			case 'radio':
				$counter = 0;
			?>
				<table id="<?php echo $pluginslug; ?>-table-<?php echo $id; ?>" class="jcem-admin-ui-table <?php echo $pluginslug; ?>-table">
					<tbody><?php
							foreach ($fieldargs['options'] as $option) { ?>
							<tr>
								<?php if (isset($option['label'])) { ?>
									<td><label class="jcem-admin-ui-fieldlabel" for="<?php echo $id . '-' . $counter; ?>"><?php echo $option['label']; ?></label></td>
								<?php } ?>
								<td><input class="jcem-admin-ui-field <?php echo $pluginslug; ?>-field <?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id . '-' . $counter++; ?>" value="<?php echo $option['id']; ?>" <?php echo ($option['id'] == $value) ? 'checked' : '';
																																																																		echo $attributes; ?>>
									<span><?php echo (isset($option['description']) ? $option['description'] : ''); ?></span>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			<?php
				break;
			case 'select':
			?><select class="jcem-admin-ui-field <?php echo $pluginslug; ?>-field <?php echo $classes; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $attributes; ?>>
					<?php
					foreach ($fieldargs['options'] as $option) { ?>
						<option value="<?php echo $option['id']; ?>" <?php echo (isset($option['attributes']) ? $option['attributes'] : ''); ?>><?php echo $option['label']; ?></option>
					<?php
					} ?>
				</select>
			<?php
				break;
			case 'checkbox':
			?>
				<input class="jcem-admin-ui-field <?php echo $pluginslug; ?>-field <?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $input_val ? 'checked' : '';
																																															echo $attributes; ?> />
				<span><?php echo $description; ?></span>
			<?php
				break;
			case 'textarea':
			?>
				<div><textarea class="jcem-admin-ui-field <?php echo $pluginslug; ?>-field <?php echo $classes; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" <?php echo $attributes; ?> onload="jcem_intput_charcount(this);" onkeyup="jcem_intput_charcount(this);"><?php echo $input_val; ?></textarea><span class="jcem-admin-ui-charcounter"></span>
					<span><?php echo $description; ?></span>
				</div>
			<?php
				break;
			case 'html':
			?>
				<div>
					<?php echo $value; ?>
					<span><?php echo $description; ?></span>
				</div>
			<?php
				break;
			case 'errormsg': ?>
				<div class="jcem-admin-ui-errormsg">
					<span><?php echo $description; ?></span>
					<span class="dashicons dashicons-welcome-comments"></span>
				</div>
			<?php
				break;
			case 'img':
				// Get WordPress' media upload URL
				$upload_link = esc_url(get_upload_iframe_src('image'));
				if ($value) {
					$img_src = wp_get_attachment_image_src($value, 'full');
					$value = is_array($img_src);
				}
			?>
				<div class="jcem-admin-ui-img" id="<?php echo $id; ?>-img">
					<?php if ($value) { ?>
						<img src="<?php echo $your_img_src[0] ?>" alt="" style="max-width:100%;" <?php echo $attributes; ?> />
					<?php } else {
					?>
						<span class="dashicons dashicons-format-image"></span>
					<?php
					} ?>
				</div>
				<!-- Your add & remove image links -->
				<p class="jcem-admin-ui-hide-if-no-js">
					<a class="jcem-admin-ui-add-img <?php if ($value) {
														echo 'hidden';
													} ?>" href="<?php echo $upload_link ?>" onclick="jcem_admin_ui_add_img(event, '<?php echo $pluginslug; ?>', '#<?php echo $id; ?>')">
						<?php _e('Set custom image') ?>
					</a>
					<a class="jcem-admin-ui-delete-img <?php if (!$value) {
															echo 'hidden';
														} ?>" href="#" onclick="jcem_admin_ui_delete_img(event, '<?php echo $pluginslug; ?>', '#<?php echo $id; ?>')">
						<?php _e('Remove this image') ?>
					</a>
				</p>

				<!-- A hidden input to set and post the chosen image id -->
				<input class="jcem-admin-ui-field" name="<?php echo $name; ?>" id="<?php echo $id; ?>" type="hidden" value="<?php echo $input_val; ?>" />
			<?php
				break;
			default:
			?>
				<div><input class="jcem-admin-ui-field <?php echo $pluginslug; ?>-field <?php echo $classes; ?>" type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo $input_val; ?>" <?php echo $attributes; ?> onload="jcem_intput_charcount(this);" onkeyup="jcem_intput_charcount(this);" /><span class="jcem-admin-ui-charcounter"></span>
					<span><?php echo $description; ?></span>
				</div>

		<?php
				break;
		}
	}
}

if (!function_exists('jcem_admin_ui_render_form')) {
	function jcem_admin_ui_render_form($pluginslug, $formargs, $values, $value_callback = 'jcem_admin_ui_get_value')
	{
		$pluginslug = sanitize_title($pluginslug);
		$plugin_underscore_slug = str_replace("-", "_", $pluginslug);
		wp_nonce_field($plugin_underscore_slug . '_nonce_action', $plugin_underscore_slug . '_post_nonce'); ?>
		<div id="<?php echo $pluginslug; ?>-form-content" class="jcem-admin-ui-form-content">
			<div id="<?php echo $pluginslug; ?>-tabs" class="jcem-admin-ui-tabs">
				<?php
				if (isset($formargs)) {
					foreach ($formargs as $id => $group) {
						if (isset($group['label'])) {
				?>
							<button id="<?php echo $pluginslug; ?>-tab-<?php echo $id; ?>" class="jcem-admin-ui-tab <?php echo $pluginslug; ?>-tab" onclick="jcem_admin_ui_changedTabs(event,'<?php echo $pluginslug; ?>','<?php echo $id; ?>');"><?php echo $group['label']; ?></button>
				<?php
						}
					}
				} ?>
			</div>
			<div id="<?php echo $pluginslug; ?>-groups" class="jcem-admin-ui-groups">
				<?php
				foreach ($formargs as $id => $group) { ?>
					<div id="<?php echo $pluginslug; ?>-group-<?php echo $id; ?>" class="jcem-admin-ui-group <?php echo $pluginslug; ?>-group">
						<table id="<?php echo $pluginslug; ?>-table-<?php echo $id; ?>" class="jcem-admin-ui-table <?php echo $pluginslug; ?>-table">
							<tbody>
								<?php
								foreach ($group['fields'] as $field) {
								?><tr><?php
											if ($field['input_type'] != 'fieldset' &&  $field['input_type'] != 'div' && isset($field['label'])) {
												$field_label = isset($field['label']) ? $field['label'] : '';
												$field_name = !empty($field['id']) ? $field['id'] : "";
												$field_id = $pluginslug . '-' . sanitize_title($field_name); ?><td><label class="jcem-admin-ui-fieldlabel" for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label></td>
											<td><?php
											} else {
												?>
											<td colspan="2" class="jcem-admin-ui-single-cell">
											<?php
											}
											jcem_admin_ui_render_field($pluginslug, $field, $values, $value_callback); ?></td>
									<tr><?php
									}
										?>
							</tbody>
						</table>
					</div> <?php
						} ?>
			</div>
		</div>
<?php
	}
}

if (!function_exists('jcem_admin_ui_validate_form')) {
	function jcem_admin_ui_validate_form($pluginslug)
	{
		$pluginslug = sanitize_title($pluginslug);
		$plugin_underscore_slug = str_replace("-", "_", $pluginslug);

		if (!isset($_POST[$plugin_underscore_slug . '_post_nonce'])) {
			return false;
		}

		if (!wp_verify_nonce($_POST[$plugin_underscore_slug . '_post_nonce'], $plugin_underscore_slug . '_nonce_action')) {
			return false;
		}

		return true;
	}
}
