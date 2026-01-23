<?php
/**
 * Block registration for the Events Grid block
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Snap_Events_Block
 *
 * Registers the Events Grid Gutenberg block.
 */
class Snap_Events_Block {

    /**
     * Initialize the block registration
     */
    public function __construct() {
        // Register immediately since we're already running on init hook
        $this->register_block();
    }

    /**
     * Register the block with WordPress
     *
     * Uses block.json metadata for configuration.
     * The render.php template handles server-side rendering.
     */
    public function register_block() {
        register_block_type( SNAP_EVENTS_PLUGIN_DIR . 'build/blocks/events-grid' );
    }
}
