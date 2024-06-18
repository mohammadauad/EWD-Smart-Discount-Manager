<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMAdminMain class.
 *
 * Here you can register you classes.
 */
class MXESDMAdminMain
{

    /*
    * List of model names used in the plugin.
    */
    public $modelsCollection = [
        'MXESDMDiscountRulesModel'
    ];

    /*
    * Additional classes.
    */
    public function additionalClasses()
    {

        // Enqueue scripts.
        mxesdmRequireClassFileAdmin( 'enqueue-scripts.php' );

        MXESDMEnqueueScripts::registerScripts();

        // Custom table.
        mxesdmRequireClassFileAdmin( 'discount-rules-table.php' );

    }

    /*
    * Models Connection.
    */
    public function modelsCollection()
    {

        foreach ($this->modelsCollection as $model) {            
            mxesdmUseModel( $model );
        }
    }

    /**
    * AJAX actions registration.
    */
    public function registrationAjaxActions()
    {

        MXESDMDiscountRulesModel::wpAjax();
    }

    /*
    * Routes collection.
    */
    public function routesCollection()
    {

        // Discount rule item.
        MXESDMRoute::get( 'MXESDMDiscountRulesController', 'index', '', [
            'page_title' => __('EWD Discount Manager', 'ewd-smart-discount-manager'),
            'menu_title' => __('EWD Discount Manager', 'ewd-smart-discount-manager')
        ] );
        
            // Edit a single table item.
            MXESDMRoute::get( 'MXESDMDiscountRulesController', 'editDiscountRule', 'NULL', [
                'page_title' => __('Edit Discount rule', 'ewd-smart-discount-manager'),
            ], MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU );

            // Create a single table item.
            MXESDMRoute::get( 'MXESDMDiscountRulesController', 'createDiscountRule', 'NULL', [
                'page_title' => __('Create Discount rule', 'ewd-smart-discount-manager'),
            ], MXESDM_CREATE_DISCOUNT_RULE_MENU );

    }

}

/**
 * Initialization.
 */
$adminClassInstance = new MXESDMAdminMain();

/**
 * Include classes.
 */
$adminClassInstance->additionalClasses();

/**
 * Include models.
 */
$adminClassInstance->modelsCollection();

/**
 * AJAX requests registration.
 */
$adminClassInstance->registrationAjaxActions();

/**
 * Include controllers.
 */
$adminClassInstance->routesCollection();
