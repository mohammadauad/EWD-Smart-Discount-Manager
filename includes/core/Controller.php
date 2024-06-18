<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMController abstract class.
 *
 * Basic settings of Controller.
 */
abstract class MXESDMController
{

    /**
    * Catch missing methods on the controller
    */
    public function __call( $name, $arguments ) {

        echo esc_attr( 'Missing method "' . $name . '"!' );
    }

}
