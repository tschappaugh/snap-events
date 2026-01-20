<?php
/**
 * Template for displaying single events
 *
 * @package Snap_Events
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="main" class="site-main snap-event-single">
    <?php

    while ( have_posts() ) :
        the_post();
        // Get event meta
        $start_date = get_post_meta( get_the_ID(), 'start_date', true );
        $end_date   = get_post_meta( get_the_ID(), 'end_date', true );
        $venue      = get_post_meta( get_the_ID(), 'venue', true );
        $city       = get_post_meta( get_the_ID(), 'city', true );
        $state      = get_post_meta( get_the_ID(), 'state', true );
        $country    = get_post_meta( get_the_ID(), 'country', true );
        
        // Format dates
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
        
        // Build location string
        $location_parts = array_filter( [ $venue, $city, $state, $country ] );
        $location = implode( ', ', $location_parts );
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <div class="snap-event-meta">
                <?php if ( $formatted_start ) : ?>
                    <p class="snap-event-date">
                        <strong><?php esc_html_e( 'Date:', 'snap-events' ); ?></strong>
                        <?php echo esc_html( $formatted_start ); ?>
                        <?php if ( $formatted_end && $formatted_end !== $formatted_start ) : ?>
                            - <?php echo esc_html( $formatted_end ); ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>
                <?php if ( $location ) : ?>
                    <p class="snap-event-location">
                        <strong><?php esc_html_e( 'Location:', 'snap-events' ); ?></strong>
                        <?php echo esc_html( $location ); ?>
                    </p>
                <?php endif; ?>
            </div>
        </header>
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="snap-event-featured-image">
                <?php the_post_thumbnail( 'large' ); ?>
            </div>
        <?php endif; ?>
        <div class="entry-content">
            <?php
            the_content();
            
            wp_link_pages( [
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'snap-events' ),
                'after'  => '</div>',
            ] );
            ?>
        </div>
    </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();



