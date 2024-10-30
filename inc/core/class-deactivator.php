<?php

namespace AmwnLogos\Inc\Core;
use AmwnLogos as NS;

/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://allmywebneeds.com
 * @since      1.0.0
 *
 * @author     All My Web Needs
 **/
class Deactivator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		// Remove settings fields
		$fields = [
			'amwnlogos_show_credit',
			'amwnlogos_hide_credit',
			'amwnlogos_name',
			'amwnlogos_start_date',
			'amwnlogos_start_date[]',
			'amwnlogos_end_date',
			'amwnlogos_repeat',
			'amwnlogos_replacement_logo',
			'amwnlogos_replacement_logo_retina',
			'amwnlogos_replacement_logo_image',
			'amwnlogos_replacement_logo_image_retina',
			'amwnlogos_logo_holder',
		];
		foreach ($fields as $field) {
			unregister_setting(NS\PLUGIN_TEXT_DOMAIN, $field);
			delete_option($field);
		}
	}

}
