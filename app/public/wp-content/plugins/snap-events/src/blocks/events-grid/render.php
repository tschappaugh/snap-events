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
$count         = isset( $attributes['count'] ) ? $attributes['count'] : 6;
$columns       = isset( $attributes['columns'] ) ? $attributes['columns'] : 3;
$show_excerpt  = isset( $attributes['showExcerpt'] ) ? $attributes['showExcerpt'] : true;
$show_image    = isset( $attributes['showImage'] ) ? $attributes['showImage'] : true;
$show_date     = isset( $attributes['showDate'] ) ? $attributes['showDate'] : true;
$show_location = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true;

// Card style attributes
$card_bg_color      = isset( $attributes['cardBackgroundColor'] ) ? $attributes['cardBackgroundColor'] : '#2e3858';
$card_text_color    = isset( $attributes['cardTextColor'] ) ? $attributes['cardTextColor'] : 'rgba(255, 255, 255, 0.7)';
$card_heading_color = isset( $attributes['cardHeadingColor'] ) ? $attributes['cardHeadingColor'] : '#ffffff';
$card_link_color    = isset( $attributes['cardLinkColor'] ) ? $attributes['cardLinkColor'] : '#ffffff';
$card_padding       = isset( $attributes['cardPadding'] ) ? $attributes['cardPadding'] : 30;
$card_border_radius = isset( $attributes['cardBorderRadius'] ) ? $attributes['cardBorderRadius'] : 0;
$card_box_shadow    = isset( $attributes['cardBoxShadow'] ) ? $attributes['cardBoxShadow'] : false;
$card_border_width  = isset( $attributes['cardBorderWidth'] ) ? $attributes['cardBorderWidth'] : 0;
$card_border_color  = isset( $attributes['cardBorderColor'] ) ? $attributes['cardBorderColor'] : '#cccccc';
$grid_gap           = isset( $attributes['gridGap'] ) ? $attributes['gridGap'] : 30;

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
] );

// Get block wrapper attributes (applies color, spacing, etc. from block supports)
$anchor = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : '';
$wrapper_attrs = [
    'class' => 'snap-events-grid snap-events-columns-' . $columns,
    'style' => 'gap: ' . intval( $grid_gap ) . 'px;',
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
        <article class="snap-event-card" style="<?php echo $card_style; ?> --card-heading-color: <?php echo esc_attr( $card_heading_color ); ?>; --card-link-color: <?php echo esc_attr( $card_link_color ); ?>;">
            
            <?php if ( $show_image && ! empty( $event['thumbnail_url'] ) ) : ?>
                <div class="snap-event-image">
                    <img src="<?php echo esc_url( $event['thumbnail_url'] ); ?>" alt="" role="presentation">
                </div>
            <?php endif; ?>

            <div class="snap-event-content">
                <h3 class="snap-event-title" style="color: var(--card-heading-color, #ffffff);">
                    <a href="<?php echo esc_url( $event['permalink'] ); ?>" style="color: var(--card-heading-color, #ffffff);">
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

                <a href="<?php echo esc_url( $event['permalink'] ); ?>" class="snap-event-link" style="color: var(--card-link-color, #ffffff);" aria-hidden="true" tabindex="-1">
                    <?php esc_html_e( 'View Event', 'snap-events' ); ?>
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<?php
