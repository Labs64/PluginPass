<?php

namespace PluginPass\Inc\Common\Traits;

use NetLicensing\Constants;
use NetLicensing\Context;
use NetLicensing\LicenseeService;
use NetLicensing\ValidationParameters;
use PluginPass\Inc\Core\Activator;

trait PluginPass_Plugable {

	protected function get_plugin( array $where ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		$queryWhere = implode( ' AND ', array_map( function ( $key, $value ) {
			return "$key='$value'";
		}, array_keys( $where ), $where ) );

		$plugin = $wpdb->get_row( "SELECT * FROM $plugins_table WHERE $queryWhere" );

		if ( $plugin ) {
			$plugin->validation = json_decode( $plugin->validation, true );
		}

		return $plugin;
	}

	/**
	 * Create new row in plugins table and save plugin validation data
	 *
	 * @param array $data
	 *
	 * @return array|object|void|null
	 * @throws \Exception
	 */
	protected function create_plugin( array $data ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		$result = $wpdb->insert( $plugins_table, array_merge( $data, [ 'validated_at' => date( DATE_ATOM ) ] ) );

		if ( ! $result ) {
			throw new \Exception( 'Failed to save plugin validation data.' );
		}

		return $this->get_plugin( [ 'ID' => $wpdb->insert_id ] );
	}

	/**
	 * Update existing plugin validation data
	 *
	 * @param array $data
	 * @param array $where
	 *
	 * @return array|object|void|null
	 * @throws \Exception
	 */
	protected function update_plugin( array $data, array $where ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		$result = $wpdb->update( $plugins_table, array_merge( $data, [ 'validated_at' => date( DATE_ATOM ) ] ), $where );

		if ( ! $result ) {
			throw new \Exception( 'Failed to update plugin validation data.' );
		}

		return $this->get_plugin( $where );
	}
}