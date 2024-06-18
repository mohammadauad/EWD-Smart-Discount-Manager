<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

?>

<div class="mx-discount-item-table-item-wrap">
    
    <a class="mx-go-back" href="<?php echo admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG); ?>"><?php echo __( 'Go back', 'ewd-smart-discount-manager' ); ?></a>

    <div class="mxesdmmx_block_wrap">

        <form id="mxesdm_form_create_table_item" class="mx-settings" method="post" action="">
            
            <div class="mx-form-1">
                <div class="formbold-mb-3">
                    <label for="mxesdm_title" class="formbold-form-label"><?php echo __( 'Title', 'ewd-smart-discount-manager' ); ?></label>
                    <input type="text" name="mxesdm_title" id="mxesdm_title" class="formbold-form-input" value="" />
                </div>

                <div class="formbold-mb-3">
                    <label for="mxesdm_label" class="formbold-form-label"><?php echo __( 'Label', 'ewd-smart-discount-manager' ); ?></label>
                    <input type="text" name="mxesdm_label" id="mxesdm_label" class="formbold-form-input" value="" />
                </div>

                <div class="formbold-mb-3">
                    <label for="mxesdm_type" class="formbold-form-label"><?php echo __( 'Type', 'ewd-smart-discount-manager' ); ?></label>
                    <select name="mxesdm_type" id="mxesdm_type" class="formbold-form-input">
                        <option value="cart_adjustment"><?php echo __( 'Cart Adjustment', 'ewd-smart-discount-manager' ); ?></option>
                    </select>
                </div>

                <div class="formbold-mb-3">
                    <label for="mxesdm_filter" class="formbold-form-label"><?php echo __( 'Filter', 'ewd-smart-discount-manager' ); ?></label>
                    <select name="mxesdm_filter" id="mxesdm_filter" class="formbold-form-input">
                        <option value="all_categories"><?php echo __( 'All Categories', 'ewd-smart-discount-manager' ); ?></option>
                        <option value="specific_categories"><?php echo __( 'Specific Categories', 'ewd-smart-discount-manager' ); ?></option>
                    </select>
                </div>

                <div id="mxesdm_discount_block_all" class="mxesdm_discount-block">
                    <div class="formbold-mb-3">
                        <label for="mxesdm_discount" class="formbold-form-label"><?php echo __( 'Discount Type', 'ewd-smart-discount-manager' ); ?></label>
                        <select name="mxesdm_discount" id="mxesdm_discount" class="formbold-form-input">
                            <option value="fixed"><?php echo __( 'Fixed Discount', 'ewd-smart-discount-manager' ); ?></option>
                        </select>
                    </div>

                    <div class="formbold-mb-3">
                        <label for="mxesdm_value" class="formbold-form-label"><?php echo __( 'Value', 'ewd-smart-discount-manager' ); ?></label>
                        <input type="text" name="mxesdm_value" id="mxesdm_value" class="formbold-form-input" value="" />
                    </div>
                </div>

                <div id="mxesdm_specific_block" class="mxesdm_specific_block">
                    <h3><?php echo __( 'Specific Discounts', 'ewd-smart-discount-manager' ); ?></h3>
                    <div class="formbold-mb-3">
                        <label for="mxesdm_specific_search" class="formbold-form-label"><?php echo __( 'Search', 'ewd-smart-discount-manager' ); ?></label>
                        <input type="text" id="mxesdm_specific_search" class="formbold-form-input" />
                    </div>
                    <div id="mxesdm_specific_list"></div>
                </div>


                <div class="general-rules-conditions">
                    <h3><?php echo __('General Rules Conditions', 'ewd-smart-discount-manager'); ?></h3>
                    <div class="logic-operator">
                        <label><?php echo __('Logic Operator', 'ewd-smart-discount-manager'); ?></label>
                        <div>
                            <div>
                                <input type="radio" name="condition_relationship" class="discount-item-radio-inner" value="AND" checked> <?php echo __( 'AND', 'ewd-smart-discount-manager' ); ?>
                            </div>
                            <div>
                                <input type="radio" name="condition_relationship" class="discount-item-radio-inner" value="OR"> <?php echo __( 'OR', 'ewd-smart-discount-manager' ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="conditions-list">
                        <!-- General conditions will be loaded here -->
                    </div>
                    <button type="button" class="formbold-btn add-general-condition"><?php echo __('Add Condition', 'ewd-smart-discount-manager'); ?></button>
                </div>




            </div>

            <div class="mx-form-2">
                <div class="mx-submit_button_wrap">
                    <input type="hidden" id="mxesdm_wpnonce" name="mxesdm_wpnonce" value="<?php echo wp_create_nonce('mxesdm_nonce_request'); ?>" />
                    <input class="button-primary formbold-btn" type="submit" name="mxesdm_submit" value="<?php echo __( 'Create', 'ewd-smart-discount-manager' ); ?>" />
                </div>
            </div>

        </form>

    </div>

</div>
