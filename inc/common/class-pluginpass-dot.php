<?php

namespace PluginPass\Inc\Common;

class PluginPass_Dot {
	/**
	 * Returns whether or not the $key exists within $arr
	 *
	 * @param array $array
	 * @param string $key
	 *
	 * @return bool
	 */
	public static function has( $array, $key ) {
		if ( ! $array ) {
			return false;
		}

		$keys = explode( '.', $key );

		if ( count( $keys ) > 1 ) {
			foreach ( $keys as $key ) {
				if ( ! array_key_exists( $key, $array ) ) {
					return false;
				}
				$array = $array[ $key ];
			}

			return true;
		}

		return array_key_exists( $key, $array );
	}

	/**
	 * Returns he value of $key if found in $arr or $default
	 *
	 * @param array $array
	 * @param string $key
	 * @param null|mixed $default
	 *
	 * @return mixed
	 */
	public static function get( $array, $key, $default = null ) {
		if ( ! $array ) {
			return $default;
		}

		$keys = explode( '.', $key );

		if ( count( $keys ) > 1 ) {
			foreach ( $keys as $key ) {
				if ( ! array_key_exists( $key, $array ) ) {
					return $default;
				}
				$array = $array[ $key ];
			}

			return $array;
		}

		return array_key_exists( $key, $array ) ? $array[ $key ] : $default;
	}
}