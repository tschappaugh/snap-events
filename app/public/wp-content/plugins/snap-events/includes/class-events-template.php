<?php
/**
 * Template customizations for single event pages
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Snap_Events_Template
 *
 * Customizes display for single snap_event posts.
 */
class Snap_Events_Template {

    /**
     * Initialize template customizations
     */
    public function __construct() {
        add_filter( 'the_content', [ $this, 'prepend_event_meta' ] );
        add_filter( 'render_block', [ $this, 'remove_post_meta_blocks' ], 10, 2 );
    }

    /**
     * Prepend event meta to content on single event pages
     *
     * @param string $content The post content.
     * @return string Modified content with event meta prepended.
     */
    public function prepend_event_meta( $content ) {
        if ( ! is_singular( 'snap_event' ) || ! in_the_loop() || ! is_main_query() ) {
            return $content;
        }

        $post_id    = get_the_ID();
        $start_date = get_post_meta( $post_id, 'start_date', true );
        $end_date   = get_post_meta( $post_id, 'end_date', true );
        $venue      = get_post_meta( $post_id, 'venue', true );
        $city       = get_post_meta( $post_id, 'city', true );
        $state      = get_post_meta( $post_id, 'state', true );
        $country    = get_post_meta( $post_id, 'country', true );

        $formatted_start = '';
        $formatted_end   = '';

        if ( $start_date && strlen( $start_date ) === 8 ) {
            $date_obj = DateTime::createFromFormat( 'Ymd', $start_date );
            if ( $date_obj ) {
                $formatted_start = $date_obj->format( 'F j, Y' );
            }
        }

        if ( $end_date && strlen( $end_date ) === 8 ) {
            $date_obj = DateTime::createFromFormat( 'Ymd', $end_date );
            if ( $date_obj ) {
                $formatted_end = $date_obj->format( 'F j, Y' );
            }
        }

        $location_parts = array_filter( [ $city, $state, $country ] );
        $location       = implode( ', ', $location_parts );

        $meta_html = '<div class="snap-event-meta" style="background: #f5f5f5; padding: 20px; margin-bottom: 20px; border-radius: 4px;">';

        if ( $formatted_start ) {
            $meta_html .= '<p style="margin: 0 0 10px 0; color: #333;"><strong>' . esc_html__( 'Date:', 'snap-events' ) . '</strong> ';
            $meta_html .= esc_html( $formatted_start );
            if ( $formatted_end && $formatted_end !== $formatted_start ) {
                $meta_html .= ' - ' . esc_html( $formatted_end );
            }
            $meta_html .= '</p>';
        }

        if ( $venue ) {
            $meta_html .= '<p style="margin: 0 0 10px 0; color: #333;"><strong>' . esc_html__( 'Venue:', 'snap-events' ) . '</strong> ';
            $meta_html .= esc_html( $venue ) . '</p>';
        }

        if ( $location ) {
            $meta_html .= '<p style="margin: 0; color: #333;"><strong>' . esc_html__( 'Location:', 'snap-events' ) . '</strong> ';
            $meta_html .= esc_html( $location ) . '</p>';
        }

        $meta_html .= '</div>';

        return $meta_html . $content;
    }

    /**
     * Remove post meta blocks (author, categories, etc.) for snap_event posts
     *
     * @param string $block_content The block content.
     * @param array  $block         The block data.
     * @return string Modified block content.
     */
    public function remove_post_meta_blocks( $block_content, $block ) {
        if ( ! is_singular( 'snap_event' ) ) {
            return $block_content;
        }

        // Blocks to completely hide for snap_event
        $blocks_to_hide = [
            'core/post-author',
            'core/post-author-name',
            'core/post-terms',
            'core/query',
        ];

        if ( in_array( $block['blockName'], $blocks_to_hide, true ) ) {
            return '';
        }

        // Hide paragraph blocks containing only "Written by " or "in"
        if ( 'core/paragraph' === $block['blockName'] ) {
            $text = trim( strip_tags( $block_content ) );
            if ( $text === 'Written by' || $text === 'in' ) {
                return '';
            }
        }

        // Hide heading blocks containing "More posts"
        if ( 'core/heading' === $block['blockName'] ) {
            $text = trim( strip_tags( $block_content ) );
            if ( $text === 'More posts' ) {
                return '';
            }
        }

        // Hide group blocks that are now empty (contained only the byline)
        if ( 'core/group' === $block['blockName'] ) {
            $text = trim( strip_tags( $block_content ) );
            if ( empty( $text ) ) {
                return '';
            }
        }

        return $block_content;
    }
}
