<?php

namespace PluginPass\Inc\Exceptions;

use Throwable;

class Consent_Missing_Exception extends \Exception {
	public function __construct( $message = 'No Consent', $code = 0, Throwable $previous = null ) {
		parent::__construct( $message, $code, $previous );
	}
}