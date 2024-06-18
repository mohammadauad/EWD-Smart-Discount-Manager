<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * The MXESDMDiscountRuleTable class.
 *
 * Custom table creation. Data managing.
 */
class MXESDMDiscountRuleTable extends WP_List_Table
{

    public function __construct( $args = [] )
    {

        parent::__construct(
            [
                'singular' => 'mxesdm_singular',
                'plural'   => 'mxesdm_plural',
            ]
        );

    }

    public function prepare_items()
    {
        global $wpdb;
    
        // Pagination.
        $perPage     = 20;
        $currentPage = $this->get_pagenum();
    
        if (1 < $currentPage) {
            $offset = $perPage * ( $currentPage - 1 );
        } else {
            $offset = 0;
        }
    
        // Sortable.
        $order = isset( $_GET['order'] ) ? trim( sanitize_text_field( $_GET['order'] ) ) : 'desc';
        $orderBy = isset( $_GET['orderby'] ) ? trim( sanitize_text_field( $_GET['orderby'] ) ) : 'id';
    
        // Search.
        $search = '';
    
        if (!empty($_REQUEST['s'])) {
            $search = "AND title LIKE '%" . esc_sql( $wpdb->esc_like( sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) ) ) . "%' ";
        }
    
        // Status.
        $itemStatus = isset( $_GET['item_status'] ) ? trim( $_GET['item_status'] ) : '';
    
        if (empty($itemStatus) || 'all' == $itemStatus) {
            // All except 'trash'.
            $status = "AND status != 'trash'";
        } else {
            $status = "AND status = '$itemStatus'";
        }
        
        // Get data.
        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;
    
        $items = $wpdb->get_results(
            "SELECT * FROM {$tableName} WHERE 1 = 1 {$status} {$search}" .
            $wpdb->prepare( "ORDER BY {$orderBy} {$order} LIMIT %d OFFSET %d;", $perPage, $offset ),
            ARRAY_A
        );
    
        $count = $wpdb->get_var( "SELECT COUNT(id) FROM {$tableName} WHERE 1 = 1 {$status} {$search};" );
    
        // Set data.
        $this->items = $items;
    
        // Set column headers.
        $columns  = $this->get_columns();
        $hidden   = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
    
        $this->_column_headers = [
            $columns,
            $hidden,
            $sortable,
        ];
    
        // Set the pagination.
        $this->set_pagination_args(
            [
                'total_items' => $count,
                'per_page'    => $perPage,
                'total_pages' => ceil( $count / $perPage ),
            ]
        );
    
    }
    

    public function get_columns()
    {

        return [
            'cb'          => '<input type="checkbox" />',
            'id'          => __('ID', 'ewd-smart-discount-manager'),
            'title'       => __('Title', 'ewd-smart-discount-manager'),
            'status'      => __('Status', 'ewd-smart-discount-manager'),
            'type'        => __('Type', 'ewd-smart-discount-manager'),
            'filter'      => __('Filter', 'ewd-smart-discount-manager'),
            'created_at'  => __('Created', 'ewd-smart-discount-manager'),
        ];
        
    }    

    public function get_hidden_columns()
    {

        return [
            'id',
            // 'status',
        ];

    }

    public function get_sortable_columns()
    {

        return [
            'title' => [
                'title',
                false
            ]
        ];
        
    }

    public function column_default( $item, $columnName )
    {

        do_action( "manage_mxesdm_items_custom_column", $columnName, $item );

    }

    public function column_cb( $item )
    {
        
        echo sprintf( '<input type="checkbox" class="mxesdm_bulk_input" name="mxesdm-action-%1$s" value="%1$s" />', $item['id'] );
    
    }

    public function column_id( $item )
    {

        echo $item['id'];

    }

    public function column_title( $item )
    {

        $url      = admin_url( 'admin.php?page=' . MXESDM_DISCOUNT_RULE_TABLE_ITEM_MENU );

        $user_id  = get_current_user_id();

        $can_edit = current_user_can( 'edit_user', $user_id );

        $output   = '<strong>';

        if ($can_edit) {

            $output .= '<a href="' . esc_url( $url ) . '&edit-item=' . $item['id'] . '">' . $item['title'] . '</a>';

            $actions['edit']  = '<a href="' . esc_url( $url ) . '&edit-item=' . $item['id'] . '">' . __( 'Edit', 'ewd-smart-discount-manager' ) . '</a>';
            $actions['trash'] = '<a class="submitdelete" aria-label="' . esc_attr__( 'Trash', 'ewd-smart-discount-manager' ) . '" href="' . esc_url(
                wp_nonce_url(
                    add_query_arg(
                        [
                            'trash' => $item['id'],
                        ],
                        $url
                    ),
                    'trash',
                    'mxesdm_nonce'
                )
            ) . '">' . esc_html__( 'Trash', 'ewd-smart-discount-manager' ) . '</a>';

            $itemStatus = isset( $_GET['item_status'] ) ? trim( $_GET['item_status'] ) : 'active';

            if ($itemStatus == 'trash') {

                unset( $actions['edit'] );
                unset( $actions['trash'] );

                $actions['restore'] = '<a aria-label="' . esc_attr__( 'Restore', 'ewd-smart-discount-manager' ) . '" href="' . esc_url(
                    wp_nonce_url(
                        add_query_arg(
                            [
                                'restore' => $item['id'],
                            ],
                            $url
                        ),
                        'restore',
                        'mxesdm_nonce'
                    )
                ) . '">' . esc_html__( 'Restore', 'ewd-smart-discount-manager' ) . '</a>';

                $actions['delete'] = '<a class="submitdelete" aria-label="' . esc_attr__( 'Delete Permanently', 'ewd-smart-discount-manager' ) . '" href="' . esc_url(
                    wp_nonce_url(
                        add_query_arg(
                            [
                                'delete' => $item['id'],
                            ],
                            $url
                        ),
                        'delete',
                        'mxesdm_nonce'
                    )
                ) . '">' . esc_html__( 'Delete Permanently', 'ewd-smart-discount-manager' ) . '</a>';

            }
    
            $rowActions = [];
    
            foreach ($actions as $action => $link) {
                $rowActions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
            }
    
            $output .= '<div class="row-actions">' . implode( ' | ', $rowActions ) . '</div>';
                
        } else {

            $output .= $item['title'];

        }

        $output .= '</strong>';

        echo $output;

    }

    public function column_type( $item )
    {
        if($item['type'] == 'cart_adjustment'){
            echo __('Cart Adjustment', 'ewd-smart-discount-manager');
        }
    }

    public function column_filter( $item )
    {
        if($item['filter'] == 'all_categories'){
            echo __('All Categories', 'ewd-smart-discount-manager');
        }else if($item['filter'] == 'specific_categories'){
            echo __('Specific Categories', 'ewd-smart-discount-manager');
        }
    }

    public function column_status( $item )
    {
        if($item['status'] == 'active'){
            echo __('Active', 'ewd-smart-discount-manager');
        }else if($item['status'] == 'inactive'){
            echo __('Inactive', 'ewd-smart-discount-manager');
        }
    }

    public function column_created_at( $item )
    {

        echo $item['created_at'];

    }

    protected function get_bulk_actions()
    {

        if (!current_user_can('edit_posts')) {
            return [];
        }

        $itemStatus = isset( $_GET['item_status'] ) ? trim( $_GET['item_status'] ) : 'active';

        $action = [
            'trash' => __( 'Move to trash', 'ewd-smart-discount-manager' ),
        ];

        if ($itemStatus == 'trash') {

            unset( $action['trash'] );

            $action['restore'] = __( 'Restore Item', 'ewd-smart-discount-manager' );
            $action['delete']  = __( 'Delete Permanently', 'ewd-smart-discount-manager' );

        }

        return $action;

    }

    public function search_box( $text, $inputId )
    {

        if (empty($_REQUEST['s']) && ! $this->has_items()) {
            return;
        }

        ?>
            <p class="search-box">
                <label class="screen-reader-text" for="<?php echo esc_attr( $inputId ); ?>"><?php echo $text; ?>:</label>
                <input type="search" id="<?php echo esc_attr( $inputId ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
                    <?php submit_button( $text, '', '', false, ['id' => 'mxesdm-search-submit'] ); ?>
            </p>
        <?php

    }


    public function get_views()
    {
        global $wpdb;
    
        $tableName     = $wpdb->prefix . MXESDM_TABLE_SLUG;
        $itemStatus    = isset($_GET['item_status']) ? trim($_GET['item_status']) : '';
        $activeNumber = $wpdb->get_var("SELECT COUNT(id) FROM {$tableName} WHERE status='active';");
        $trashNumber   = $wpdb->get_var("SELECT COUNT(id) FROM {$tableName} WHERE status='trash';");
        $inactiveNumber   = $wpdb->get_var("SELECT COUNT(id) FROM {$tableName} WHERE status='inactive';");
        $url           = admin_url('admin.php?page=' . MXESDM_DISCOUNT_RULES_MENU_SLUG);
    
        $statusLinks   = [];
    
        // All.
        $statusLinks['all'] = [
            'url'     => remove_query_arg('item_status', $url),
            'label'   => __('All', 'ewd-smart-discount-manager'),
            'current' => empty($itemStatus) || 'all' == $itemStatus,
        ];
    
        // active.
        $statusLinks['active'] = [
            'url'     => add_query_arg('item_status', 'active', $url),
            'label'   => sprintf(
                _nx(
                    'Active <span class="count">(%s)</span>',
                    'Active <span class="count">(%s)</span>',
                    $activeNumber,
                    'active'
                ),
                number_format_i18n($activeNumber)
            ),
            'current' => 'active' == $itemStatus,
        ];
    
        if ($activeNumber == 0) {
            unset($statusLinks['active']);
        }
    
        // Inactive.
        $statusLinks['inactive'] = [
            'url'     => add_query_arg('item_status', 'inactive', $url),
            'label'   => sprintf(
                _nx(
                    'Inactive <span class="count">(%s)</span>',
                    'Inactive <span class="count">(%s)</span>',
                    $inactiveNumber,
                    'inactive'
                ),
                number_format_i18n($inactiveNumber)
            ),
            'current' => 'inactive' == $itemStatus,
        ];
    
        if ($inactiveNumber == 0) {
            unset($statusLinks['inactive']);
        }
    
        // Trash.
        $statusLinks['trash'] = [
            'url'     => add_query_arg('item_status', 'trash', $url),
            'label'   => sprintf(
                _nx(
                    'Trash <span class="count">(%s)</span>',
                    'Trash <span class="count">(%s)</span>',
                    $trashNumber,
                    'trash'
                ),
                number_format_i18n($trashNumber)
            ),
            'current' => 'trash' == $itemStatus,
        ];
    
        if ($trashNumber == 0) {
            unset($statusLinks['trash']);
        }
    
        return $this->get_views_links($statusLinks);
    }
    
    

    public function no_items()
    {

        $itemStatus = isset( $_GET['item_status'] ) ? trim( $_GET['item_status'] ) : 'active';
        
        if ($itemStatus == 'trash') {

            _e( 'No items found in trash.' );

        } else {

            _e( 'No items found.' );

        }

    }

}

if (!function_exists('mxesdmTableLayout')) {

    function mxesdmTableLayout() {

        global $wpdb;
    
        $tableName = $wpdb->prefix . MXESDM_TABLE_SLUG;
    
        $isTable = $wpdb->get_var(
    
            $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $tableName ) )
    
        );
    
        if (!$isTable) return;
    
        ?>
            <h1 class="wp-heading-inline"><?php _e( 'Discount Rules list', 'ewd-smart-discount-manager' ); ?></h1>
            <a href="<?php echo admin_url( 'admin.php?page=' . MXESDM_CREATE_DISCOUNT_RULE_MENU ); ?>" class="page-title-action"><?= __('Add New', 'ewd-smart-discount-manager') ?></a>
            <hr class="wp-header-end">
        <?php
    
        $tableInstance = new MXESDMDiscountRuleTable();
        
        $tableInstance->prepare_items();
    
        $tableInstance->views();
    
        echo '<form id="mxesdm_custom_talbe_search_form" method="post">';
            $tableInstance->search_box( 'Search Items', 'mxesdm_custom_talbe_search_input' );
        echo '</form>';
    
        echo '<form id="mxesdm_custom_talbe_form" method="post">';
            $tableInstance->display();
        echo '</form>';
    
    }

}
