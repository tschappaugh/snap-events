<?php
/**
 * Custom Post Type registration for Events
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles registration of the snap_event custom post type
 */
class Snap_Events_CPT {

    /**
     * The post type slug
     * 
     * Used internally by WordPress to identify this post type.
     * Keep it short and use underscores, not hyphens.
     */
    const POST_TYPE = 'snap_event';

    /**
     * Register the custom post type
     */
    public function register_post_type() {
        $labels = [
            'name'                  => _x( 'Events', 'Post type general name', 'snap-events' ),
            'singular_name'         => _x( 'Event', 'Post type singular name', 'snap-events' ),
            'menu_name'             => _x( 'Events', 'Admin Menu text', 'snap-events' ),
            'name_admin_bar'        => _x( 'Event', 'Add New on Toolbar', 'snap-events' ),
            'add_new'               => __( 'Add New', 'snap-events' ),
            'add_new_item'          => __( 'Add New Event', 'snap-events' ),
            'new_item'              => __( 'New Event', 'snap-events' ),
            'edit_item'             => __( 'Edit Event', 'snap-events' ),
            'view_item'             => __( 'View Event', 'snap-events' ),
            'all_items'             => __( 'All Events', 'snap-events' ),
            'search_items'          => __( 'Search Events', 'snap-events' ),
            'parent_item_colon'     => __( 'Parent Events:', 'snap-events' ),
            'not_found'             => __( 'No events found.', 'snap-events' ),
            'not_found_in_trash'    => __( 'No events found in Trash.', 'snap-events' ),
            'featured_image'        => _x( 'Event Image', 'Overrides the "Featured Image" phrase', 'snap-events' ),
            'set_featured_image'    => _x( 'Set event image', 'Overrides the "Set featured image" phrase', 'snap-events' ),
            'remove_featured_image' => _x( 'Remove event image', 'Overrides the "Remove featured image" phrase', 'snap-events' ),
            'use_featured_image'    => _x( 'Use as event image', 'Overrides the "Use as featured image" phrase', 'snap-events' ),
            'archives'              => _x( 'Event archives', 'The post type archive label', 'snap-events' ),
            'insert_into_item'      => _x( 'Insert into event', 'Overrides the "Insert into post" phrase', 'snap-events' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this event', 'Overrides the "Uploaded to this post" phrase', 'snap-events' ),
            'filter_items_list'     => _x( 'Filter events list', 'Screen reader text', 'snap-events' ),
            'items_list_navigation' => _x( 'Events list navigation', 'Screen reader text', 'snap-events' ),
            'items_list'            => _x( 'Events list', 'Screen reader text', 'snap-events' ),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'events', 'with_front' => false ],
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => [ 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ],
            'show_in_rest'       => true,
        ];
        
        register_post_type( self::POST_TYPE, $args );
    }
}
