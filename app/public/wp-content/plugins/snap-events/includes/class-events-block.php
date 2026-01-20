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
        add_action( 'init', [ $this, 'register_block' ] );
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
    }

    /**
     * Register the block with WordPress
     *
     * Uses block.json metadata for configuration.
     * The render.php template handles server-side rendering.
     */
    public function register_block() {
        register_block_type( SNAP_EVENTS_PLUGIN_DIR . 'src/blocks/events-grid' );
    }

    /**
     * Enqueue editor-only assets (sidebar plugin)
     */
    public function enqueue_editor_assets() {
        $asset_file = SNAP_EVENTS_PLUGIN_DIR . 'build/editor/index.asset.php';
        
        if ( ! file_exists( $asset_file ) ) {
            return;
        }
        
        $asset = include $asset_file;
        
        wp_enqueue_script(
            'snap-events-editor',
            SNAP_EVENTS_PLUGIN_URL . 'build/editor/index.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );
    }
}
