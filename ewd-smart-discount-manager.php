<?php

/*
Plugin Name: EWD Smart Discount Manager
Plugin URI: https://github.com/mohammadauad/EWD-Smart-Discount-Manager
Description: Create flexible discount rules based on product categories, cart totals, user roles, and more.
Author: Euroweb Digital - Mohammad auad
Version: 1.0
Author URI: https://github.com/mohammadauad/
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/*
* Unique string - MXESDM
*/

/*
* Define MXESDM_PLUGIN_PATH
*
* \wp-content\plugins\ewd-smart-discount-manager\ewd-smart-discount-manager.php
*/
if (!defined('MXESDM_PLUGIN_PATH')) {

	define( 'MXESDM_PLUGIN_PATH', __FILE__ );
}

/*
* Define MXESDM_PLUGIN_URL
*
* Return /wp-content/plugins/ewd-smart-discount-manager/
*/
if (!defined('MXESDM_PLUGIN_URL')) {

	define( 'MXESDM_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}

/*
* Define MXESDM_PLUGN_BASE_NAME
*
* Return ewd-smart-discount-manager/ewd-smart-discount-manager.php
*/
if (!defined( 'MXESDM_PLUGN_BASE_NAME')) {

	define( 'MXESDM_PLUGN_BASE_NAME', plugin_basename( __FILE__ ) );
}

/*
* Define MXESDM_TABLE_SLUG
*/
if (!defined('MXESDM_TABLE_SLUG')) {

	define( 'MXESDM_TABLE_SLUG', 'mx_discount_rules' );
}

/*
* Define MXESDM_PLUGIN_ABS_PATH
* 
* \wp-content\plugins\ewd-smart-discount-manager/
*/
if (!defined( 'MXESDM_PLUGIN_ABS_PATH')) {

	define( 'MXESDM_PLUGIN_ABS_PATH', dirname( MXESDM_PLUGIN_PATH ) . '/' );
}

/*
* Define MXESDM_PLUGIN_VERSION
* 
* The version of all CSS and JavaScript files in this plugin.
*/
if (!defined('MXESDM_PLUGIN_VERSION')) {

	define( 'MXESDM_PLUGIN_VERSION', '1.1' ); 
}

/*
* Define MXESDM_DISCOUNT_RULES_MENU_SLUG
*/
if (!defined('MXESDM_DISCOUNT_RULES_MENU_SLUG')) {

	define( 'MXESDM_DISCOUNT_RULES_MENU_SLUG', 'mxesdm-ewd-smart-discount-manager-discount-rules' );
}

/*
* Define MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU
*/
if (!defined( 'MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU')) {

	// Single table item menu.
	define( 'MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU', 'mxesdm-ewd-smart-discount-manager-discount-rule' );
}

/*
* Define MXESDM_CREATE_DISCOUNT_RULE_MENU
*/
if (!defined('MXESDM_CREATE_DISCOUNT_RULE_MENU')) {

	// Table item menu.
	define( 'MXESDM_CREATE_DISCOUNT_RULE_MENU', 'mxesdm-ewd-smart-discount-manager-create-discount-rule' );
}

/**
 * activation|deactivation
 */
require_once plugin_dir_path( __FILE__ ) . 'install.php';

/*
* Registration hooks.
*/
// Activation.
register_activation_hook( __FILE__, ['MXESDMBasisPluginClass', 'activate'] );

// Deactivation.
register_deactivation_hook( __FILE__, ['MXESDMBasisPluginClass', 'deactivate'] );

/*
* Include the main MXESDMEWDSmartDiscountManager class.
*/
if (!class_exists('MXESDMEWDSmartDiscountManager')) {

	require_once plugin_dir_path( __FILE__ ) . 'includes/final-class.php';

	/*
	* Translate plugin.
	*/
	function mxesdm_translate()
	{

		load_plugin_textdomain( 'ewd-smart-discount-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	add_action( 'plugins_loaded', 'mxesdm_translate' );
}
