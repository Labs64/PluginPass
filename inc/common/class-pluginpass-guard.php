<?php

namespace PluginPass\Inc\Common;

use NetLicensing\Constants;
use PluginPass\Inc\Common\Traits\PluginPass_Plugable;
use PluginPass\Inc\Common\Traits\PluginPass_Validatable;
use SelvinOrtiz\Dot\Dot;

class PluginPass_Guard {
	use PluginPass_Validatable;
	use PluginPass_Plugable;

	protected $plugin;

	public function __construct( $api_key, $plugin_number, $plugin_name ) {
		$this->plugin = $this->get_plugin( [ 'number' => $plugin_number ] );

		if ( $this->is_plugin_not_exits_or_validation_expired() ) {
			$result = self::validate( $api_key, $plugin_number );

			/** @var  $ttl \DateTime */
			$ttl        = $result->getTtl();
			$expires_at = $ttl->format( \DateTime::ATOM );
			$validation = json_encode( $result->getValidations() );

			$data = [
				'number'     => $plugin_number,
				'name'       => $plugin_name,
				'api_key'    => $api_key,
				'expires_at' => $expires_at,
				'validation' => $validation,
			];

			$this->plugin = ( ! $this->plugin )
				? $this->create_plugin( $data )
				: $this->update_plugin( $data, [ 'number' => $plugin_number ] );
		}
	}

	public function allow( $ability ) {

		if ( ! Dot::has( $this->plugin->validation, $ability ) ) {
			return false;
		}

		$product_module = reset( explode( '.', $ability ) );
		$licensingModel = Dot::get( $this->plugin->validation, "$product_module.licensingModel" );

		if ( is_null( $licensingModel ) ) {
			return false;
		}

		$ability .= ( $licensingModel === Constants::LICENSING_MODEL_MULTI_FEATURE )
			? '.0.valid' : '.valid';

		return Dot::get( $this->plugin->validation, $ability ) === 'true';
	}

	public function denies( $ability ) {
		return ! $this->allow( $ability );
	}

	protected function is_plugin_not_exits_or_validation_expired() {
		return ( ! $this->plugin || strtotime( $this->plugin->expires_at ) <= time() );
	}
}
