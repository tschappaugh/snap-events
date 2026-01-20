<?php
/**
 * Template loader for single event pages
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Snap_Events_Template
 *
 * Loads custom template for single snap_event posts.
 */
class Snap_Events_Template {

    /**
     * Initialize template loader
     */
    public function __construct() {
        add_filter( 'template_include', [ $this, 'load_single_template' ] );
    }

    /**
     * Load custom template for single events
     *
     * @param string $template The path to the template file.
     * @return string Modified template path.
     */
    public function load_single_template( $template ) {
        if ( is_singular( 'snap_event' ) ) {
            $custom_template = SNAP_EVENTS_PLUGIN_DIR . 'templates/single-snap_event.php';
            
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }
        
        return $template;
    }
}
