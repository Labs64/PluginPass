<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://netlicensing.io
 * @since      1.0.0
 *
 * @package    Pluginpass_Demo
 * @subpackage Pluginpass_Demo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pluginpass_Demo
 * @subpackage Pluginpass_Demo/includes
 * @author     Labs64 NetLicensing <netlicensing@labs64.com>
 */
class Pluginpass_Demo_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Required for plugins not hosted on WordPress.org
		load_plugin_textdomain(
			'pluginpass-demo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
