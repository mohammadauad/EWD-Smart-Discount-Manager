<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) exit;

/**
 * The MXESDMMxView class.
 *
 * This class helps connect a view file
 * to a controller and a model.
 */
class MXESDMMxView
{

    public function __construct( ...$args )
    {
        
        $this->render( ...$args );
    }
    
    // Render HTML.
    public function render( $file, $data = NULL )
    {

        // View content.
        if (file_exists( MXESDM_PLUGIN_ABS_PATH . "includes/admin/views/{$file}.php")) {

            // Get a data from a model.
            $data = $data;

            require_once MXESDM_PLUGIN_ABS_PATH . "includes/admin/views/{$file}.php";
        } else { ?>

            <div class="notice notice-error is-dismissible">

                <p>The view file "<b>includes/admin/views/<?php echo esc_attr( $file ); ?>.php</b>" doesn't exists.</p>
 
            </div>
        <?php }
    }
    
}
