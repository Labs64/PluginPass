<?php

namespace PluginPass\Inc\Core;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.

 * @link       https://www.labs64.com
 * @since      1.0.0
 *
 * @author     Labs64 <info@labs64.com>
 */

class Activator {

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		$min_php = '5.6.0';

		// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.
		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
					deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . $min_php );
		}

    // create plugin database tables
		global $wpdb;
		$table_name = $wpdb->prefix . 'pluginpass';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			plugin_slug varchar(255) NOT NULL,
			plugin_name tinytext NOT NULL,
			expires_at datetime,
			last_validated datetime,
			ttl datetime,
			response_cache text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}
