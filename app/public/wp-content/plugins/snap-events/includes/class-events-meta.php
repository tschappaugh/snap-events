<?php
/**
 * REST API meta field registration for Events
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers event meta fields for REST API access
 * 
 * This allows the Gutenberg editor to read and write meta fields
 * via the WordPress REST API, enabling our custom sidebar panel.
 */
class Snap_Events_Meta {

    /**
     * Meta fields configuration
     * 
     * Each field has:
     * - type: Data type for validation (string, integer, boolean, etc.)
     * - description: Human-readable description for documentation
     * - default: Default value if not set
     */
    private $meta_fields = [
        'start_date' => [
            'type'        => 'string',
            'description' => 'Event start date in Ymd format (e.g., 20260115)',
            'default'     => '',
        ],
        'end_date' => [
            'type'        => 'string',
            'description' => 'Event end date in Ymd format (e.g., 20260116)',
            'default'     => '',
        ],
        'venue' => [
            'type'        => 'string',
            'description' => 'Venue or location name',
            'default'     => '',
        ],
        'city' => [
            'type'        => 'string',
            'description' => 'City where the event takes place',
            'default'     => '',
        ],
        'state' => [
            'type'        => 'string',
            'description' => 'State or province',
            'default'     => '',
        ],
        'country' => [
            'type'        => 'string',
            'description' => 'Country',
            'default'     => '',
        ],
    ];

    /**
     * Register all meta fields for the snap_event post type
     */
    public function register_meta_fields() {
        foreach ( $this->meta_fields as $meta_key => $config ) {
            register_post_meta(
                Snap_Events_CPT::POST_TYPE,  // Only for snap_event posts
                $meta_key,
                [
                    'type'              => $config['type'],
                    'description'       => $config['description'],
                    'single'            => true,
                    'default'           => $config['default'],
                    'show_in_rest'      => true,
                    'sanitize_callback' => [ $this, 'sanitize_meta_field' ],
                    'auth_callback'     => [ $this, 'auth_callback' ],
                ]
            );
        }
    }

    /**
     * Sanitize meta field values before saving
     *
     * @param mixed $value The value to sanitize.
     * @return string Sanitized value.
     */
    public function sanitize_meta_field( $value ) {
        return sanitize_text_field( $value );
    }
    
    /**
     * Check if user is authorized to edit meta fields
     *
     * @return bool Whether the user can edit.
     */
    public function auth_callback() {
        return current_user_can( 'edit_posts' );
    }
}
