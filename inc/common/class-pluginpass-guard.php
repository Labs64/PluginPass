<?php

namespace PluginPass\Inc\Common;

use NetLicensing\Constants;
use NetLicensing\Token;
use NetLicensing\TokenService;
use NetLicensing\ValidationResults;
use PluginPass\Inc\Common\Traits\PluginPass_Plugable;
use PluginPass\Inc\Common\Traits\PluginPass_Validatable;

class PluginPass_Guard {
	use PluginPass_Validatable {
		validate as restValidate;
	}
	use PluginPass_Plugable;

	protected $plugin;

	/**
	 * Initialize and register plugin.
	 *
	 * @since 1.0.0
	 * @access   public
	 * @param    string               $api_key             NetLicensing APIKey.
	 * @param    string               $product_number      NetLicensing product number.
	 * @param    string               $plugin_name         The plugin name.
	 *
	 * @throws \Exception
	 */
	public function __construct( $api_key, $product_number, $plugin_name ) {
		$this->plugin = $this->get_plugin( [ 'number' => $product_number ] );

		if ( $this->is_plugin_not_exits_or_validation_expired() ) {
			/** @var  $result ValidationResults*/
			$result = self::restValidate( $api_key, $product_number );

			/** @var  $ttl \DateTime */
			$ttl        = $result->getTtl();
			$expires_at = $ttl->format( \DateTime::ATOM );
			$validation = json_encode( $result->getValidations() );

			$data = [
				'number'     => $product_number,
				'name'       => $plugin_name,
				'api_key'    => $api_key,
				'expires_at' => $expires_at,
				'validation' => $validation,
			];

			$this->plugin = ( ! $this->plugin )
				? $this->create_plugin( $data )
				: $this->update_plugin( $data, [ 'number' => $product_number ] );
		}
	}

	/**
	 * Validate plugin feature for the current wordpress instance.
	 *
	 * @since 1.0.0
	 * @access   public
	 * @param    string               $feature         The plugin feature to be checked.
	 * @return   boolean                               The status, whether this feature is available.
	 */
	public function validate( $feature ) {

		if ( ! PluginPass_Dot::has( $this->plugin->validation, $feature ) ) {
			return false;
		}

		$product_module = reset( explode( '.', $feature ) );
		$licensingModel = PluginPass_Dot::get( $this->plugin->validation, "$product_module.licensingModel" );

		if ( is_null( $licensingModel ) ) {
			return false;
		}

		$feature .= ( $licensingModel === Constants::LICENSING_MODEL_MULTI_FEATURE )
			? '.0.valid' : '.valid';

		return PluginPass_Dot::get( $this->plugin->validation, $feature ) === 'true';
	}

	/**
	 * Redirect user to the Shop URL for license acquisition.
	 *
	 * @since 1.0.0
	 * @access   public
	 * @param string $successUrl
	 * @param string $successUrlTitle
	 * @param string $cancelUrl
	 * @param string $cancelUrlTitle
	 */
	public function open_shop( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		$shopToken = $this->get_shop_token( $successUrl, $successUrlTitle, $cancelUrl, $cancelUrlTitle );

		$shopUrl = $shopToken->getShopURL();

		header( "Location:$shopUrl", true, 307 );
	}

	/**
	 * Generate shop URL for license acquisition.
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function get_shop_url( $title, array $attrs = [], $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		$shopToken = $this->get_shop_token( $successUrl, $successUrlTitle, $cancelUrl, $cancelUrlTitle );

		$shopUrl = $shopToken->getShopURL();

		$attrsMap = array_map( function ( $key, $value ) {
			return "$key=\"$value\"";
		}, array_keys( $attrs ), $attrs );

		$attrsString = implode( " ", $attrsMap );

		echo "<a href='$shopUrl' $attrsString>$title</a>";
	}

	protected function get_shop_token( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		$shopToken = new Token();
		$shopToken->setTokenType( 'SHOP' );
		$shopToken->setLicenseeNumber( self::get_licensee_number() );

		if ( $successUrl ) {
			$shopToken->setSuccessURL( $successUrl );
		}

		if ( $successUrlTitle ) {
			$shopToken->setSuccessURLTitle( $successUrlTitle );
		}

		if ( $cancelUrl ) {
			$shopToken->setCancelURL( $cancelUrl );
		}

		if ( $cancelUrlTitle ) {
			$shopToken->setCancelURLTitle( $cancelUrlTitle );
		}

		$shopToken = TokenService::create( self::get_context( $this->plugin->api_key ), $shopToken );

		return $shopToken;
	}

	protected function is_plugin_not_exits_or_validation_expired() {
		return ( ! $this->plugin || strtotime( $this->plugin->expires_at ) <= time() );
	}
}
