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
 * Registers the Events Grid Gutenberg block and editor sidebar.
 */
class Snap_Events_Block {

    /**
     * Initialize the block registration
     */
    public function __construct() {
        // Register immediately since we're already running on init hook
        $this->register_block();

        // Enqueue editor sidebar scripts
        add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
    }

    /**
     * Register the block with WordPress
     *
     * Uses block.json metadata for configuration.
     * The render.php template handles server-side rendering.
     */
    public function register_block() {
        // Register the frontend stylesheet (loads in both editor and frontend)
        wp_register_style(
            'snap-events-grid-style',
            SNAP_EVENTS_PLUGIN_URL . 'assets/css/events-display.css',
            [],
            SNAP_EVENTS_VERSION
        );

        // Register block with the style
        register_block_type(
            SNAP_EVENTS_PLUGIN_DIR . 'build/blocks/events-grid',
            [
                'style' => 'snap-events-grid-style',
            ]
        );
    }

    /**
     * Enqueue editor sidebar assets
     *
     * Loads the editor sidebar script that provides the Event Details panel.
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
