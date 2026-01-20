/**
 * WordPress dependencies
 */
import { registerPlugin } from '@wordpress/plugins';

/**
 * Internal dependencies
 */
import EventDetailsSidebar from './EventDetailsSidebar';

/**
 * Register the sidebar plugin
 *
 * This adds the Event Details panel to the editor sidebar
 * when editing snap_event posts.
 */
registerPlugin( 'snap-event-details', {
    render: EventDetailsSidebar,
} );
