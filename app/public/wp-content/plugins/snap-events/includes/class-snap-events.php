<?php
/**
 * Main plugin class
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Snap_Events class
 * 
 * Uses the singleton pattern to ensure only one instance exists.
 * This is important because we're registering hooks - we don't want
 * them registered multiple times.
 */
class Snap_Events {

    /**
     * Single instance of this class
     *
     * @var Snap_Events|null
     */
    private static $instance = null;

    /**
     * Get the singleton instance
     *
     * @return Snap_Events
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - private to enforce singleton
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required class files
     */
    private function load_dependencies() {
        // Custom Post Type registration
        require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-cpt.php';
        
        // REST API meta field registration
        require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-meta.php';
        
        // Event query helper class
        require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-query.php';
        
        // Block registration and rendering
        require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-block.php';
        
        // Single event template loader
        require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-template.php';
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Initialize components on 'init' hook
        add_action( 'init', [ $this, 'init_components' ] );
        
        // Enqueue frontend styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
    }

    /**
     * Initialize plugin components
     * 
     * Runs on the 'init' hook to ensure WordPress is ready.
     */
    public function init_components() {
        // Register custom post type
        $cpt = new Snap_Events_CPT();
        $cpt->register_post_type();
        
        // Register meta fields for REST API
        $meta = new Snap_Events_Meta();
        $meta->register_meta_fields();
        
        // Block registers itself via constructor hook
        new Snap_Events_Block();
        
        // Initialize template loader
        new Snap_Events_Template();
    }

    /**
     * Enqueue frontend styles
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'snap-events-frontend',
            SNAP_EVENTS_PLUGIN_URL . 'assets/css/events-display.css',
            [],
            SNAP_EVENTS_VERSION
        );
    }
}
