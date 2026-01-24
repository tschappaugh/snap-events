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
$city          = isset( $attributes['city'] ) ? $attributes['city'] : '';
$state         = isset( $attributes['state'] ) ? $attributes['state'] : '';

// Query events using our query class
$events = Snap_Events_Query::get_events( [
    'posts_per_page' => $count,
    'city'           => $city,
    'state'          => $state,
] );

// Get block wrapper attributes (applies color, spacing, etc. from block supports)
$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'snap-events-grid snap-events-columns-' . $columns,
] );

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
        <article class="snap-event-card">
            <?php if ( $show_image && ! empty( $event['thumbnail_url'] ) ) : ?>
                <div class="snap-event-image">
                    <a href="<?php echo esc_url( $event['permalink'] ); ?>">
                        <img src="<?php echo esc_url( $event['thumbnail_url'] ); ?>" alt="<?php echo esc_attr( $event['title'] ); ?>">
                    </a>
                </div>
            <?php endif; ?>

            <div class="snap-event-content">
                <h3 class="snap-event-title">
                    <a href="<?php echo esc_url( $event['permalink'] ); ?>">
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

                <a href="<?php echo esc_url( $event['permalink'] ); ?>" class="snap-event-link">
                    <?php esc_html_e( 'View Event', 'snap-events' ); ?>
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>
<?php
