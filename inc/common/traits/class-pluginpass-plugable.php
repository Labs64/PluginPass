<?php

namespace PluginPass\Inc\Common\Traits;

use NetLicensing\Constants;
use NetLicensing\Context;
use NetLicensing\LicenseeService;
use NetLicensing\ValidationParameters;
use PluginPass\Inc\Core\Activator;

trait PluginPass_Plugable {

	protected function get_plugin( array $where ) {
		$results = $this->get_plugins( $where );

		return is_array( $results ) ? reset( $results ) : $results;
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

		$result = $wpdb->insert( $plugins_table, $data );

		if ( $result === false ) {
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

		$result = $wpdb->update( $plugins_table, $data, $where );

		if ( $result === false ) {
			throw new \Exception( 'Failed to update plugin validation data.' );
		}

		return $this->get_plugin( $where );
	}

	/**
	 * Delete plugin data
	 *
	 * @param array $where
	 *
	 * @return false|int
	 */
	protected function delete_plugin( array $where ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		return $wpdb->delete( $plugins_table, $where );
	}


	protected function get_plugins( array $where = [] ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		$query = "SELECT * FROM $plugins_table";

		if ( ! empty( $where ) ) {
			$queryWhere = implode( ' AND ', array_map( function ( $key, $value ) {
				if ( is_array( $value ) ) {
					$in = implode( '\',', $value );

					return "$key IN ('$in')";
				}

				return "$key='$value'";
			}, array_keys( $where ), $where ) );

			$query .= " WHERE $queryWhere";
		}

		$results = $wpdb->get_results( $query );

		$plugins = [];

		if ( ! empty( $results ) ) {
			foreach ( $results as &$plugin ) {
				$plugin->validation_result = json_decode( $plugin->validation_result, true );
				$plugins[ $plugin->ID ]    = $plugin;
			}

			return $plugins;
		}

		return $results;
	}
}
