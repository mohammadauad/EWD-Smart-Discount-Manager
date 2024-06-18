<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Require Display Error feature.
require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/error_handle/Display-Error.php';

// Require Handle Error feature.
require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/error_handle/Error-Handle.php';

/**
 * The MXESDMCatchingErrors class.
 *
 * Catching errors.
 */
class MXESDMCatchingErrors
{

	/**
	* Show notification missing class or methods.
	*/
	public static function catchClassAttributesError( $className, $method )
	{

		$errorClassInstance = new MXESDMErrorHandle();

		$errorDisplay = $errorClassInstance->classAttributesError( $className, $method );

		$error = NULL;

		if ($errorDisplay !== true) {
			$error = $errorDisplay;
		}		

		return $error;
	}

}
