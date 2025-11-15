<?php

namespace PluginPass\Inc\Core;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link https://www.labs64.com
 * @since 1.0.0
 *
 * @author Labs64 <info@labs64.com>
 */
class Internationalization_i18n {

	private $text_domain;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_text_domain ) {
		$this->text_domain = $plugin_text_domain;
	}


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->text_domain,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
