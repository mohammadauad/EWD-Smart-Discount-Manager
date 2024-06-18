<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

// Create table class.
require_once MXESDM_PLUGIN_ABS_PATH . 'includes/core/create-table.php';

class MXESDMBasisPluginClass
{
    private static $tableSlug = 'mx_discount_rules';
    private static $tableSlugSpecific = 'mx_discount_rules_specific';

    public static $data = [
        [
            'title'       => 'Example Discount Rule',
            'label'       => 'Example Label',
            'type'        => 'cart_adjustment',
            'filter'      => 'all_products',
            'discount'    => 'percentage',
            'value'       => '10',
            'status'      => 'inactive',
        ],
    ];

    public static function activate()
    {
        // Create discount rules table.
        self::createDiscountRulesTable();

        // Create specific discounts table.
        self::createSpecificDiscountsTable();

        // Create rule conditions table.
        self::createRuleConditionsTable();
    }

    private static function createDiscountRulesTable()
    {
        global $wpdb;
    
        $tableName = $wpdb->prefix . self::$tableSlug;
    
        $productTable = new MXESDMCreateTable($tableName);
        $productTable->varchar('title', 200, true, 'text');
        $productTable->varchar('label', 200, true, 'text');
        $productTable->varchar('type', 50, true, 'cart_adjustment');
        $productTable->varchar('filter', 50, true, 'all_products');
        $productTable->varchar('discount', 50, true, 'percentage');
        $productTable->varchar('value', 50, true, '10');
        $productTable->varchar('condition_relationship', 3, true, 'AND'); // Add this line
        $productTable->varchar('status', 20, true, 'inactive');
        $productTable->datetime('created_at');
        $productTable->create_columns();
    
        $tableCreated = $productTable->createTable();
    
        // If the table has been created - insert a dummy data.
        if ($tableCreated == 1) {
            foreach (self::$data as $value) {
                $wpdb->insert(
                    $tableName,
                    [
                        'title'       => $value['title'],
                        'label'       => $value['label'],
                        'type'        => $value['type'],
                        'filter'      => $value['filter'],
                        'discount'    => $value['discount'],
                        'value'       => $value['value'],
                        'condition_relationship' => 'AND', // Add this line
                        'status'      => $value['status'],
                        'created_at'  => date('Y-m-d H:i:s'),
                    ],
                    [
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s', // Add this line
                        '%s',
                        '%s',
                    ]
                );
            }
        }
    }
    

    private static function createSpecificDiscountsTable()
    {
        global $wpdb;
    
        $tableName = $wpdb->prefix . self::$tableSlugSpecific;
    
        $specificTable = new MXESDMCreateTable($tableName);
        $specificTable->int('discount_rule_id');
        $specificTable->int('item_id');
        $specificTable->varchar('item_type', 50, true, 'product');
        $specificTable->varchar('condition_relationship', 3, true, 'AND'); // Add this line
        $specificTable->varchar('discount', 50, true, 'percentage');
        $specificTable->varchar('value', 50, true, '10');
        $specificTable->create_columns();
    
        $tableCreated = $specificTable->createTable();
    
        // Insert example data if the table was created
        if ($tableCreated == 1) {
            // Insert dummy data into specific discounts table if needed
            // Example:
            // $wpdb->insert(
            //     $tableName,
            //     [
            //         'discount_rule_id' => 1,
            //         'item_id' => 123, // Example product/category ID
            //         'item_type' => 'product',
            //         'condition_relationship' => 'AND', // Add this line
            //         'discount' => 'fixed',
            //         'value' => '5',
            //     ],
            //     [
            //         '%d',
            //         '%d',
            //         '%s',
            //         '%s',
            //         '%s',
            //         '%s',
            //     ]
            // );
        }
    }

    private static function createRuleConditionsTable()
    {
        global $wpdb;
    
        $tableName = $wpdb->prefix . 'mx_discount_rule_conditions';
    
        $conditionsTable = new MXESDMCreateTable($tableName);
        $conditionsTable->varchar('context', 50, true, 'discount_rule');
        $conditionsTable->int('context_id');
        $conditionsTable->varchar('condition_type', 50, true, 'subtotal');
        $conditionsTable->varchar('operator', 50, true, '>');
        $conditionsTable->varchar('value', 50, true, '0');
        $conditionsTable->varchar('calculate_from', 50, true, 'subtotal');
        $conditionsTable->create_columns();
    
        $conditionsTable->createTable();
    }


    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}

register_activation_hook(__FILE__, ['MXESDMBasisPluginClass', 'activate']);
register_deactivation_hook(__FILE__, ['MXESDMBasisPluginClass', 'deactivate']);
