<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMDiscountRulesController class.
 *
 * Here you can connect your model with a view.
 */
class MXESDMDiscountRulesController extends MXESDMController
{
    protected $modelInstance;

    public function __construct()
    {
        $this->modelInstance = new MXESDMDiscountRulesModel();
    }
    
    public function index()
    {
        return new MXESDMMxView('discount-rules');
    }

    // Edit a discount rule.
    public function editDiscountRule()
    {
        global $wpdb;
    
        $deleteId = isset($_GET['delete']) ? trim(sanitize_text_field($_GET['delete'])) : false;
        if ($deleteId) {
            if (isset($_GET['mxesdm_nonce']) || wp_verify_nonce($_GET['mxesdm_nonce'], 'delete')) {
                $this->modelInstance->deletePermanently($deleteId);
            }
            mxesdmAdminRedirect(admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG . '&item_status=trash'));
            return;
        }
    
        $restore_id = isset($_GET['restore']) ? trim(sanitize_text_field($_GET['restore'])) : false;
        if ($restore_id) {
            if (isset($_GET['mxesdm_nonce']) || wp_verify_nonce($_GET['mxesdm_nonce'], 'restore')) {
                $this->modelInstance->restoreDiscountRule($restore_id);
            }
            mxesdmAdminRedirect(admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG . '&item_status=trash'));
            return;
        }
    
        $trash_id = isset($_GET['trash']) ? trim(sanitize_text_field($_GET['trash'])) : false;
        if ($trash_id) {
            if (isset($_GET['mxesdm_nonce']) || wp_verify_nonce($_GET['mxesdm_nonce'], 'trash')) {
                $this->modelInstance->moveDiscountRuleToTrash($trash_id);
            }
            mxesdmAdminRedirect(admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG));
            return;
        }
    
        $item_id = isset($_GET['edit-item']) ? trim(sanitize_text_field($_GET['edit-item'])) : 0;
        $data = $this->modelInstance->getRow(NULL, 'id', intval($item_id));

        if ($data == NULL) {
            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == NULL) {
                mxesdmAdminRedirect(admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG));
            } else {
                mxesdmAdminRedirect($_SERVER['HTTP_REFERER']);
            }
            return;
        }
    
        if ($data->filter === 'specific_products' || $data->filter === 'specific_categories') {
            $data->specific_discounts = $wpdb->get_results($wpdb->prepare("
                SELECT s.*, 
                CASE 
                    WHEN s.item_type = 'product' THEN p.post_title 
                    ELSE t.name 
                END as name
                FROM {$wpdb->prefix}mx_discount_rules_specific s
                LEFT JOIN {$wpdb->posts} p ON s.item_id = p.ID AND s.item_type = 'product'
                LEFT JOIN {$wpdb->terms} t ON s.item_id = t.term_id AND s.item_type = 'category'
                WHERE s.discount_rule_id = %d
            ", $data->id), ARRAY_A);
    


            foreach ($data->specific_discounts as &$specific_discount) {

                $specific_discount['conditions'] = $wpdb->get_results($wpdb->prepare("
                    SELECT *
                    FROM {$wpdb->prefix}mx_discount_rule_conditions
                    WHERE context = 'specific_discount' AND context_id = %d
                ", $specific_discount['item_id']), ARRAY_A);
            }

        } else {
            $data->specific_discounts = [];
        }
    
        $data->general_conditions = $wpdb->get_results($wpdb->prepare("
            SELECT *
            FROM {$wpdb->prefix}mx_discount_rule_conditions
            WHERE context = 'discount_rule' AND context_id = %d
        ", $data->id), ARRAY_A);


    
        return new MXESDMMxView('edit-discount-rule', $data);
    }
    
    

    // Create a discount rule.
    public function createDiscountRule() {
        return new MXESDMMxView('create-discount-rule');
    }
}
