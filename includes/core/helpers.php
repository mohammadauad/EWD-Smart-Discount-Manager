<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

if (!function_exists('mxesdmRequireClassFileAdmin')) {
    /**
     * Require class for admin panel.
     * 
     * @param string $file   File name in \wp-content\plugins\ewd-smart-discount-manager\includes/admin/classes/ folder.
     *
     * @return void
     */
    function mxesdmRequireClassFileAdmin( $file ) {

        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/admin/classes/' . $file;
    }
}


if (!function_exists('mxesdmUseModel')) {
    /**
     * Require a Model.
     * 
     * @param string $model   File name in \wp-content\plugins\ewd-smart-discount-manager/includes/admin/models/ folder without ".php".
     *
     * @return void
     */
    function mxesdmUseModel( $model ) {

        require_once MXESDM_PLUGIN_ABS_PATH . 'includes/admin/models/' . $model . '.php';
    }
}

/*
* Debugging
*/
if (!function_exists('mxesdmDebugToFile')) {
    /**
     * Debug anything. The result will be collected 
     * in \wp-content\plugins\ewd-smart-discount-manager/mx-debug/mx-debug.txt file in the root of
     * the plugin.
     * 
     * @param string $content   List of parameters (coma separated).
     *
     * @return void
     */
    function mxesdmDebugToFile( ...$content ) {

        $content = mxesdmContentToString( ...$content );

        $dir = MXESDM_PLUGIN_ABS_PATH . 'mx-debug';

        $file = $dir . '/mx-debug.txt';

        if (!file_exists($dir)) {

            mkdir($dir, 0777, true);

            $current = '>>>' . date('Y/m/d H:i:s', time()) . ':' . "\n";

            $current .= $content . "\n";

            $current .= '_____________________________________' . "\n";

            file_put_contents($file, $current);
        } else {

            $current = '>>>' . date('Y/m/d H:i:s', time()) . ':' . "\n";

            $current .= $content . "\n";
            
            $current .= '_____________________________________' . "\n";          

            $current .= file_get_contents($file) . "\n";

            file_put_contents($file, $current);
        }
    }
}

if (!function_exists('mxesdmContentToString')) {
    /**
     * This function is helpers for the
     * mxesdmDebugToFile function.
     * 
     * @param string $content   List of parameters (coma separated).
     *
     * @return string
     */
    function mxesdmContentToString( ...$content ) {

        ob_start();

        var_dump( ...$content );

        return ob_get_clean();
    }
}

if (!function_exists('mxesdmInsertNewColumnToPosition')) {
    /**
     * Manage posts columns. Add column to a position.
     * 
     * @param array $columns     Existing columns returned by 
     * "manage_{$post_type}_posts_columns" filter.
     * 
     * @param int $position      Position of new columns.
     * @param array $newColumn   List of new columns.
     * Eg. [
     *  'book_id'     => 'Book ID',
     *  'book_author' => 'Book Author'
     * ]
     *
     * @return string
     */
    function mxesdmInsertNewColumnToPosition( array $columns, int $position, array $newColumn ) {

        $chunkedArray = array_chunk( $columns, $position, true );

        $result = array_merge( $chunkedArray[0], $newColumn, $chunkedArray[1] );

        return $result;
    }
}

if (!function_exists('mxesdmAdminRedirect')) {
    /**
     * Redirect from admin panel.
     * 
     * @param string $url   An url where you want to redirect to.
     *
     * @return void
     */
    function mxesdmAdminRedirect( $url ) {

        if (!$url) return;

        add_action( 'admin_footer', function() use ( $url ) {
            printf("<script>window.location.href = '%s';</script>", esc_url_raw($url));
        } );
    }
}

function mxesdmLoad_discount_rules() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mx_discount_rules';
    $rules = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active'");

    foreach ($rules as $rule) {
        $rule->specifics = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mx_discount_rules_specific WHERE discount_rule_id = %d", $rule->id
        ));

        foreach ($rule->specifics as $specific) {
            $specific->conditions = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}mx_discount_rule_conditions WHERE context = 'specific_discount' AND context_id = %d", $specific->item_id
            ));
        }
        
        $rule->conditions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}mx_discount_rule_conditions WHERE context = 'discount_rule' AND context_id = %d", $rule->id
        ));
    }
    return $rules;
}



add_action('woocommerce_cart_calculate_fees', 'mxesdmApply_cart_discounts', 20, 1);

function mxesdmApply_cart_discounts($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;


    // Charger les règles de réduction
    $rules = mxesdmLoad_discount_rules();

    // Tableau pour stocker le nombre total d'articles par catégorie
    $total_items_by_category = [];
    $total_weight_by_category = [];

    // Calculer le nombre total d'articles par catégorie
    foreach ($rules as $rule) {
        if ($rule->type == 'cart_adjustment') {
            if ($rule->filter == 'specific_categories') {
                foreach ($rule->specifics as $specific) {
                    $category_count = 0;
                    $product_weight = 0;
                    foreach ($cart->get_cart() as $item) {
                        $item_product = $item['data'];
                        $item_product_id = $item_product->get_id();

                        $item_category_ids = wp_get_post_terms($item_product_id, 'product_cat', array('fields' => 'ids'));
                        
                        if (in_array($specific->item_id, $item_category_ids)) {
                            $category_count += $item['quantity'];
                            $product_weight += $item_product->get_weight() * $item['quantity'];
                        }
                    }
                    $total_weight_by_category[$specific->item_id] = $product_weight;
                    $total_items_by_category[$specific->item_id] = $category_count;
                }
                
            }else if($rule->filter == 'all_categories'){
                $total_items = 0;
                $total_weight = 0;

                foreach ($cart->get_cart() as $item) {
                    $item_product = $item['data'];
                    $item_product_id = $item_product->get_id();

                    $total_items += $item['quantity'];
                    $total_weight += $item_product->get_weight() * $item['quantity'];
                }

            }

        }
    }


    // Appliquer les réductions basées sur les règles chargées
    foreach ($rules as $rule) {
        $discount = 0;

        if ($rule->type == 'cart_adjustment') {
            if ($rule->filter == 'specific_categories') {
                foreach ($rule->specifics as $specific) {
                    if (!empty($total_items_by_category[$specific->item_id]) && !empty($total_weight_by_category[$specific->item_id])) {
                        $category_count = $total_items_by_category[$specific->item_id];
                        $total_weight = (float)$total_weight_by_category[$specific->item_id];

                        
                        $conditions  = $rule->conditions;
                        if(!empty($specific->conditions)){
                            $conditions  = $specific->conditions;
                        }

                        $apply_discount = true;
                        if(!empty($conditions)){
                            foreach ($conditions as $condition) {
                                if ($condition->calculate_from == 'cart_partial') {
                                    if ($condition->condition_type == 'weight') {

                                        $condition_value = (float)$condition->value;
                                        switch ($condition->operator) {
                                            case '>':
                                                if (!($total_weight > $condition_value)) {
                                                    $apply_discount = false;
                                                    break 2; // Sortir de la boucle interne et externe
                                                }
                                                break;
                                            case '>=':
                                                if (!($total_weight >= $condition_value)) {
                                                    $apply_discount = false;
                                                    break 2; // Sortir de la boucle interne et externe
                                                }
                                                break;
                                            case '<':
                                                if (!($total_weight < $condition_value)) {
                                                    $apply_discount = false;
                                                    break 2; // Sortir de la boucle interne et externe
                                                }
                                                break;
                                            case '<=':
                                                if (!($total_weight <= $condition_value)) {
                                                    $apply_discount = false;
                                                    break 2; // Sortir de la boucle interne et externe
                                                }
                                                break;
                                            case '==':
                                                if (!($total_weight == $condition_value)) {
                                                    $apply_discount = false;
                                                    break 2; // Sortir de la boucle interne et externe
                                                }
                                                break;
                                            default:
                                                // Gérer les autres opérateurs selon vos besoins
                                                break;
                                        }
                                    }
                                }
                            }
                        }


                        if ($apply_discount) {
                            // Vérifie le type de réduction
                            if ($specific->discount == 'fixed') {
                                // Si la réduction est fixe, utilise simplement la valeur spécifiée
                                $discount = ($specific->value * $category_count) + $discount;
                            }
                            // echo '<pre>';
                            // var_dump($discount);
                            // print_r($total_items_by_category);
                            // Appliquer la réduction comme frais sur le panier
                        }
                    }
                }
            
            }else if($rule->filter == 'all_categories'){
               
                if (!empty($total_items) && !empty($total_weight)) {

                    $apply_discount = true;
                    foreach ($rule->conditions as $condition) {
                        if ($condition->calculate_from == 'cart_partial') {
                            if ($condition->condition_type == 'weight') {

                                $condition_value = (float)$condition->value;
                       
                                switch ($condition->operator) {
                                    case '>':
                                        if (!($total_weight > $condition_value)) {
                                            $apply_discount = false;
                                            break 2; // Sortir de la boucle interne et externe
                                        }
                                        break;
                                    case '>=':
                                        if (!($total_weight >= $condition_value)) {
                                            $apply_discount = false;
                                            break 2; // Sortir de la boucle interne et externe
                                        }
                                        break;
                                    case '<':
                                        if (!($total_weight < $condition_value)) {
                                            $apply_discount = false;
                                            break 2; // Sortir de la boucle interne et externe
                                        }
                                        break;
                                    case '<=':
                                        if (!($total_weight <= $condition_value)) {
                                            $apply_discount = false;
                                            break 2; // Sortir de la boucle interne et externe
                                        }
                                        break;
                                    case '==':
                                        if (!($total_weight == $condition_value)) {
                                            $apply_discount = false;
                                            break 2; // Sortir de la boucle interne et externe
                                        }
                                        break;
                                    default:
                                        // Gérer les autres opérateurs selon vos besoins
                                        break;
                                }
                            }
                        }
              
                    }

    
           
                    if ($apply_discount) {
                        // Vérifie le type de réduction
                        if ($rule->discount == 'fixed') {
                            // Si la réduction est fixe, utilise simplement la valeur spécifiée
                            $discount = ($rule->value * $total_items);
                        }
                    }
                }                   
            }
        }
        if($discount > 0)
            $cart->add_fee($rule->label, -$discount);
    }

    // die;
}
