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
 * @since 1.0.0
 * @package           Pluginpass
 *
 * @wordpress-plugin
 * Plugin Name:       PluginPass
 * Plugin URI:        https://github.com/Labs64/PluginPass
 * Description:       Easily control the use and monetize your WordPress plugins and themes using PluginPass - a WordPress License Manager backed by Labs64 NetLicensing.
 * Version:           0.9.10
 * Author:            Labs64
 * Author URI:        https://netlicensing.io
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pluginpass
 * Domain Path:       /languages
 */

namespace PluginPass;

// If this file is called directly, abort.
use PluginPass\Inc\Core\Init;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Constants
 */
require_once( __DIR__ . '/constants.php' );

/**
 * Autoload Classes
 */
// external dependencies (composer)
require_once( constant( NS . 'PLUGIN_NAME_DIR' ) . 'vendor/autoload.php' );
// included dependencies
require_once( constant( NS . 'PLUGIN_NAME_DIR' ) . 'inc/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in inc/core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'Inc\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented inc/core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'Inc\Core\Deactivator', 'deactivate' ) );

/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 *
 * @since 1.0.0
 */
class PluginPass {
	static $init;

	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null == self::$init ) {
			self::$init = new Init();
			self::$init->run();
		}

		return self::$init;
	}
}

/*
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 */
function pluginpass_init() {
	return PluginPass::init();
}

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, constant( NS . 'PLUGIN_MIN_PHP_VERSION' ), '>=' ) ) {
	pluginpass_init();
}
