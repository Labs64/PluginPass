<?php

namespace PluginPass\Inc\Core;

use PluginPass as NS;

/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 * @link       https://www.labs64.com
 * @since 1.0.0
 *
 * @author     Labs64 <info@labs64.com>
 */
class Activator {
	public static function get_plugins_table_name() {
		global $wpdb;

		return $wpdb->prefix . 'pluginpass_plugins';
	}

	/**
	 * Short Description.
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
	// Check PHP Version and deactivate & die if it doesn't meet minimum requirements.

		if ( version_compare( PHP_VERSION, NS\PLUGIN_MIN_PHP_VERSION, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( 'This plugin requires a minmum PHP Version of ' . NS\PLUGIN_MIN_PHP_VERSION );
		}

		// create plugin database tables
		global $wpdb;

		$plugins_table   = self::get_plugins_table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $plugins_table (
			ID bigint(20) NOT NULL AUTO_INCREMENT,
			plugin_folder varchar(255) NOT NULL,
			product_number varchar(255) NOT NULL,
			api_key varchar(255) NOT NULL,
			consented_at timestamp NULL DEFAULT NULL,
			validated_at timestamp NULL DEFAULT NULL,
			expires_ttl_at timestamp NULL DEFAULT NULL,
			validation_result json DEFAULT NULL,
			PRIMARY KEY (ID)
		) $charset_collate;";


		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$indexes           = $wpdb->get_results( "SHOW INDEX FROM $plugins_table" );
		$number_index_name = 'pluginpass_pl_number';

		$is_unique_number_exists = false;

		foreach ( $indexes as $index ) {
			if ( $index->Key_name === $number_index_name ) {
				$is_unique_number_exists = true;
			}
		}

		if ( ! $is_unique_number_exists ) {
			$wpdb->query("CREATE UNIQUE INDEX $number_index_name ON $plugins_table (product_number)");
		}

	}
}
