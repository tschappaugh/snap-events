<?php
/**
 * Server-side rendering of the Events Grid block
 *
 * @package Snap_Events
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Extract attributes with defaults
$count              = isset( $attributes['count'] ) ? $attributes['count'] : 6;
$columns            = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
$show_excerpt       = isset( $attributes['showExcerpt'] ) ? $attributes['showExcerpt'] : true;
$show_image         = isset( $attributes['showImage'] ) ? $attributes['showImage'] : true;
$show_date          = isset( $attributes['showDate'] ) ? $attributes['showDate'] : true;
$show_location      = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true;
$enable_load_more   = isset( $attributes['enableLoadMore'] ) ? $attributes['enableLoadMore'] : true;
$enable_sort        = isset( $attributes['enableSort'] ) ? $attributes['enableSort'] : true;
$default_sort_order = isset( $attributes['defaultSortOrder'] ) ? $attributes['defaultSortOrder'] : 'ASC';

// Card style attributes
$card_bg_color      = isset( $attributes['cardBackgroundColor'] ) ? $attributes['cardBackgroundColor'] : '#f5f5f5';
$card_text_color    = isset( $attributes['cardTextColor'] ) ? $attributes['cardTextColor'] : '#333333';
$card_heading_color = isset( $attributes['cardHeadingColor'] ) ? $attributes['cardHeadingColor'] : '#000000';
$card_link_color    = isset( $attributes['cardLinkColor'] ) ? $attributes['cardLinkColor'] : '#0073aa';
$card_padding       = isset( $attributes['cardPadding'] ) ? $attributes['cardPadding'] : 30;
$card_border_radius = isset( $attributes['cardBorderRadius'] ) ? $attributes['cardBorderRadius'] : 0;
$card_box_shadow    = isset( $attributes['cardBoxShadow'] ) ? $attributes['cardBoxShadow'] : false;
$card_border_width  = isset( $attributes['cardBorderWidth'] ) ? $attributes['cardBorderWidth'] : 0;
$card_border_color  = isset( $attributes['cardBorderColor'] ) ? $attributes['cardBorderColor'] : '#cccccc';
$grid_gap           = isset( $attributes['gridGap'] ) ? $attributes['gridGap'] : 30;

// Button style attributes
$btn_bg_color      = isset( $attributes['buttonBackgroundColor'] ) ? $attributes['buttonBackgroundColor'] : '#333333';
$btn_text_color    = isset( $attributes['buttonTextColor'] ) ? $attributes['buttonTextColor'] : '#ffffff';
$btn_border_color  = isset( $attributes['buttonBorderColor'] ) ? $attributes['buttonBorderColor'] : '#333333';
$btn_border_width  = isset( $attributes['buttonBorderWidth'] ) ? $attributes['buttonBorderWidth'] : 0;
$btn_border_radius = isset( $attributes['buttonBorderRadius'] ) ? $attributes['buttonBorderRadius'] : 4;
$btn_box_shadow    = isset( $attributes['buttonBoxShadow'] ) ? $attributes['buttonBoxShadow'] : false;
$btn_shadow_style = $btn_box_shadow ? 'box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);' : '';

// Build card inline styles
$card_style  = 'background-color: ' . esc_attr( $card_bg_color ) . ';';
$card_style .= ' color: ' . esc_attr( $card_text_color ) . ';';
$card_style .= ' padding: ' . intval( $card_padding ) . 'px;';
if ( $card_border_radius > 0 ) {
    $card_style .= ' border-radius: ' . intval( $card_border_radius ) . 'px;';
}
if ( $card_box_shadow ) {
    $card_style .= ' box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);';
}
if ( $card_border_width > 0 ) {
    $card_style .= ' border: ' . intval( $card_border_width ) . 'px solid ' . esc_attr( $card_border_color ) . ';';
}

// Query events using our query class
$events = Snap_Events_Query::get_events( [
    'posts_per_page' => $count,
    'order'          => $default_sort_order,
] );

// Get total count to determine if Load More should be visible
$total_events = Snap_Events_Query::get_events_count();
$has_more     = $count < $total_events;

// Build config for frontend JavaScript
$block_config = wp_json_encode( [
    'count'              => $count,
    'columns'            => $columns,
    'showExcerpt'        => $show_excerpt,
    'showImage'          => $show_image,
    'showDate'           => $show_date,
    'showLocation'       => $show_location,
    'enableLoadMore'     => $enable_load_more,
    'enableSort'         => $enable_sort,
    'defaultSortOrder'   => $default_sort_order,
    'cardBackgroundColor' => $card_bg_color,
    'cardTextColor'       => $card_text_color,
    'cardHeadingColor'    => $card_heading_color,
    'cardLinkColor'       => $card_link_color,
    'cardPadding'         => $card_padding,
    'cardBorderRadius'    => $card_border_radius,
    'cardBoxShadow'       => $card_box_shadow,
    'cardBorderWidth'     => $card_border_width,
    'cardBorderColor'     => $card_border_color,
    'restUrl'             => esc_url_raw( rest_url( 'snap-events/v1/events' ) ),
] );

// Get block wrapper attributes (applies color, spacing, etc. from block supports)
$anchor = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : '';
$wrapper_attrs = [
    'class'       => 'snap-events-grid snap-events-columns-' . $columns,
    'style'       => 'gap: ' . intval( $grid_gap ) . 'px; --btn-bg: ' . esc_attr( $btn_bg_color ) . '; --btn-color: ' . esc_attr( $btn_text_color ) . '; --btn-radius: ' . intval( $btn_border_radius ) . 'px; --btn-border: ' . intval( $btn_border_width ) . 'px solid ' . esc_attr( $btn_border_color ) . ';',
    'data-config' => $block_config,
];

if ( $anchor ) {
    $wrapper_attrs['id'] = $anchor;
}
$wrapper_attributes = get_block_wrapper_attributes( $wrapper_attrs );

// Start output
if ( empty( $events ) ) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output from get_block_wrapper_attributes() is pre-escaped.
    printf(
        '<div %s><p class="snap-events-no-events">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'No upcoming events found.', 'snap-events' )
    );
    return;
}
?>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output from get_block_wrapper_attributes() is pre-escaped. ?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ( $events as $event ) : ?>
        <article class="snap-event-card" style="<?php echo esc_attr( $card_style . ' --card-heading-color: ' . $card_heading_color . '; --card-link-color: ' . $card_link_color . ';' ); ?>">
            
            <?php if ( $show_image && ! empty( $event['thumbnail_url'] ) ) : ?>
                <div class="snap-event-image">
                    <img src="<?php echo esc_url( $event['thumbnail_url'] ); ?>" alt="" role="presentation">
                </div>
            <?php endif; ?>

            <div class="snap-event-content">
                <h3 class="snap-event-title" style="color: var(--card-heading-color, #000000);">
                    <a href="<?php echo esc_url( $event['permalink'] ); ?>" style="color: var(--card-heading-color, #000000);">
                        <?php echo esc_html( $event['title'] ); ?>
                    </a>
                </h3>

                <?php if ( $show_date && ! empty( $event['start_date'] ) ) : ?>
                    <p class="snap-event-date">
                        <strong><?php esc_html_e( 'Date:', 'snap-events' ); ?></strong>
                        <?php echo esc_html( $event['start_date'] ); ?>
                        <?php if ( ! empty( $event['end_date'] ) && $event['end_date'] !== $event['start_date'] ) : ?>
                            - <?php echo esc_html( $event['end_date'] ); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <?php if ( $show_location && ! empty( $event['venue'] ) ) : ?>
                    <p class="snap-event-venue">
                        <strong><?php esc_html_e( 'Venue:', 'snap-events' ); ?></strong>
                        <span class="snap-event-venue-name"><?php echo esc_html( $event['venue'] ); ?></span>
                    </p>
                <?php endif; ?>

                <?php
                // Build location string (city, state, country - without venue)
                $location_parts = array_filter( [ $event['city'], $event['state'], $event['country'] ] );
                $location = implode( ', ', $location_parts );
                if ( $show_location && ! empty( $location ) ) :
                ?>
                    <p class="snap-event-location">
                        <strong><?php esc_html_e( 'Location:', 'snap-events' ); ?></strong>
                        <span class="snap-event-location-text"><?php echo esc_html( $location ); ?></span>
                    </p>
                <?php endif; ?>

                <?php if ( $show_excerpt && ! empty( $event['excerpt'] ) ) : ?>
                    <div class="snap-event-excerpt">
                        <?php echo wp_kses_post( $event['excerpt'] ); ?>
                    </div>
                <?php endif; ?>

                <a href="<?php echo esc_url( $event['permalink'] ); ?>" class="snap-event-link" style="color: var(--card-link-color, #0073aa);" aria-hidden="true" tabindex="-1">
                    <?php esc_html_e( 'View Event', 'snap-events' ); ?>
                </a>
            </div>
        </article>
    <?php endforeach; ?>

    <?php if ( $enable_sort || $enable_load_more ) : ?>
        <div class="snap-events-controls">
    <?php if ( $enable_sort ) : ?>
        <button class="snap-events-sort-toggle" style="<?php echo esc_attr( $btn_shadow_style ); ?>" data-current-order="<?php echo esc_attr( $default_sort_order ); ?>" aria-label="<?php esc_attr_e( 'Toggle sort order', 'snap-events' ); ?>">
            <svg class="snap-events-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4v16M7 4l-4 4M7 4l4 4M17 20V4M17 20l-4-4M17 20l4-4"/></svg>
            <span class="snap-events-sort-label">
                <?php echo $default_sort_order === 'ASC'
                    ? esc_html__( 'Soonest First', 'snap-events' )
                    : esc_html__( 'Furthest Out First', 'snap-events' ); ?>
            </span>
        </button>
    <?php endif; ?>

    <?php if ( $enable_load_more ) : ?>
        <button class="snap-events-load-more<?php echo ! $has_more ? ' snap-events-hidden' : ''; ?>" style="<?php echo esc_attr( $btn_shadow_style ); ?>" aria-label="<?php esc_attr_e( 'Load more events', 'snap-events' ); ?>">
            <svg class="snap-events-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            <span class="snap-events-load-more-label"><?php esc_html_e( 'Load More Events', 'snap-events' ); ?></span>
        </button>
    <?php endif; ?>
            <div class="snap-events-status" role="status" aria-live="polite" aria-atomic="true"></div>
        </div>
    <?php endif; ?>
</div>
<?php

