<?php
/**
 * REST API endpoint for events
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Snap_Events_REST
 *
 * Registers a custom REST endpoint for paginated, sortable event queries.
 * Used by the frontend JavaScript for Load More and Sort toggle features.
 */
class Snap_Events_REST {

    const NAMESPACE = 'snap-events/v1';
    const ROUTE     = '/events';

    /**
     * Initialize REST route registration
     */
    public function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Register the events endpoint
     */
    public function register_routes() {
        register_rest_route(
            self::NAMESPACE,
            self::ROUTE,
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_events' ],
                'permission_callback' => '__return_true',
                'args'                => [
                    'page' => [
                        'type'              => 'integer',
                        'default'           => 1,
                        'minimum'           => 1,
                        'sanitize_callback' => 'absint',
                    ],
                    'per_page' => [
                        'type'              => 'integer',
                        'default'           => 6,
                        'minimum'           => 1,
                        'maximum'           => 100,
                        'sanitize_callback' => 'absint',
                    ],
                    'order' => [
                        'type'              => 'string',
                        'default'           => 'ASC',
                        'enum'              => [ 'ASC', 'DESC' ],
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ]
        );
    }

    /**
     * Handle the events request
     *
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response Response with events data and pagination info.
     */
    public function get_events( $request ) {
        $page     = $request->get_param( 'page' );
        $per_page = $request->get_param( 'per_page' );
        $order    = $request->get_param( 'order' );

        $events = Snap_Events_Query::get_events( [
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'order'          => $order,
        ] );

        $total    = Snap_Events_Query::get_events_count();
        $has_more = ( $page * $per_page ) < $total;

        return new WP_REST_Response( [
            'events'       => $events,
            'total'        => $total,
            'has_more'     => $has_more,
            'current_page' => $page,
        ], 200 );
    }
}
