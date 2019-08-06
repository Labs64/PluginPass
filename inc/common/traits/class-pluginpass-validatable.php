<?php

namespace PluginPass\Inc\Common\Traits;

use NetLicensing\Constants;
use NetLicensing\Context;
use NetLicensing\LicenseeService;
use NetLicensing\ValidationParameters;

trait PluginPass_Validatable{

	protected static function validate( $api_key, $plugin_number ) {
		$context = new Context();
		$context->setApiKey( $api_key );
		$context->setSecurityMode( Constants::APIKEY_IDENTIFICATION );

		// TODO(RVA) change to production
		$context->setBaseUrl( 'http://localhost:28080/core/v2/rest' );

		$validation_parameters = new ValidationParameters();
		$validation_parameters->setProductNumber( $plugin_number );
		$validation_parameters->setLicenseeName( get_bloginfo( 'name' ) );

		$host = parse_url( get_home_url() )['host'];

		return LicenseeService::validate( $context, $host, $validation_parameters );
	}
}