<?php
/**
 * Event query helper class
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles querying and formatting event data
 * 
 * Uses static methods so it can be called from anywhere without
 * needing to instantiate the class: Snap_Events_Query::get_events()
 */
class Snap_Events_Query {

    /**
     * Get upcoming events
     *
     * @param array $args {
     *     Optional. Arguments to filter events.
     *
     *     @type int    $posts_per_page Number of events to return. -1 for all. Default -1.
     *     @type string $city           Filter by city. Default empty.
     *     @type string $state          Filter by state. Default empty.
     *     @type string $country        Filter by country. Default empty.
     * }
     * @return array Array of event data arrays.
     */
    public static function get_events( $args = [] ) {
        // Default arguments
        $defaults = [
            'posts_per_page' => -1,
            'city'           => '',
            'state'          => '',
            'country'        => '',
        ];

        $args = wp_parse_args( $args, $defaults );

        // Today's date in Ymd format for comparison
        $today = current_time( 'Ymd' );

        // Base query arguments
        $query_args = [
            'post_type'      => Snap_Events_CPT::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => $args['posts_per_page'],
            'meta_key'       => 'start_date',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        ];

        // Meta query to filter only future events
        $meta_query = [
            'relation' => 'AND',
            [
                'key'     => 'start_date',
                'value'   => $today,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ];

        // Add location filters if specified
        if ( ! empty( $args['city'] ) ) {
            $meta_query[] = [
                'key'     => 'city',
                'value'   => $args['city'],
                'compare' => '=',
            ];
        }

        if ( ! empty( $args['state'] ) ) {
            $meta_query[] = [
                'key'     => 'state',
                'value'   => $args['state'],
                'compare' => '=',
            ];
        }

        if ( ! empty( $args['country'] ) ) {
            $meta_query[] = [
                'key'     => 'country',
                'value'   => $args['country'],
                'compare' => '=',
            ];
        }

        $query_args['meta_query'] = $meta_query;

        // Execute the query
        $query = new WP_Query( $query_args );
        $events = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                $post_id = get_the_ID();

                $events[] = [
                    'id'            => $post_id,
                    'title'         => get_the_title(),
                    'permalink'     => get_permalink(),
                    'excerpt'       => get_the_excerpt(),
                    'content'       => get_the_content(),
                    'thumbnail_url' => get_the_post_thumbnail_url( $post_id, 'medium' ),
                    'start_date'    => self::format_date( get_post_meta( $post_id, 'start_date', true ) ),
                    'end_date'      => self::format_date( get_post_meta( $post_id, 'end_date', true ) ),
                    'venue'         => get_post_meta( $post_id, 'venue', true ),
                    'city'          => get_post_meta( $post_id, 'city', true ),
                    'state'         => get_post_meta( $post_id, 'state', true ),
                    'country'       => get_post_meta( $post_id, 'country', true ),
                ];
            }

            wp_reset_postdata();
        }

        return $events;
    }

    /**
     * Format a date from Ymd to human-readable format
     *
     * @param string $date Date in Ymd format (e.g., 20260115).
     * @return string Formatted date (e.g., "January 15, 2026") or empty string.
     */
    private static function format_date( $date ) {
        if ( empty( $date ) || strlen( $date ) !== 8 ) {
            return '';
        }

        $date_obj = DateTime::createFromFormat( 'Ymd', $date );

        if ( $date_obj === false ) {
            return '';
        }

        return $date_obj->format( 'F j, Y' );
    }
    
    /**
     * Build a location string from event meta
     *
     * @param array $event Event data array from get_events().
     * @return string Formatted location string.
     */
    public static function get_location_string( $event ) {
        $parts = [];

        if ( ! empty( $event['venue'] ) ) {
            $parts[] = $event['venue'];
        }

        if ( ! empty( $event['city'] ) ) {
            $parts[] = $event['city'];
        }

        if ( ! empty( $event['state'] ) ) {
            $parts[] = $event['state'];
        }

        if ( ! empty( $event['country'] ) ) {
            $parts[] = $event['country'];
        }

        return implode( ', ', $parts );
    }
}
