<?php

/**
 * Uninstall.
 * 
 * This file runs when somebody uninstalls
 * the plugin. Here you can remove options
 * or posts if you want.
 * 
 */
if (!defined('WP_UNINSTALL_PLUGIN')) die();

global $wpdb;

// Table names.
$table_names = [
    $wpdb->prefix . 'mx_discount_rules',
    $wpdb->prefix . 'mx_discount_rules_specific',
    $wpdb->prefix . 'mx_discount_rule_conditions'
];

// Drop table(s).
foreach ($table_names as $table_name) {
    $sql = 'DROP TABLE IF EXISTS ' . $table_name . ';';
    $wpdb->query($sql);
}

// Delete CPTs.
$posts = get_posts(['post_type' => 'mxesdm_book', 'numberposts' => -1]);

foreach ($posts as $post) {
    wp_delete_post($post->ID, true);
}

// Uncomment the line below if you have options to delete.
// delete_option('some_option');
