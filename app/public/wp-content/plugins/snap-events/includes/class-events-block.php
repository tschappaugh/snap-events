<?php
/**
 * Block registration for the Events Grid and Events List blocks
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
 * Registers the Events Grid and Events List Gutenberg blocks and editor sidebar.
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
     * Register blocks with WordPress
     *
     * Uses block.json metadata for configuration.
     * Each block's render.php template handles server-side rendering.
     */
    public function register_block() {
        // Register the frontend stylesheet (loads in both editor and frontend)
        wp_register_style(
            'snap-events-grid-style',
            SNAP_EVENTS_PLUGIN_URL . 'assets/css/events-display.css',
            [],
            SNAP_EVENTS_VERSION
        );

        // Register the interactive elements stylesheet
        wp_register_style(
            'snap-events-interactive-style',
            SNAP_EVENTS_PLUGIN_URL . 'assets/css/events-interactive.css',
            [],
            SNAP_EVENTS_VERSION
        );

        // Register the list layout stylesheet
        wp_register_style(
            'snap-events-list-style',
            SNAP_EVENTS_PLUGIN_URL . 'assets/css/events-list.css',
            [],
            SNAP_EVENTS_VERSION
        );

        // Register the events grid block with its styles
        register_block_type(
            SNAP_EVENTS_PLUGIN_DIR . 'build/blocks/events-grid',
            [
                'style' => [
                    'snap-events-grid-style',
                    'snap-events-interactive-style',
                ],
            ]
        );

        // Register the events list block with its styles
        register_block_type(
            SNAP_EVENTS_PLUGIN_DIR . 'build/blocks/events-list',
            [
                'style' => [
                    'snap-events-grid-style',
                    'snap-events-interactive-style',
                    'snap-events-list-style',
                ],
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
