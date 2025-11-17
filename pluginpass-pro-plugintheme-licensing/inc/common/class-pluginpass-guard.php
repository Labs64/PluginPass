<?php

namespace PluginPass\Inc\Common;

use NetLicensing\Constants;
use NetLicensing\NetLicensingService;
use NetLicensing\Token;
use NetLicensing\TokenService;
use NetLicensing\ValidationResults;
use PluginPass\Inc\Common\Traits\PluginPass_Plugable;
use PluginPass\Inc\Common\Traits\PluginPass_Validatable;
use PluginPass as NS;

class PluginPass_Guard {
	use PluginPass_Validatable {
		validate as protected restValidate;
	}
	use PluginPass_Plugable;

	protected $plugin;

	/**
	 * Initialize and register plugin.
	 *
	 * @param string $api_key NetLicensing APIKey.
	 * @param string $product_number NetLicensing product number.
	 * @param string $plugin_folder Relative path to plugin folder.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 * @access   public
	 */
	public function __construct( $api_key, $product_number, $plugin_folder ) {
		if ( ! array_key_exists( $plugin_folder, get_plugins() ) ) {
			throw new \Exception( 'Plugin path "' . esc_html( $plugin_folder ) . '" not found!' );
		}

		$this->plugin = $this->get_plugin( [ 'product_number' => $product_number ] );

		$data = [
			'product_number' => $product_number,
			'plugin_folder'  => $plugin_folder,
			'api_key'        => $api_key,
		];

		$this->plugin = ( ! $this->plugin )
			? $this->create_plugin( $data )
			: $this->update_plugin( $data, [ 'product_number' => $product_number ] );

		$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		NetLicensingService::getInstance()->curl()->setUserAgent( 'NetLicensing/PHP/' . NS\PLUGIN_NAME . ' ' . PHP_VERSION . '/' . NS\PLUGIN_VERSION . ' (https://netlicensing.io)' . '; ' . $user_agent );
	}

	/**
	 * Store user consent timestamp in the database.
	 *
	 * @return $this
	 * @throws \Exception
	 */
	public function set_consent( ) {
		if (empty( $this->plugin->consented_at ) ) {
			$this->plugin = $this->update_plugin( [ 'consented_at' => gmdate( DATE_ATOM ) ], [ 'ID' => $this->plugin->ID ] );
		}

		return $this;
	}

	/**
	 * Verify, whether user consent is available.
	 *
	 * @return bool true if user consent is available
	 */
	public function has_consent() {
		return ( ! empty( $this->plugin->consented_at ) );
	}

	/**
	 * Validate plugin or theme module/feature for the current wordpress instance.
	 *
	 * @param string $module The plugin module to be checked.
	 *
	 * @return   boolean The status, whether this module/feature is available.
	 * @throws \Exception
	 * @since 1.0.0
	 * @access   public
	 */
	public function validate( $module ) {

		if ( ! $this->has_consent() ) {
			throw new NS\Inc\Exceptions\Consent_Missing_Exception();
		}

		if ( $this->is_validation_expired() ) {
			/** @var  $result ValidationResults */
			$result = self::restValidate( $this->plugin->api_key, $this->plugin->product_number );

			/** @var  $ttl \DateTime */
			$ttl               = $result->getTtl();
			$expires_ttl_at    = $ttl->format( \DateTime::ATOM );
			$validation_result = json_encode( $result->getValidations() );

			$data = [
				'expires_ttl_at'    => $expires_ttl_at,
				'validated_at'      => gmdate( DATE_ATOM ),
				'validation_result' => $validation_result,
			];

			$this->plugin = $this->update_plugin( $data, [ 'product_number' => $this->plugin->product_number ] );
		}

		if ( ! $this->plugin->validation_result || ! PluginPass_Dot::has( $this->plugin->validation_result, $module ) ) {
			return false;
		}

		$module_parsed  = explode( '.', $module );
		$product_module = reset( $module_parsed );
		$licensingModel = PluginPass_Dot::get( $this->plugin->validation_result, "$product_module.licensingModel" );

		if ( is_null( $licensingModel ) ) {
			return false;
		}

		$module .= ( $licensingModel === Constants::LICENSING_MODEL_MULTI_FEATURE )
			? '.0.valid' : '.valid';

		return PluginPass_Dot::get( $this->plugin->validation_result, $module ) === 'true';
	}

	/**
	 * Get validation result for module
	 *
	 * @param null $module
	 *
	 * @return mixed
	 */
	public function validation_result( $module = null ) {
		if ( ! $module ) {
			return $this->plugin->validation_result;
		}

		return PluginPass_Dot::get( $this->plugin->validation_result, $module );
	}

	/**
	 * Redirect user to the Shop URL for license acquisition.
	 *
	 * @param string $successUrl
	 * @param string $successUrlTitle
	 * @param string $cancelUrl
	 * @param string $cancelUrlTitle
	 *
	 * @throws NS\Inc\Exceptions\Consent_Missing_Exception
	 * @since 1.0.0
	 * @access   public
	 *
	 */
	public function open_shop( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		if ( ! $this->has_consent() ) {
			throw new NS\Inc\Exceptions\Consent_Missing_Exception();
		}

		// Validate and sanitize URLs to prevent open redirect and SSRF
		$successUrl = $this->validate_redirect_url( $successUrl );
		$cancelUrl = $this->validate_redirect_url( $cancelUrl );

		$shopToken = $this->get_shop_token( $successUrl, $successUrlTitle, $cancelUrl, $cancelUrlTitle );

		$shopUrl = $shopToken->getShopURL();

		// Validate the shop URL before redirecting
		if ( ! $this->is_safe_redirect_url( $shopUrl ) ) {
			wp_die( esc_html__( 'Invalid redirect URL', 'pluginpass-pro-plugintheme-licensing' ), esc_html__( 'Security Error', 'pluginpass-pro-plugintheme-licensing' ), array( 'response' => 403 ) );
		}

		wp_safe_redirect( $shopUrl, 307 );
		exit;
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
	 * @throws NS\Inc\Exceptions\Consent_Missing_Exception
	 * @since 1.0.0
	 * @access   public
	 */
	public function get_shop_url( $successUrl = '', $successUrlTitle = '', $cancelUrl = '', $cancelUrlTitle = '' ) {
		if ( ! $this->has_consent() ) {
			throw new NS\Inc\Exceptions\Consent_Missing_Exception();
		}

		// Validate and sanitize URLs to prevent open redirect and SSRF
		$successUrl = $this->validate_redirect_url( $successUrl );
		$cancelUrl = $this->validate_redirect_url( $cancelUrl );

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

	/**
	 * Validate and sanitize redirect URLs to prevent open redirect and SSRF attacks
	 *
	 * @param string $url The URL to validate
	 * @return string Sanitized URL or empty string if invalid
	 */
	protected function validate_redirect_url( $url ) {
		if ( empty( $url ) ) {
			return '';
		}

		// Sanitize the URL
		$url = esc_url_raw( $url );

		// Only allow HTTP and HTTPS protocols
		$parsed_url = wp_parse_url( $url );
		if ( ! $parsed_url || ! isset( $parsed_url['scheme'] ) ) {
			return '';
		}

		if ( ! in_array( $parsed_url['scheme'], array( 'http', 'https' ), true ) ) {
			return '';
		}

		// Optionally, validate against allowed domains (whitelist approach)
		// For now, we allow any valid HTTP(S) URL since this is for callbacks
		// Plugin developers can override this method for stricter validation

		return $url;
	}

	/**
	 * Check if a URL is safe for redirect (from trusted NetLicensing service)
	 *
	 * @param string $url The URL to check
	 * @return bool True if safe, false otherwise
	 */
	protected function is_safe_redirect_url( $url ) {
		if ( empty( $url ) ) {
			return false;
		}

		// Sanitize and parse the URL
		$url = esc_url_raw( $url );
		$parsed_url = wp_parse_url( $url );

		if ( ! $parsed_url || ! isset( $parsed_url['scheme'] ) || ! isset( $parsed_url['host'] ) ) {
			return false;
		}

		// Only allow HTTPS for shop URLs (more secure)
		if ( $parsed_url['scheme'] !== 'https' ) {
			return false;
		}

		// Whitelist trusted NetLicensing domains
		$trusted_domains = array(
			'netlicensing.io',
			'www.netlicensing.io',
			'go.netlicensing.io',
		);

		// Check if the host matches trusted domains
		$host = strtolower( $parsed_url['host'] );
		foreach ( $trusted_domains as $trusted_domain ) {
			if ( $host === $trusted_domain || substr( $host, -( strlen( $trusted_domain ) + 1 ) ) === '.' . $trusted_domain ) {
				return true;
			}
		}

		return false;
	}

	protected function is_validation_expired() {
		return ( ! $this->plugin->expires_ttl_at || strtotime( $this->plugin->expires_ttl_at ) <= time() );
	}

}
