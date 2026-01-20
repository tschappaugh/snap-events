<?php
/**
 * Plugin Name: Snap Events
 * Plugin URI: https://github.com/tschappaugh/snap-events
 * Description: A simple events plugin with Gutenberg block support for displaying upcoming events.
 * Version: 1.0.0
 * Author: Tony Schappaugh
 * Author URI: https://github.com/tschappaugh
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: snap-events
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin version - update this when releasing new versions
define( 'SNAP_EVENTS_VERSION', '1.0.0' );

// Plugin directory path (with trailing slash) - for including PHP files
define( 'SNAP_EVENTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Plugin URL (with trailing slash) - for enqueueing assets (CSS, JS)
define( 'SNAP_EVENTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load the main plugin class
require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-snap-events.php';

/**
 * Initialize the plugin
 * 
 * Hooked to 'plugins_loaded' to ensure WordPress core and other plugins
 * are fully loaded before we initialize our plugin.
 */
function snap_events_init() {
    Snap_Events::get_instance();
}
add_action( 'plugins_loaded', 'snap_events_init' );

/**
 * Activation hook
 * 
 * Runs when the plugin is activated. Registers the CPT and flushes
 * rewrite rules so the /events/ URLs work immediately.
 */
function snap_events_activate() {
    // Load the CPT class and register the post type
    require_once SNAP_EVENTS_PLUGIN_DIR . 'includes/class-events-cpt.php';
    $cpt = new Snap_Events_CPT();
    $cpt->register_post_type();
    
    // Flush rewrite rules so /events/ permalink works
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'snap_events_activate' );

/**
 * Deactivation hook
 * 
 * Runs when the plugin is deactivated. Flushes rewrite rules to clean up.
 */
function snap_events_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'snap_events_deactivate' );
