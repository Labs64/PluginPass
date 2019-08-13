<?php

namespace PluginPass\Inc\Common\Traits;

use NetLicensing\Constants;
use NetLicensing\Context;
use NetLicensing\LicenseeService;
use NetLicensing\NetLicensingService;
use NetLicensing\ValidationParameters;
use PluginPass as NS;

trait PluginPass_Validatable {

	protected static function get_context( $api_key ) {
		$context = new Context();
		$context->setApiKey( $api_key );
		$context->setSecurityMode( Constants::APIKEY_IDENTIFICATION );

		return $context;
	}

	protected static function validate( $api_key, $product_number ) {
		$context = self::get_context( $api_key );

		$validation_parameters = new ValidationParameters();
		$validation_parameters->setProductNumber( $product_number );
		$validation_parameters->setLicenseeName( get_bloginfo( 'name' ) );


		NetLicensingService::getInstance()->curl()->setUserAgent( 'NetLicensing/PHP/' . NS\PLUGIN_NAME . ' ' . PHP_VERSION . '/' . NS\PLUGIN_VERSION . ' (https://netlicensing.io)' . '; ' . $_SERVER['HTTP_USER_AGENT'] );

		return LicenseeService::validate( $context, self::get_licensee_number(), $validation_parameters );
	}


	protected static function get_licensee_number() {
		return $host = parse_url( get_home_url() )['host'];
	}
}