<?php

if (!defined('ABSPATH')) exit;

class MXESDMDiscountRulesModel extends MXESDMModel
{
    /**
     * Register AJAX handlers.
     */
    public static function wpAjax()
    {
        add_action('wp_ajax_mxesdm_update_discount_rule', ['MXESDMDiscountRulesModel', 'prepareDiscountRuleEdition']);
        add_action('wp_ajax_mxesdm_create_discount_rule', ['MXESDMDiscountRulesModel', 'prepareDiscountRuleCreation']);
        add_action('wp_ajax_mxesdm_bulk_actions', ['MXESDMDiscountRulesModel', 'prepareBulkActions']);
        add_action('wp_ajax_mxesdm_search_products_or_categories', ['MXESDMDiscountRulesModel', 'searchProductsOrCategories']);
    }

    /**
     * Prepare bulk actions.
     */
    public static function prepareBulkActions()
    {
        if (empty($_POST['nonce'])) wp_die('0');
        if (wp_verify_nonce($_POST['nonce'], 'bulk-mxesdm_plural')) {
            switch ($_POST['bulk_action']) {
                case 'delete':
                    if (current_user_can('edit_posts')) {
                        self::actionDelete($_POST['ids']);
                    }
                    break;
                case 'restore':
                    if (current_user_can('edit_posts')) {
                        self::actionRestore($_POST['ids']);
                    }
                    break;
                case 'trash':
                    if (current_user_can('edit_posts')) {
                        self::actionTrash($_POST['ids']);
                    }
                    break;
            }
        }
        wp_die();
    }

    /**
     * Handle bulk delete action.
     */
    public static function actionDelete($ids)
    {
        foreach ($ids as $id) {
            (new self)->deletePermanently($id);
        }
    }

    /**
     * Handle bulk restore action.
     */
    public static function actionRestore($ids)
    {
        foreach ($ids as $id) {
            (new self)->restoreDiscountRule($id);
        }
    }

    /**
     * Handle bulk trash action.
     */
    public static function actionTrash($ids)
    {
        foreach ($ids as $id) {
            (new self)->moveDiscountRuleToTrash($id);
        }
    }

    /**
     * Prepare DiscountRule creation.
     */
    public static function prepareDiscountRuleCreation()
    {
        if (empty($_POST['nonce'])) wp_die('0');
        if (wp_verify_nonce($_POST['nonce'], 'mxesdm_nonce_request')) {
            global $wpdb;
            $data = [
                'title' => sanitize_text_field($_POST['title']),
                'label' => sanitize_text_field($_POST['label']),
                'type' => sanitize_text_field($_POST['type']),
                'filter' => sanitize_text_field($_POST['filter']),
                'discount' => sanitize_text_field($_POST['discount']),
                'value' => sanitize_text_field($_POST['value']),
                'condition_relationship' => sanitize_text_field($_POST['condition_relationship']),
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
            ];

            self::createDiscountRule($data);
            $discount_rule_id = $wpdb->insert_id;

            // General rule conditions
            if (!empty($_POST['general_conditions'])) {
                foreach ($_POST['general_conditions'] as $condition) {
                    $condition_data = [
                        'context' => 'discount_rule',
                        'context_id' => $discount_rule_id,
                        'condition_type' => sanitize_text_field($condition['condition_type']),
                        'operator' => esc_sql($condition['operator']),
                        'value' => sanitize_text_field($condition['value']),
                        'calculate_from' => sanitize_text_field($condition['calculate_from']),
                    ];
                    self::createRuleCondition($condition_data);
                }
            }

            // Specific discounts
            if (in_array($data['filter'], ['specific_products', 'specific_categories'])) {
                foreach ($_POST['mxesdm_discount_specific'] as $item => $discount) {
                    $specific_data = [
                        'discount_rule_id' => $discount_rule_id,
                        'item_id' => $item,
                        'item_type' => $data['filter'] === 'specific_products' ? 'product' : 'category',
                        'discount' => sanitize_text_field($discount),
                        'value' => sanitize_text_field($_POST['mxesdm_value_specific'][$item]),
                        'condition_relationship' => sanitize_text_field($_POST['mxesdm_condition_relationship'][$item]),
                    ];
                    self::createSpecificDiscount($specific_data);
                    $specific_discount_id = $wpdb->insert_id;

                    if (!empty($_POST['mxesdm_conditions_specific'][$item])) {
                        foreach ($_POST['mxesdm_conditions_specific'][$item] as $condition) {
                            $condition_data = [
                                'context' => 'specific_discount',
                                'context_id' => $specific_discount_id,
                                'condition_type' => sanitize_text_field($condition['condition_type']),
                                'operator' => esc_sql($condition['operator']),
                                'value' => sanitize_text_field($condition['value']),
                                'calculate_from' => sanitize_text_field($condition['calculate_from']),
                            ];
                            self::createRuleCondition($condition_data);
                        }
                    }
                }
            }
            wp_send_json_success(['id' => $discount_rule_id]);
        }
        wp_die();
    }

    /**
     * Create a new DiscountRule.
     */
    public static function createDiscountRule($data)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;

        $wpdb->insert(
            $tableName,
            [
                'title' => $data['title'],
                'label' => $data['label'],
                'type' => $data['type'],
                'filter' => $data['filter'],
                'discount' => $data['discount'],
                'value' => $data['value'],
                'condition_relationship' => $data['condition_relationship'],
                'status' => $data['status'],
                'created_at' => $data['created_at'],
            ],
            [
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
            ]
        );
    }

    /**
     * Create a new specific discount.
     */
    public static function createSpecificDiscount($data)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'mx_discount_rules_specific';

        $wpdb->insert(
            $tableName,
            [
                'discount_rule_id' => $data['discount_rule_id'],
                'item_id' => $data['item_id'],
                'item_type' => $data['item_type'],
                'condition_relationship' => $data['condition_relationship'],
                'discount' => $data['discount'],
                'value' => $data['value'],
            ],
            [
                '%d', '%d', '%s', '%s', '%s', '%s'
            ]
        );
    }

    /**
     * Prepare DiscountRule updating.
     */
    public static function prepareDiscountRuleEdition()
    {
        if (empty($_POST['nonce'])) wp_die('0');
        if (wp_verify_nonce($_POST['nonce'], 'mxesdm_nonce_request')) {

            global $wpdb;

            $data = [
                'id' => sanitize_text_field($_POST['id']),
                'title' => sanitize_text_field($_POST['title']),
                'label' => sanitize_text_field($_POST['label']),
                'type' => sanitize_text_field($_POST['type']),
                'filter' => sanitize_text_field($_POST['filter']),
                'discount' => sanitize_text_field($_POST['discount']),
                'value' => sanitize_text_field($_POST['value']),
                'condition_relationship' => sanitize_text_field($_POST['condition_relationship']),
                'status' => sanitize_text_field($_POST['status']),
            ];

            self::updateDatabaseDiscountRule($data);

            // Handle general rules conditions
            $general_conditions = $_POST['general_conditions'] ?? [];
            self::updateGeneralConditions($data['id'], $general_conditions);

            // Handle specific discounts if applicable
            if (in_array($data['filter'], ['specific_products', 'specific_categories'])) {
                $existingSpecifics = $wpdb->get_col($wpdb->prepare("
                    SELECT item_id 
                    FROM {$wpdb->prefix}mx_discount_rules_specific
                    WHERE discount_rule_id = %d
                ", $data['id']));
                
     
                $updatedSpecifics = isset($_POST['mxesdm_discount_specific']) ? array_keys($_POST['mxesdm_discount_specific']) : [];

                $specificsToRemove = array_diff($existingSpecifics, $updatedSpecifics);

                // Remove specifics that are no longer present
                foreach ($specificsToRemove as $item_id) {
                    self::removeSpecificItemById($item_id);
                }


                foreach ($_POST['mxesdm_discount_specific'] as $item => $label) {
                    $specific_data = [
                        'discount_rule_id' => $data['id'],
                        'item_id' => $item,
                        'item_type' => $data['filter'] === 'specific_products' ? 'product' : 'category',
                        'discount' => sanitize_text_field($_POST['mxesdm_discount_specific'][$item]),
                        'value' => sanitize_text_field($_POST['mxesdm_value_specific'][$item]),
                        'condition_relationship' => sanitize_text_field($_POST['mxesdm_condition_relationship'][$item]),
                    ];

                    self::upsertSpecificDiscount($specific_data);

                    // Handle specific rules conditions
                    $specific_conditions = $_POST['mxesdm_conditions_specific'][$item] ?? [];
    
                    self::updateSpecificConditions($data['id'], $item, $specific_conditions);
                }
            }
            wp_send_json_success();
        }
        wp_die();
    }

    /**
     * Update DiscountRule in the database.
     */
    public static function updateDatabaseDiscountRule($data)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;

        $wpdb->update(
            $tableName,
            [
                'title' => $data['title'],
                'label' => $data['label'],
                'type' => $data['type'],
                'filter' => $data['filter'],
                'discount' => $data['discount'],
                'value' => $data['value'],
                'condition_relationship' => $data['condition_relationship'],
                'status' => $data['status'],
            ],
            ['id' => $data['id']],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );
    }

    /**
     * Update general conditions.
     */
    public static function updateGeneralConditions($discount_rule_id, $conditions)
    {
        global $wpdb;

        // Remove existing general conditions
        $wpdb->delete(
            "{$wpdb->prefix}mx_discount_rule_conditions",
            [
                'context' => 'discount_rule',
                'context_id' => $discount_rule_id
            ],
            ['%s', '%d']
        );

        // Insert new general conditions
        foreach ($conditions as $condition) {
            $wpdb->insert(
                "{$wpdb->prefix}mx_discount_rule_conditions",
                [
                    'context' => 'discount_rule',
                    'context_id' => $discount_rule_id,
                    'condition_type' => sanitize_text_field($condition['condition_type']),
                    'operator' => esc_sql($condition['operator']),
                    'value' => sanitize_text_field($condition['value']),
                    'calculate_from' => sanitize_text_field($condition['calculate_from']),
                ],
                ['%s', '%d', '%s', '%s', '%s', '%s']
            );
        }
    }

    /**
     * Update specific conditions.
     */
    public static function updateSpecificConditions($discount_rule_id, $specific_item_id, $conditions)
    {
        global $wpdb;

        // Remove existing specific conditions
        $wpdb->delete(
            "{$wpdb->prefix}mx_discount_rule_conditions",
            ['context' => 'specific_discount', 'context_id' => $specific_item_id],
            ['%s', '%d']
        );





        // Insert new specific conditions
        foreach ($conditions as $condition) {
            $wpdb->insert(
                "{$wpdb->prefix}mx_discount_rule_conditions",
                [
                    'context' => 'specific_discount',
                    'context_id' => $specific_item_id,
                    'condition_type' => sanitize_text_field($condition['condition_type']),
                    'operator' => esc_sql($condition['operator']),
                    'value' => sanitize_text_field($condition['value']),
                    'calculate_from' => sanitize_text_field($condition['calculate_from']),
                ],
                ['%s', '%d', '%s', '%s', '%s', '%s']
            );
            // echo '<pre>';
            // print_r($wpdb->last_query);
        }
        // die;

    }

    /**
     * Upsert rule condition (update if exists, insert if not).
     */
    public static function upsertRuleCondition($data)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'mx_discount_rule_conditions';
        $existing = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*)
            FROM {$tableName}
            WHERE context = %s AND context_id = %d AND condition_type = %s
        ", $data['context'], $data['context_id'], $data['condition_type']));
        if ($existing) {
            $wpdb->update(
                $tableName,
                [
                    'operator' => $data['operator'],
                    'value' => $data['value'],
                    'calculate_from' => $data['calculate_from']
                ],
                [
                    'context' => $data['context'],
                    'context_id' => $data['context_id'],
                    'condition_type' => $data['condition_type']
                ],
                [
                    '%s',
                    '%s',
                    '%s'
                ],
                [
                    '%s',
                    '%d',
                    '%s'
                ]
            );
        } else {
            self::createRuleCondition($data);
        }
    }

    /**
     * Create rule condition.
     */
    public static function createRuleCondition($data)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'mx_discount_rule_conditions';
        $wpdb->insert(
            $tableName,
            [
                'context' => $data['context'],
                'context_id' => $data['context_id'],
                'condition_type' => $data['condition_type'],
                'operator' => $data['operator'],
                'value' => $data['value'],
                'calculate_from' => $data['calculate_from']
            ],
            [
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            ]
        );
    }

    /**
     * Upsert specific discount (update if exists, insert if not).
     */
    public static function upsertSpecificDiscount($data)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'mx_discount_rules_specific';

        // Check if specific discount already exists
        $existing = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$tableName}
            WHERE discount_rule_id = %d AND item_id = %d AND item_type = %s
        ", $data['discount_rule_id'], $data['item_id'], $data['item_type']));

        if ($existing) {
            // Update existing specific discount
            $wpdb->update(
                $tableName,
                [
                    'discount' => $data['discount'],
                    'value' => $data['value'],
                    'condition_relationship' => $data['condition_relationship'],
                ],
                [
                    'discount_rule_id' => $data['discount_rule_id'],
                    'item_id' => $data['item_id'],
                    'item_type' => $data['item_type'],
                ],
                [
                    '%s',
                    '%s',
                    '%s',
                ],
                [
                    '%d',
                    '%d',
                    '%s',
                ]
            );
        } else {
            // Insert new specific discount
            $wpdb->insert(
                $tableName,
                [
                    'discount_rule_id' => $data['discount_rule_id'],
                    'item_id' => $data['item_id'],
                    'item_type' => $data['item_type'],
                    'discount' => $data['discount'],
                    'value' => $data['value'],
                    'condition_relationship' => $data['condition_relationship'],
                ],
                [
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                ]
            );
        }
    }

    /**
     * Handle AJAX search requests.
     */
    public static function searchProductsOrCategories()
    {
        global $wpdb;

        $search = sanitize_text_field($_POST['search']);
        $filter = sanitize_text_field($_POST['filter']);
        $exclude_ids = isset($_POST['exclude_ids']) ? array_map('intval', $_POST['exclude_ids']) : [];

        $exclude_clause = '';
        if (!empty($exclude_ids)) {
            if ($filter === 'specific_products') {
                $exclude_clause = 'AND ID NOT IN (' . implode(',', $exclude_ids) . ')';
            } elseif ($filter === 'specific_categories') {
                $exclude_clause = 'AND t.term_id NOT IN (' . implode(',', $exclude_ids) . ')';
            }
        }

        if ($filter === 'specific_products') {
            $query = $wpdb->prepare("
                SELECT ID, post_title as name
                FROM {$wpdb->posts}
                WHERE post_title LIKE %s
                AND post_type = 'product'
                AND post_status = 'active'
                $exclude_clause
                LIMIT 10
            ", '%' . $wpdb->esc_like($search) . '%');
            $results = $wpdb->get_results($query, ARRAY_A);
        } elseif ($filter === 'specific_categories') {
            $query = $wpdb->prepare("
                SELECT t.term_id as ID, t.name
                FROM {$wpdb->terms} t
                INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
                WHERE tt.taxonomy = 'product_cat'
                AND t.name LIKE %s
                $exclude_clause
                LIMIT 10
            ", '%' . $wpdb->esc_like($search) . '%');
            $results = $wpdb->get_results($query, ARRAY_A);
        } else {
            $results = [];
        }

        if ($results) {
            wp_send_json_success($results);
        } else {
            wp_send_json_error(__('No results found', 'ewd-smart-discount-manager'));
        }

        wp_die();
    }

    /**
     * Handle specific item removal by ID.
     */
    public static function removeSpecificItemById($item_id)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'mx_discount_rules_specific';
        $wpdb->delete($tableName, ['item_id' => $item_id], ['%d']);
        self::removeConditionsBySpecificId($item_id);
    }

    /**
     * Remove conditions by specific discount ID.
     */
    public static function removeConditionsBySpecificId($specific_discount_id)
    {
        global $wpdb;
        $tableName = $wpdb->prefix . 'mx_discount_rule_conditions';
        $wpdb->delete($tableName, ['context' => 'specific_discount', 'context_id' => $specific_discount_id], ['%s', '%d']);
    }

    /**
     * Restore DiscountRule.
     */
    public function restoreDiscountRule($id)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;

        $wpdb->update(
            $tableName,
            ['status' => 'active'],
            ['id' => $id],
            ['%s'],
            ['%d']
        );
    }

    /**
     * Move DiscountRule to trash.
     */
    public function moveDiscountRuleToTrash($id)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;

        $wpdb->update(
            $tableName,
            ['status' => 'trash'],
            ['id' => $id],
            ['%s'],
            ['%d']
        );
    }

    /**
     * Delete DiscountRule permanently.
     */
    public function deletePermanently($id)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;

        $wpdb->delete(
            $tableName,
            ['id' => $id],
            ['%d']
        );
    }
}
?>
