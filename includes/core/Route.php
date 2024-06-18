<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Require Route Registrar class.
require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/Route-Registrar.php';

/**
 * The MXESDMRoute class.
 *
 * This class works together with 
 * MXESDMRouteRegistrar class and helps
 * create a menu pate in the admin panel.
 */
class MXESDMRoute
{
    
    public static function get( ...$args )
    {

        return new MXESDMRouteRegistrar( ...$args );
    }
    
}
