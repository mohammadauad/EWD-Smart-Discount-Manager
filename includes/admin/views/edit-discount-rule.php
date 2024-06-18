<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;
?>

<div class="mx-discount-item-table-item-wrap">

    <a class="mx-go-back" href="<?php echo admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG); ?>"><?php echo __('Go Back', 'ewd-smart-discount-manager'); ?></a>

    <div class="mxesdmmx_block_wrap">

        <form id="mxesdm_form_update" class="mx-settings" method="post" action="">
            
            <div class="mx-form-1">
                <input type="hidden" id="mxesdm_id" name="mxesdm_id" value="<?php echo esc_attr($data->id); ?>" />
                <div class="mxesdm_status-block">                
                    <input type="checkbox" name="mxesdm_status" id="mxesdm_status" class="discount-item-checkbox-inner" value="active" <?php checked($data->status, 'active'); ?> />
                    <label for="mxesdm_status"><?php echo __('Active', 'ewd-smart-discount-manager'); ?></label>
                </div>
                
                <div>
                    <label for="mxesdm_title"><?php echo __('Title', 'ewd-smart-discount-manager'); ?></label>
                    
                    <input type="text" name="mxesdm_title" id="mxesdm_title" value="<?php echo esc_attr($data->title); ?>" />
                </div>
                        
                <div class="mxesdm_type-block">
                    <label for="mxesdm_type"><?php echo __('Type', 'ewd-smart-discount-manager'); ?></label>
                    
                    <select name="mxesdm_type" id="mxesdm_type">
                        <option value="cart_adjustment" <?php selected($data->type, 'cart_adjustment'); ?>><?php echo __('Cart Adjustment', 'ewd-smart-discount-manager'); ?></option>
                    </select>
                </div>
                
                <div>
                    <label for="mxesdm_label"><?php echo __('Label', 'ewd-smart-discount-manager'); ?></label>
                    
                    <input type="text" name="mxesdm_label" id="mxesdm_label" value="<?php echo esc_attr($data->label); ?>" />
                </div>
                
                <div class="mxesdm_filter-block">
                    <label for="mxesdm_filter"><?php echo __('Filter', 'ewd-smart-discount-manager'); ?></label>
                    
                    <select name="mxesdm_filter" id="mxesdm_filter">
                        <option value="all_categories" <?php selected($data->filter, 'all_categories'); ?>><?php echo __('All Categories', 'ewd-smart-discount-manager'); ?></option>
                        <option value="specific_categories" <?php selected($data->filter, 'specific_categories'); ?>><?php echo __('Specific Categories', 'ewd-smart-discount-manager'); ?></option>
                    </select>
                </div>
                
                <div id="mxesdm_discount_block_all" class="mxesdm_discount-block">
                    <div>
                        <label for="mxesdm_discount"><?php echo __('Discount Type', 'ewd-smart-discount-manager'); ?></label>
                        
                        <select name="mxesdm_discount" id="mxesdm_discount">
                            <option value="fixed" <?php selected($data->discount, 'fixed'); ?>><?php echo __('Fixed Discount', 'ewd-smart-discount-manager'); ?></option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="mxesdm_value"><?php echo __('Value', 'ewd-smart-discount-manager'); ?></label>
                        
                        <input type="text" name="mxesdm_value" id="mxesdm_value" value="<?php echo esc_attr($data->value); ?>" />
                    </div>
                </div>

                <div id="mxesdm_specific_block" class="mxesdm_specific_block">
                    <h3><?php echo __('Specific Discounts', 'ewd-smart-discount-manager'); ?></h3>
                    <div>
                        <label for="mxesdm_specific_search"><?php echo __('Search', 'ewd-smart-discount-manager'); ?></label>
                        <input type="text" id="mxesdm_specific_search" />
                    </div>
                    <div id="mxesdm_specific_list">
                        <?php if (!empty($data->specific_discounts)) : ?>
                            <?php foreach ($data->specific_discounts as $specific) : ?>
                                <div class="specific-item" data-id="<?php echo esc_attr($specific['item_id']); ?>">
                                    <span><?php echo esc_html($specific['name']); ?></span>
                                    <div class="mxesdm_discount-block">
                                        <div>   
                                            <label for="mxesdm_discount_<?php echo esc_attr($specific['item_id']); ?>"><?php echo __('Discount Type', 'ewd-smart-discount-manager'); ?></label>
                                            <select name="mxesdm_discount_specific[<?php echo esc_attr($specific['item_id']); ?>]" id="mxesdm_discount_<?php echo esc_attr($specific['item_id']); ?>" class="specific-discount">
                                                <option value="fixed" <?php selected($specific['discount'], 'fixed'); ?>><?php echo __('Fixed Discount', 'ewd-smart-discount-manager'); ?></option>
                                            </select>
                                        </div>
                                        <div>
                                            <label for="mxesdm_value_<?php echo esc_attr($specific['item_id']); ?>"><?php echo __('Value', 'ewd-smart-discount-manager'); ?></label>
                                            <input type="text" name="mxesdm_value_specific[<?php echo esc_attr($specific['item_id']); ?>]" id="mxesdm_value_<?php echo esc_attr($specific['item_id']); ?>" class="specific-value" value="<?php echo esc_attr($specific['value']); ?>" />
                                        </div>
                                        <button type="button" class="dashicons dashicons-no-alt mxesdm_remove_specific"  title="<?php echo __('Remove', 'ewd-smart-discount-manager'); ?>"></button>

                                    </div>
                                    <div class="rules-conditions">
                                        <h4><?php echo __('Rules Conditions', 'ewd-smart-discount-manager'); ?></h4>
                                        <div class="logic-operator">
                                            <label><?php echo __('Logic Operator', 'ewd-smart-discount-manager'); ?></label>
                                            <div>
                                                <div>
                                                    <input type="radio" class="discount-item-radio-inner" name="logic_operator[<?php echo esc_attr($specific['item_id']); ?>]" value="AND" <?php checked(isset($specific['condition_relationship']) ? $specific['condition_relationship'] : '', 'AND'); ?>> <?php echo __( 'AND', 'ewd-smart-discount-manager' ); ?>
                                                </div>
                                                <div>
                                                    <input type="radio" class="discount-item-radio-inner" name="logic_operator[<?php echo esc_attr($specific['item_id']); ?>]" value="OR" <?php checked(isset($specific['condition_relationship']) ? $specific['condition_relationship'] : '', 'OR'); ?>> <?php echo __( 'OR', 'ewd-smart-discount-manager' ); ?>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="conditions-list">
                                            <?php if (!empty($specific['conditions'])) : ?>
                                                <?php foreach ($specific['conditions'] as $condition) : ?>
                                                    <div class="condition-item" data-id="<?php echo esc_attr($condition['id']); ?>">
                                                        <div class="mxesdm_condition-type">
                                                            <label><?php echo __('Condition Type', 'ewd-smart-discount-manager'); ?></label>
                                                            <select class="condition-type" name="rules_conditions[<?php echo esc_attr($specific['item_id']); ?>][<?php echo esc_attr($condition['id']); ?>][condition_type]">
                                                                <option value="weight" <?php selected($condition['condition_type'], 'weight'); ?>><?php echo __('Weight', 'ewd-smart-discount-manager'); ?> (<?= get_option('woocommerce_weight_unit'); ?>)</option>
                                                                <!-- Add other condition types as needed -->
                                                            </select>
                                                        </div>

                                                        <div class="mxesdm_condition-operator">
                                                            <label><?php echo __('Operator', 'ewd-smart-discount-manager'); ?></label>
                                                            <select class="condition-operator" name="rules_conditions[<?php echo esc_attr($specific['item_id']); ?>][<?php echo esc_attr($condition['id']); ?>][operator]">
                                                                <option value=">" <?php selected($condition['operator'], '>'); ?>><?php echo __('>', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value="<" <?php selected($condition['operator'], '<'); ?>><?php echo __('<', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value=">=" <?php selected($condition['operator'], '>='); ?>><?php echo __('>=', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value="<=" <?php selected($condition['operator'], '<='); ?>><?php echo __('<=', 'ewd-smart-discount-manager'); ?></option>
                                                                <!-- Add other operators as needed -->
                                                            </select>
                                                        </div>

                                                        <div class="mxesdm_condition-value">
                                                            <label><?php echo __('Value', 'ewd-smart-discount-manager'); ?></label>
                                                            <input type="text" class="condition-value" name="rules_conditions[<?php echo esc_attr($specific['item_id']); ?>][<?php echo esc_attr($condition['id']); ?>][value]" value="<?php echo esc_attr($condition['value']); ?>" />
                                                        </div>

                                                        <div class="mxesdm_condition-calculate-from">
                                                            <label><?php echo __('How to calculate', 'ewd-smart-discount-manager'); ?></label>
                                                            <select class="condition-calculate-from" name="rules_conditions[<?php echo esc_attr($specific['item_id']); ?>][<?php echo esc_attr($condition['id']); ?>][calculate_from]">
                                                                <option value="cart_partial" <?php selected($condition['calculate_from'], 'cart_partial'); ?>><?php echo __('Count only the items selected in the filters set for this rule', 'ewd-smart-discount-manager'); ?></option>
                                                                <!-- Add other calculation methods as needed -->
                                                            </select>
                                                        </div>
                                                        <button type="button" class="dashicons dashicons-no-alt remove-condition"  title="<?php echo __('Remove', 'ewd-smart-discount-manager'); ?>"></button>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="add-condition"><?php echo __('Add Condition', 'ewd-smart-discount-manager'); ?></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="general-rules-conditions">
                    <h3><?php echo __('General Rules Conditions', 'ewd-smart-discount-manager'); ?></h3>
                    <div class="logic-operator">
                        <label><?php echo __('Logic Operator', 'ewd-smart-discount-manager'); ?></label>
                        <div>
                            <div>
                                <input type="radio" name="condition_relationship" class="discount-item-radio-inner" value="AND" <?php checked(isset($data->condition_relationship) ? $data->condition_relationship : '', 'AND'); ?>> <?php echo __( 'AND', 'ewd-smart-discount-manager' ); ?>
                            </div>
                            <div>
                                <input type="radio" name="condition_relationship" class="discount-item-radio-inner" value="OR" <?php checked(isset($data->condition_relationship) ? $data->condition_relationship : '', 'OR'); ?>> <?php echo __( 'OR', 'ewd-smart-discount-manager' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="conditions-list">
                        <?php if (!empty($data->general_conditions)) : ?>
                            <?php foreach ($data->general_conditions as $condition) : ?>
                                <div class="condition-item" data-id="<?php echo esc_attr($condition['id']); ?>">
                                    <div class="mxesdm_condition-type">
                                        <label><?php echo __('Condition Type', 'ewd-smart-discount-manager'); ?></label>
                                        <select class="condition-type" name="rules_conditions_general[<?php echo esc_attr($condition['id']); ?>][condition_type]">
                                            <option value="weight" <?php selected($condition['condition_type'], 'weight'); ?>><?php echo __('Weight', 'ewd-smart-discount-manager'); ?> (<?= get_option('woocommerce_weight_unit'); ?>)</option>
                                            <!-- Add other condition types as needed -->
                                        </select>
                                    </div>

                                    <div class="mxesdm_condition-operator">
                                        <label><?php echo __('Operator', 'ewd-smart-discount-manager'); ?></label>
                                        <select class="condition-operator" name="rules_conditions_general[<?php echo esc_attr($condition['id']); ?>][operator]">
                                        <option value=">" <?php selected($condition['operator'], '>'); ?>><?php echo __('>', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value="<" <?php selected($condition['operator'], '<'); ?>><?php echo __('<', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value=">=" <?php selected($condition['operator'], '>='); ?>><?php echo __('>=', 'ewd-smart-discount-manager'); ?></option>
                                                                <option value="<=" <?php selected($condition['operator'], '<='); ?>><?php echo __('<=', 'ewd-smart-discount-manager'); ?></option>
                                            <!-- Add other operators as needed -->
                                        </select>
                                    </div>

                                    <div class="mxesdm_condition-value">
                                        <label><?php echo __('Value', 'ewd-smart-discount-manager'); ?></label>
                                        <input type="text" class="condition-value" name="rules_conditions_general[<?php echo esc_attr($condition['id']); ?>][value]" value="<?php echo esc_attr($condition['value']); ?>" />
                                    </div>

                                    <div class="mxesdm_condition-calculate-from">
                                        <label><?php echo __('How to calculate', 'ewd-smart-discount-manager'); ?></label>
                                        <select class="condition-calculate-from" name="rules_conditions_general[<?php echo esc_attr($condition['id']); ?>][calculate_from]">
                                            <option value="cart_partial" <?php selected($condition['calculate_from'], 'cart_partial'); ?>><?php echo __('Count only the items selected in the filters set for this rule', 'ewd-smart-discount-manager'); ?></option>
                                            <!-- Add other calculation methods as needed -->
                                        </select>
                                    </div>

                                    <div>
                                        <button type="button" class="dashicons dashicons-no-alt remove-condition" title="<?php echo __('Remove', 'ewd-smart-discount-manager'); ?>"></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="add-general-condition"><?php echo __('Add Condition', 'ewd-smart-discount-manager'); ?></button>
                </div>
            </div>
            
            <div class="mx-form-2">
                <div class="mx-submit_button_wrap">
                    <input type="hidden" id="mxesdm_wpnonce" name="mxesdm_wpnonce" value="<?php echo wp_create_nonce('mxesdm_nonce_request'); ?>" />
                    <input class="button-primary" type="submit" name="mxesdm_submit" value="<?php echo __('Save', 'ewd-smart-discount-manager'); ?>" />
                </div>
            </div>



        </form>

    </div>

</div>
