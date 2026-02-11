<?php
/**
 * Server-side rendering of the Events List block
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
$show_excerpt       = isset( $attributes['showExcerpt'] ) ? $attributes['showExcerpt'] : true;
$show_image         = isset( $attributes['showImage'] ) ? $attributes['showImage'] : true;
$show_date          = isset( $attributes['showDate'] ) ? $attributes['showDate'] : true;
$show_location      = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true;
$enable_load_more   = isset( $attributes['enableLoadMore'] ) ? $attributes['enableLoadMore'] : true;
$enable_sort        = isset( $attributes['enableSort'] ) ? $attributes['enableSort'] : true;
$default_sort_order = isset( $attributes['defaultSortOrder'] ) ? $attributes['defaultSortOrder'] : 'ASC';

// List style attributes
$text_color    = isset( $attributes['textColor'] ) ? $attributes['textColor'] : '#333333';
$heading_color = isset( $attributes['headingColor'] ) ? $attributes['headingColor'] : '#000000';
$link_color    = isset( $attributes['linkColor'] ) ? $attributes['linkColor'] : '#0073aa';
$border_color  = isset( $attributes['borderColor'] ) ? $attributes['borderColor'] : '#dddddd';
$border_width  = isset( $attributes['borderWidth'] ) ? $attributes['borderWidth'] : 1;
$item_padding  = isset( $attributes['itemPadding'] ) ? $attributes['itemPadding'] : 20;

// Button style attributes
$btn_bg_color      = isset( $attributes['buttonBackgroundColor'] ) ? $attributes['buttonBackgroundColor'] : '#333333';
$btn_text_color    = isset( $attributes['buttonTextColor'] ) ? $attributes['buttonTextColor'] : '#ffffff';
$btn_border_color  = isset( $attributes['buttonBorderColor'] ) ? $attributes['buttonBorderColor'] : '#333333';
$btn_border_width  = isset( $attributes['buttonBorderWidth'] ) ? $attributes['buttonBorderWidth'] : 0;
$btn_border_radius = isset( $attributes['buttonBorderRadius'] ) ? $attributes['buttonBorderRadius'] : 4;
$btn_box_shadow    = isset( $attributes['buttonBoxShadow'] ) ? $attributes['buttonBoxShadow'] : false;
$btn_shadow_style = $btn_box_shadow ? 'box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);' : '';

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
    'count'            => $count,
    'showExcerpt'      => $show_excerpt,
    'showImage'        => $show_image,
    'showDate'         => $show_date,
    'showLocation'     => $show_location,
    'enableLoadMore'   => $enable_load_more,
    'enableSort'       => $enable_sort,
    'defaultSortOrder' => $default_sort_order,
    'textColor'        => $text_color,
    'headingColor'     => $heading_color,
    'linkColor'        => $link_color,
    'borderColor'      => $border_color,
    'borderWidth'      => $border_width,
    'itemPadding'      => $item_padding,
    'restUrl'          => esc_url_raw( rest_url( 'snap-events/v1/events' ) ),
    'restNonce'        => wp_create_nonce( 'wp_rest' ),
] );

// Get block wrapper attributes
$anchor = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : '';
$wrapper_attrs = [
    'class'       => 'snap-events-list',
    'style'       => 'border-top: ' . intval( $border_width ) . 'px solid ' . esc_attr( $border_color ) . '; color: ' . esc_attr( $text_color ) . '; --list-heading-color: ' . esc_attr( $heading_color ) . '; --list-link-color: ' . esc_attr( $link_color ) . '; --btn-bg: ' . esc_attr( $btn_bg_color ) . '; --btn-color: ' . esc_attr( $btn_text_color ) . '; --btn-radius: ' . intval( $btn_border_radius ) . 'px; --btn-border: ' . intval( $btn_border_width ) . 'px solid ' . esc_attr( $btn_border_color ) . ';',
    'data-config' => $block_config,
];

if ( $anchor ) {
    $wrapper_attrs['id'] = $anchor;
}
$wrapper_attributes = get_block_wrapper_attributes( $wrapper_attrs );

// Start output
if ( empty( $events ) ) {
    printf(
        '<div %s><p class="snap-events-no-events">%s</p></div>',
        $wrapper_attributes,
        esc_html__( 'No upcoming events found.', 'snap-events' )
    );
    return;
}

?>
<div <?php echo $wrapper_attributes; ?>>
    <?php foreach ( $events as $event ) : ?>
        <div class="snap-event-list-item" style="border-bottom: <?php echo intval( $border_width ); ?>px solid <?php echo esc_attr( $border_color ); ?>; padding: <?php echo intval( $item_padding ); ?>px 0;">

            <?php if ( $show_image && ! empty( $event['thumbnail_url'] ) ) : ?>
                <div class="snap-event-list-image">
                    <img src="<?php echo esc_url( $event['thumbnail_url'] ); ?>" alt="" role="presentation">
                </div>
            <?php endif; ?>

            <div class="snap-event-list-content">
                <h3 class="snap-event-title" style="color: var(--list-heading-color, #000000);">
                    <a href="<?php echo esc_url( $event['permalink'] ); ?>" style="color: var(--list-heading-color, #000000);">
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

                <a href="<?php echo esc_url( $event['permalink'] ); ?>" class="snap-event-link" style="color: var(--list-link-color, #0073aa);" aria-hidden="true" tabindex="-1">
                    <?php esc_html_e( 'View Event', 'snap-events' ); ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if ( $enable_sort || $enable_load_more ) : ?>
        <div class="snap-events-controls">
            <?php if ( $enable_sort ) : ?>
                <button class="snap-events-sort-toggle" style="<?php echo $btn_shadow_style; ?>" data-current-order="<?php echo esc_attr( $default_sort_order ); ?>" aria-label="<?php esc_attr_e( 'Toggle sort order', 'snap-events' ); ?>">
                    <svg class="snap-events-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 4v16M7 4l-4 4M7 4l4 4M17 20V4M17 20l-4-4M17 20l4-4"/></svg>
                    <span class="snap-events-sort-label">
                        <?php echo $default_sort_order === 'ASC'
                            ? esc_html__( 'Soonest First', 'snap-events' )
                            : esc_html__( 'Furthest Out First', 'snap-events' ); ?>
                    </span>
                </button>
            <?php endif; ?>

            <?php if ( $enable_load_more ) : ?>
                <button class="snap-events-load-more<?php echo ! $has_more ? ' snap-events-hidden' : ''; ?>" style="<?php echo $btn_shadow_style; ?>" aria-label="<?php esc_attr_e( 'Load more events', 'snap-events' ); ?>">
                    <svg class="snap-events-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="snap-events-load-more-label"><?php esc_html_e( 'Load More Events', 'snap-events' ); ?></span>
                </button>
            <?php endif; ?>

            <div class="snap-events-status" role="status" aria-live="polite" aria-atomic="true"></div>
        </div>
    <?php endif; ?>
</div>
<?php
