<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMEWDSmartDiscountManagerWPPGenerator class.
 *
 * This is a final class of the plugin.
 * Here you can find/add/remove components
 * of the plugin.
 */
final class MXESDMEWDSmartDiscountManagerWPPGenerator
{

    /**
     * Require necessary files.
     * 
     * @return void
     */
    public function includeCore()
    {        

        // Helpers.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/helpers.php';

        // Catching errors.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/Catching-Errors.php';

        // Route.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/Route.php';

        // Models.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/Model.php';

        // Views.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/View.php';

        // Controllers.
        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/Controller.php';
    }

    /**
     * Include Admin Panel Features.
     * 
     * @return void
     */
    public function includeAdminPanel()
    {

        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/admin/admin-main.php';
    }



}

/**
 * The Final class instance.
 */
$wppGenerator = new MXESDMEWDSmartDiscountManagerWPPGenerator();

/**
 * The core files (helpers, models, controllers ...).
 */
$wppGenerator->includeCore();

/**
 * Turn on the admin panel features.
 */
$wppGenerator->includeAdminPanel();


