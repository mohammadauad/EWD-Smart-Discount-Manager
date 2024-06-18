(function ($) {
    const { __ } = wp.i18n;

    $(function () {

        // Bulk actions
        $('#mxesdm_custom_talbe_form').on('submit', function (e) {
            e.preventDefault();

            var nonce = $(this).find('#_wpnonce').val();
            var bulk_action = $(this).find('#bulk-action-selector-top').val();

            if (bulk_action !== '-1') {
                var ids = [];
                $('.mxesdm_bulk_input').each(function (index, element) {
                    if ($(element).is(':checked')) {
                        ids.push($(element).val());
                    }
                });

                if (ids.length > 0) {
                    var data = {
                        'action': 'mxesdm_bulk_actions',
                        'nonce': nonce,
                        'bulk_action': bulk_action,
                        'ids': ids
                    };

                    jQuery.post(mxesdm_admin_localize.ajaxurl, data, function (response) {
                        location.reload();
                    });
                }
            }
        });

        // Create table item
        $('#mxesdm_form_create_table_item').on('submit', function (e) {
            e.preventDefault();

            var nonce = $(this).find('#mxesdm_wpnonce').val();
            var title = $('#mxesdm_title').val();
            var label = $('#mxesdm_label').val();
            var type = $('#mxesdm_type').val();
            var filter = $('#mxesdm_filter').val();
            var discount = $('#mxesdm_discount').val();
            var value = $('#mxesdm_value').val();
            var condition_relationship = $('input[name="condition_relationship"]:checked').val();

            var data = {
                'action': 'mxesdm_create_discount_rule',
                'nonce': nonce,
                'title': title,
                'label': label,
                'type': type,
                'filter': filter,
                'discount': discount,
                'value': value,
                'condition_relationship': condition_relationship,
            };

            if (filter.includes("specific")) {
                if (title == '' || label == '' || type == '') {
                    alert(__('Fill in all fields', 'ewd-smart-discount-manager'));
                    return;
                }
            }else{
                if (title == '' || label == '' || type == '' || filter == '' || discount == '' || value == '') {
                    alert(__('Fill in all fields', 'ewd-smart-discount-manager'));
                    return;
                }
            }

            // Collect specific discounts data
            var specificDiscounts = {};
            var specificValues = {};
            var specificConditions = {};
            var specificConditionLogic = {};

            $('.specific-item').each(function () {
                var itemId = $(this).data('id');
                specificDiscounts[itemId] = $(this).find('.specific-discount').val();
                specificValues[itemId] = $(this).find('.specific-value').val();
                specificConditionLogic[itemId] = $(this).find('input[name="logic_operator[' + itemId + ']"]:checked').val();

                specificConditions[itemId] = [];
                $(this).find('.condition-item').each(function () {
                    var condition = {
                        type: $(this).find('.condition-type').val(),
                        operator: $(this).find('.condition-operator').val(),
                        value: $(this).find('.condition-value').val(),
                        calculate_from: $(this).find('.condition-calculate-from').val(),
                        condition_type: $(this).find('.condition-type').val(),
                    };
                    specificConditions[itemId].push(condition);
                });
            });

            data['mxesdm_discount_specific'] = specificDiscounts;
            data['mxesdm_value_specific'] = specificValues;
            data['mxesdm_conditions_specific'] = specificConditions;
            data['mxesdm_condition_relationship'] = specificConditionLogic;


            // Collect general conditions data
            var generalConditions = [];
            $('.general-rules-conditions .condition-item').each(function () {
                var condition = {
                    type: $(this).find('.condition-type').val(),
                    operator: $(this).find('.condition-operator').val(),
                    value: $(this).find('.condition-value').val(),
                    calculate_from: $(this).find('.condition-calculate-from').val(),
                    condition_type: $(this).find('.ccondition-type').val(),  
                };
                generalConditions.push(condition);
            });
            data['general_conditions'] = generalConditions;


            jQuery.post(mxesdm_admin_localize.ajaxurl, data, function (response) {
                if (response.success) {
                    alert(__('Discount rule added successfully.', 'ewd-smart-discount-manager'));
                    window.location.href = mxesdm_admin_localize.edit_page + '&edit-item=' + response.data.id;
                } else {
                    alert(__('Failed to create the item. Please try again.', 'ewd-smart-discount-manager'));
                }                
            }).fail(function () {
                alert(__('Failed to create the item. Please try again.', 'ewd-smart-discount-manager'));
            });
        });

        // Edit table item
        $('#mxesdm_form_update').on('submit', function (e) {
            e.preventDefault();

            var nonce = $(this).find('#mxesdm_wpnonce').val();
            var id = $('#mxesdm_id').val();
            var title = $('#mxesdm_title').val();
            var label = $('#mxesdm_label').val();
            var type = $('#mxesdm_type').val();
            var filter = $('#mxesdm_filter').val();
            var discount = $('#mxesdm_discount').val();
            var value = $('#mxesdm_value').val();
            var status = $('#mxesdm_status').is(':checked') ? 'active' : 'inactive';

            var data = {
                'action': 'mxesdm_update_discount_rule',
                'nonce': nonce,
                'id': id,
                'title': title,
                'label': label,
                'type': type,
                'filter': filter,
                'discount': discount,
                'value': value,
                'status': status
            };

            if (filter.includes("specific")) {
                if (title == '' || label == '' || type == '') {
                    alert(__('Fill in all fields', 'ewd-smart-discount-manager'));
                    return;
                }
            } else{
                if (title == '' || label == '' || type == '' || filter == '' || discount == '' || value == '') {
                    alert(__('Fill in all fields', 'ewd-smart-discount-manager'));
                    return;
                }
            }

            // Collect specific discounts data
            var specificDiscounts = {};
            var specificValues = {};
            var specificConditions = {};
            var specificConditionLogic = {};

            $('.specific-item').each(function () {
                var itemId = $(this).data('id');
                specificDiscounts[itemId] = $(this).find('.specific-discount').val();
                specificValues[itemId] = $(this).find('.specific-value').val();
                specificConditionLogic[itemId] = $(this).find('input[name="logic_operator[' + itemId + ']"]:checked').val();

                specificConditions[itemId] = [];
                $(this).find('.condition-item').each(function () {
                    var condition = {
                        type: $(this).find('.condition-type').val(),
                        operator: $(this).find('.condition-operator').val(),
                        value: $(this).find('.condition-value').val(),
                        calculate_from: $(this).find('.condition-calculate-from').val(),
                        condition_type: $(this).find('.condition-type').val(),
                        
                    };
                    specificConditions[itemId].push(condition);
                });
            });

            data['mxesdm_discount_specific'] = specificDiscounts;
            data['mxesdm_value_specific'] = specificValues;
            data['mxesdm_conditions_specific'] = specificConditions;
            data['mxesdm_condition_relationship'] = specificConditionLogic;

            // Collect general conditions data
            var generalConditions = [];
            $('.general-rules-conditions .condition-item').each(function () {
                var condition = {
                    type: $(this).find('.condition-type').val(),
                    operator: $(this).find('.condition-operator').val(),
                    value: $(this).find('.condition-value').val(),
                    calculate_from: $(this).find('.condition-calculate-from').val(),
                    condition_type: $(this).find('.condition-type').val(),
                };
                generalConditions.push(condition);
            });

            data['general_conditions'] = generalConditions;
            data['condition_relationship'] = $('input[name="condition_relationship"]:checked').val();


            jQuery.post(mxesdm_admin_localize.ajaxurl, data, function (response) {
                if (response.success) {
                    alert(__('Discount rule updated successfully.', 'ewd-smart-discount-manager'));
                } else {
                    alert(__('Failed to create the item. Please try again.', 'ewd-smart-discount-manager'));
                }    
            });
        });

        function toggleDiscountBlocks() {
            var filterValue = $('#mxesdm_filter').val();
            if (filterValue === 'all_products' || filterValue === 'all_categories') {
                $('#mxesdm_discount_block_all').show();
                $('#mxesdm_specific_block').hide();
            } else {
                $('#mxesdm_discount_block_all').hide();
                $('#mxesdm_specific_block').show();
            }
        }

        $('#mxesdm_filter').change(function () {
            toggleDiscountBlocks();
        });

        toggleDiscountBlocks(); // Initial call

        // Handle specific search and add
        $('#mxesdm_specific_search').on('input', function () {
            var searchValue = $(this).val();
            var filter = $('#mxesdm_filter').val();
            if (searchValue.length > 2) {
                var excludeIds = [];
                $('.specific-item').each(function () {
                    excludeIds.push($(this).data('id'));
                });

                var data = {
                    'action': 'mxesdm_search_products_or_categories',
                    'search': searchValue,
                    'filter': filter,
                    'exclude_ids': excludeIds
                };

                jQuery.post(mxesdm_admin_localize.ajaxurl, data, function (response) {
                    if ($('#mxesdm_search_results').length === 0) {
                        $('#mxesdm_specific_search').after('<div id="mxesdm_search_results" class="mxesdm_search_results"></div>');
                    }

                    if (response.success) {
                        var resultsHtml = '';
                        $.each(response.data, function (index, item) {
                            resultsHtml += '<div class="mxesdm_search_result_item" data-id="' + item.ID + '" data-name="' + item.name + '">' + item.name + '</div>';
                        });
                        $('#mxesdm_search_results').html(resultsHtml);
                    } else {
                        $('#mxesdm_search_results').html('<p>' + __('No results found', 'ewd-smart-discount-manager') + '</p>');
                    }
                });
            } else {
                $('#mxesdm_search_results').empty();
            }
        });

        $(document).on('click', '.mxesdm_search_result_item', function () {
            var selectedValue = $(this).data('id');
            var selectedText = $(this).data('name');
        
            var specificItemHtml = '<div class="specific-item" data-id="' + selectedValue + '">' +
                '<span>' + selectedText + '</span>' +
                '<div class="mxesdm_discount-block">' +
                    '<div>' +
                        '<label for="mxesdm_discount_' + selectedValue + '">' + __('Discount Type', 'ewd-smart-discount-manager') + '</label>' +
                        '<select name="mxesdm_discount_specific[' + selectedValue + ']" id="mxesdm_discount_' + selectedValue + '" class="specific-discount">' +
                            '<option value="fixed">' + __('Fixed Discount', 'ewd-smart-discount-manager') + '</option>' +
                        '</select>' +
                    '</div>' +
                    '<div>' +
                        '<label for="mxesdm_value_' + selectedValue + '">' + __('Value', 'ewd-smart-discount-manager') + '</label>' +
                        '<input type="text" name="mxesdm_value_specific[' + selectedValue + ']" id="mxesdm_value_' + selectedValue + '" class="specific-value"/>' +
                    '</div>' +
                    '<button type="button" class="dashicons dashicons-no-alt mxesdm_remove_specific" title="' + __('Remove', 'ewd-smart-discount-manager') + '"></button>' +
                '</div>' +
                '<div class="rules-conditions">' +
                    '<h4>' + __('Rules Conditions', 'ewd-smart-discount-manager') + '</h4>' +
                    '<div class="logic-operator">' +
                        '<label>' + __('Logic Operator', 'ewd-smart-discount-manager') + '</label>' +
                        '<div>' +
                            '<div>' +
                                '<input type="radio" class="discount-item-radio-inner" name="logic_operator[' + selectedValue + ']" value="AND" checked> AND' +
                            '</div>' +
                            '<div>' +
                                '<input type="radio" class="discount-item-radio-inner" name="logic_operator[' + selectedValue + ']" value="OR"> OR' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="conditions-list">' +
                    '</div>' +
                    '<button type="button" class="add-condition">' + __('Add Condition', 'ewd-smart-discount-manager') + '</button>' +
                '</div>' +
            '</div>';
            $('#mxesdm_specific_list').append(specificItemHtml);
            $('#mxesdm_specific_search').val('');
            $('#mxesdm_search_results').empty();
        });

        // Handle specific item removal
        $(document).on('click', '.mxesdm_remove_specific', function () {
            var specificItem = $(this).closest('.specific-item');
            specificItem.remove();
        });

        // Add general condition
        $(document).on('click', '.add-general-condition', function () {
            var conditionHtml = '<div class="condition-item">' +
                '<div class="mxesdm_condition-type">' +
                    '<label>' + __('Condition Type', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-type" name="rules_conditions_general[][condition_type]">' +
                        '<option value="weight">' + __('Weight', 'ewd-smart-discount-manager') + '('+mxesdm_admin_localize.weight_unit+')</option>' +
                        '<!-- Add other condition types as needed -->' +
                    '</select>' +
                '</div>' +
                '<div class="mxesdm_condition-operator">' +
                    '<label>' + __('Operator', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-operator" name="rules_conditions_general[][operator]">' +
                        '<option value=">">' + __('>', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value="<">' + __('<', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value=">=">' + __('>=', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value="<=">' + __('<=', 'ewd-smart-discount-manager') + '</option>' +
                        '<!-- Add other operators as needed -->' +
                    '</select>' +
                '</div>' +
                '<div class="mxesdm_condition-value">' +
                    '<label>' + __('Value', 'ewd-smart-discount-manager') + '</label>' +
                    '<input type="text" class="condition-value" name="rules_conditions_general[][value]" value="" />' +
                '</div>' +
                '<div class="mxesdm_condition-calculate-from">' +
                    '<label>' + __('How to calculate', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-calculate-from" name="rules_conditions_general[][calculate_from]">' +
                        '<option value="cart_partial">' + __('Count only the items selected in the filters set for this rule', 'ewd-smart-discount-manager') + '</option>' +
                        '<!-- Add other calculation methods as needed -->' +
                    '</select>' +
                '</div>' +
                '<div>' +
                    '<button type="button" class="dashicons dashicons-no-alt remove-condition" title="' + __('Remove', 'ewd-smart-discount-manager') + '"></button>' +
                '</div>' +
            '</div>';
            $(this).siblings('.conditions-list').append(conditionHtml);
        });

        // Add specific condition
        $(document).on('click', '.add-condition', function () {
            var conditionHtml = '<div class="condition-item">' +
                '<div class="mxesdm_condition-type">' +
                    '<label>' + __('Condition Type', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-type" name="rules_conditions_specific[][condition_type]">' +
                        '<option value="weight">' + __('Weight', 'ewd-smart-discount-manager') + '('+mxesdm_admin_localize.weight_unit+')</option>' +
                        '<!-- Add other condition types as needed -->' +
                    '</select>' +
                '</div>' +
                '<div class="mxesdm_condition-operator">' +
                    '<label>' + __('Operator', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-operator" name="rules_conditions_specific[][operator]">' +
                        '<option value=">">' + __('>', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value="<">' + __('<', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value=">=">' + __('>=', 'ewd-smart-discount-manager') + '</option>' +
                        '<option value="<=">' + __('<=', 'ewd-smart-discount-manager') + '</option>' +
                        '<!-- Add other operators as needed -->' +
                    '</select>' +
                '</div>' +
                '<div class="mxesdm_condition-value">' +
                    '<label>' + __('Value', 'ewd-smart-discount-manager') + '</label>' +
                    '<input type="text" class="condition-value" name="rules_conditions_specific[][value]" value="" />' +
                '</div>' +
                '<div class="mxesdm_condition-calculate-from">' +
                    '<label>' + __('How to calculate', 'ewd-smart-discount-manager') + '</label>' +
                    '<select class="condition-calculate-from" name="rules_conditions_specific[][calculate_from]">' +
                        '<option value="cart_partial">' + __('Count only the items selected in the filters set for this rule', 'ewd-smart-discount-manager') + '</option>' +
                        '<!-- Add other calculation methods as needed -->' +
                    '</select>' +
                '</div>' +
                '<div>' +
                    '<button type="button" class="dashicons dashicons-no-alt remove-condition" title="' + __('Remove', 'ewd-smart-discount-manager') + '"></button>' +
                '</div>' +
            '</div>';
            $(this).siblings('.conditions-list').append(conditionHtml);
        });

        // Remove condition
        $(document).on('click', '.remove-condition', function () {
            $(this).closest('.condition-item').remove();
        });

    });
})(jQuery);
