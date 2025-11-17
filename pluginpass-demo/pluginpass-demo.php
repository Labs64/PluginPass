<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://netlicensing.io
 * @since             1.0.0
 * @package           Pluginpass_Demo
 *
 * @wordpress-plugin
 * Plugin Name:       PluginPass Demo
 * Plugin URI:        https://wordpress.org/plugins/pluginpass-pro-plugintheme-licensing/
 * Description:       PluginPass Demo Plugin to demonstrate PluginPass integration.
 * Version:           1.0.0
 * Author:            Labs64
 * Author URI:        https://netlicensing.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pluginpass-demo
 * Domain Path:       /languages
 * Requires at least: 6.8
 * Tested up to:      6.8
 * Requires PHP:      8.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGINPASS_DEMO_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pluginpass-demo-activator.php
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- WordPress plugin activation hook callback
function activate_pluginpass_demo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pluginpass-demo-activator.php';
	Pluginpass_Demo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pluginpass-demo-deactivator.php
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- WordPress plugin deactivation hook callback
function deactivate_pluginpass_demo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pluginpass-demo-deactivator.php';
	Pluginpass_Demo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pluginpass_demo' );
register_deactivation_hook( __FILE__, 'deactivate_pluginpass_demo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pluginpass-demo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- WordPress plugin initialization function
function run_pluginpass_demo() {

	$plugin = new Pluginpass_Demo();
	$plugin->run();

}
run_pluginpass_demo();
