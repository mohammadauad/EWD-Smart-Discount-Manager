<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMEnqueueScripts class.
 *
 * Here you can register your CSS and JS files for the admin panel.
 */
class MXESDMEnqueueScripts
{

    public static function registerScripts()
    {

        // Register scripts and styles actions.
        add_action( 'admin_enqueue_scripts', ['MXESDMEnqueueScripts', 'enqueue'] );
    }

    public static function enqueue()
    {

        wp_enqueue_style( 'mxesdm_font_awesome', MXESDM_PLUGIN_URL . 'assets/font-awesome-4.6.3/css/font-awesome.min.css' );

        wp_enqueue_style( 'mxesdm_admin_style', MXESDM_PLUGIN_URL . 'includes/admin/assets/css/mxesdm-style.css', [ 'mxesdm_font_awesome' ], MXESDM_PLUGIN_VERSION, 'all' );

        wp_enqueue_script( 'mxesdm_admin_script', MXESDM_PLUGIN_URL . 'includes/admin/assets/js/mxesdm-script.js', [ 'jquery' ], MXESDM_PLUGIN_VERSION, true );

        wp_set_script_translations('mxesdm_admin_script', 'ewd-smart-discount-manager', plugin_dir_path(__FILE__) . 'languages');

        wp_localize_script( 'mxesdm_admin_script', 'mxesdm_admin_localize', [

            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'main_page' => admin_url( 'admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG ),
            'edit_page' => admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU),
            'weight_unit' => get_option('woocommerce_weight_unit')
        ] );
    }

}
