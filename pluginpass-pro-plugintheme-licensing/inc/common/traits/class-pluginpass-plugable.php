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

		if ( empty( $where ) ) {
			$query = "SELECT * FROM $plugins_table";
			$results = $wpdb->get_results( $query );
		} else {
			// Build safe WHERE clause using wpdb->prepare()
			$where_clauses = [];
			$where_values = [];
			
			foreach ( $where as $key => $value ) {
				// Sanitize column name to prevent SQL injection
				$safe_key = preg_replace( '/[^a-zA-Z0-9_]/', '', $key );
				
				if ( is_array( $value ) ) {
					// Use IN clause for arrays
					$placeholders = implode( ', ', array_fill( 0, count( $value ), is_numeric( reset( $value ) ) ? '%d' : '%s' ) );
					$where_clauses[] = "$safe_key IN ($placeholders)";
					$where_values = array_merge( $where_values, array_values( $value ) );
				} else {
					// Use equality for single values
					$where_clauses[] = is_numeric( $value ) ? "$safe_key = %d" : "$safe_key = %s";
					$where_values[] = $value;
				}
			}
			
			$query = "SELECT * FROM $plugins_table WHERE " . implode( ' AND ', $where_clauses );
			$results = $wpdb->get_results( $wpdb->prepare( $query, $where_values ) );
		}

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
