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

	protected $has_consent = false;

	/**
	 * Initialize and register plugin.
	 *
	 * @param string $api_key NetLicensing APIKey.
	 * @param string $product_number NetLicensing product number.
	 * @param string $plugin_name The plugin name.
	 * @param boolean $has_consent Indicate whether the author of the plugin has the user's consent to the use his personal data.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 * @access   public
	 */
	public function __construct( $api_key, $product_number, $plugin_name, $has_consent = false ) {
		$this->plugin      = $this->get_plugin( [ 'product_number' => $product_number ] );
		$this->has_consent = $has_consent;

		$data = [
			'product_number' => $product_number,
			'plugin_name'    => $plugin_name,
			'api_key'        => $api_key,
		];

		$this->plugin = ( ! $this->plugin )
			? $this->create_plugin( $data )
			: $this->update_plugin( $data, [ 'product_number' => $product_number ] );
	}

	/**
	 * Indicate whether the author of the plugin has the user's consent to the use his personal data.
	 * @return bool
	 */
	public function has_consent() {
		return ( ! empty( $this->plugin->consented_at ) || $this->has_consent );
	}

	/**
	 * Validate plugin feature for the current wordpress instance.
	 *
	 * @param string $feature The plugin feature to be checked.
	 *
	 * @return   boolean The status, whether this feature is available.
	 * @throws \Exception
	 * @since 1.0.0
	 * @access   public
	 */
	public function validate( $feature ) {

		if ( ! $this->has_consent() ) {
			return false;
		}

		if ( $this->is_validation_expired()) {
			/** @var  $result ValidationResults */
			$result = self::restValidate( $this->plugin->api_key, $this->plugin->product_number );

			/** @var  $ttl \DateTime */
			$ttl               = $result->getTtl();
			$expires_ttl_at    = $ttl->format( \DateTime::ATOM );
			$validation_result = json_encode( $result->getValidations() );

			$data = [
				'expires_ttl_at'    => $expires_ttl_at,
				'validated_at'      => date( DATE_ATOM ),
				'validation_result' => $validation_result,
			];

			if ( empty( $this->plugin->consented_at ) ) {
				$data['consented_at'] = date( DATE_ATOM );
			}

			$this->plugin = $this->update_plugin( $data, [ 'product_number' => $this->plugin->product_number ] );
		}

		if ( ! $this->plugin->validation_result || ! PluginPass_Dot::has( $this->plugin->validation_result, $feature ) ) {
			return false;
		}

		$feature_parsed = explode( '.', $feature );
		$product_module = reset( $feature_parsed );
		$licensingModel = PluginPass_Dot::get( $this->plugin->validation_result, "$product_module.licensingModel" );

		if ( is_null( $licensingModel ) ) {
			return false;
		}

		$feature .= ( $licensingModel === Constants::LICENSING_MODEL_MULTI_FEATURE )
			? '.0.valid' : '.valid';

		return PluginPass_Dot::get( $this->plugin->validation_result, $feature ) === 'true';
	}

	/**
	 * Get validation result for feature
	 *
	 * @param null $feature
	 *
	 * @return mixed
	 */
	public function validation_result( $feature = null ) {
		if ( ! $feature ) {
			return $this->plugin->validation_result;
		}

		return PluginPass_Dot::get( $this->plugin->validation_result, $feature );
	}

	/**
	 * Redirect user to the Shop URL for license acquisition.
	 *
	 * @param string $successUrl
	 * @param string $successUrlTitle
	 * @param string $cancelUrl
	 * @param string $cancelUrlTitle
	 *
	 * @since 1.0.0
	 * @access   public
	 */
	public function open_shop( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		$shopToken = $this->get_shop_token( $successUrl, $successUrlTitle, $cancelUrl, $cancelUrlTitle );

		$shopUrl = $shopToken->getShopURL();

		header( "Location:$shopUrl", true, 307 );
	}

	/**
	 * Generate shop URL for license acquisition.
	 *
	 * @param string $successUrl
	 * @param string $successUrlTitle
	 * @param string $cancelUrl
	 * @param string $cancelUrlTitle
	 *
	 * @return string
	 * @since 1.0.0
	 * @access   public
	 */
	public function get_shop_url( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		return $this->get_shop_token( $successUrl, $successUrlTitle, $cancelUrl, $cancelUrlTitle )->getShopURL();
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

	protected function is_validation_expired() {
		return ( ! $this->plugin->expires_ttl_at || strtotime( $this->plugin->expires_ttl_at ) <= time() );
	}
}
