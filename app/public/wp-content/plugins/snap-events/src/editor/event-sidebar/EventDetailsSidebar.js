/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { TextControl, DatePicker, Popover, Button } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { useState } from '@wordpress/element';

/**
 * Convert Ymd string (20260115) to Date object
 */
function ymdToDate( ymd ) {
    if ( ! ymd || ymd.length !== 8 ) return null;
    const year = ymd.substring( 0, 4 );
    const month = ymd.substring( 4, 6 );
    const day = ymd.substring( 6, 8 );
    return new Date( `${year}-${month}-${day}` );
}

/**
 * Convert Date object to Ymd string (20260115)
 */
function dateToYmd( date ) {
    if ( ! date ) return '';
    const d = new Date( date );
    const year = d.getFullYear();
    const month = String( d.getMonth() + 1 ).padStart( 2, '0' );
    const day = String( d.getDate() ).padStart( 2, '0' );
    return `${year}${month}${day}`;
}

/**
 * Format Ymd for display (20260115 -> January 15, 2026)
 */
function formatYmdForDisplay( ymd ) {
    const date = ymdToDate( ymd );
    if ( ! date ) return '';
    return date.toLocaleDateString( 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    } );
}

/**
 * Date field component with popover picker
 */
function DateField( { label, value, onChange } ) {
    const [ isOpen, setIsOpen ] = useState( false );
    
    return (
        <div className="snap-events-date-field">
            <TextControl
                label={ label }
                value={ formatYmdForDisplay( value ) }
                onFocus={ () => setIsOpen( true ) }
                readOnly
            />
            { isOpen && (
                <Popover onClose={ () => setIsOpen( false ) }>
                    <DatePicker
                        currentDate={ ymdToDate( value ) }
                        onChange={ ( newDate ) => {
                            onChange( dateToYmd( newDate ) );
                            setIsOpen( false );
                        } }
                    />
                </Popover>
            ) }
            { value && (
                <Button
                    size="small"
                    variant="link"
                    onClick={ () => onChange( '' ) }
                >
                    { __( 'Clear', 'snap-events' ) }
                </Button>
            ) }
        </div>
    );
}

/**
 * Event Details Sidebar Panel
 */
export default function EventDetailsSidebar() {
    // Get current post type
    const postType = useSelect(
        ( select ) => select( 'core/editor' ).getCurrentPostType(),
        []
    );

    // Only show for snap_event post type
    if ( postType !== 'snap_event' ) {
        return null;
    }

    // Get and set meta values using useEntityProp
    const [ meta, setMeta ] = useEntityProp( 'postType', 'snap_event', 'meta' );

    const updateMeta = ( key, value ) => {
        setMeta( { ...meta, [ key ]: value } );
    };

    return (
        <PluginDocumentSettingPanel
            name="snap-event-details"
            title={ __( 'Event Details', 'snap-events' ) }
            className="snap-event-details-panel"
        >
            <DateField
                label={ __( 'Start Date', 'snap-events' ) }
                value={ meta.start_date || '' }
                onChange={ ( value ) => updateMeta( 'start_date', value ) }
            />

            <DateField
                label={ __( 'End Date', 'snap-events' ) }
                value={ meta.end_date || '' }
                onChange={ ( value ) => updateMeta( 'end_date', value ) }
            />
            <TextControl
                label={ __( 'Venue', 'snap-events' ) }
                value={ meta.venue || '' }
                onChange={ ( value ) => updateMeta( 'venue', value ) }
            />

            <TextControl
                label={ __( 'City', 'snap-events' ) }
                value={ meta.city || '' }
                onChange={ ( value ) => updateMeta( 'city', value ) }
            />

            <TextControl
                label={ __( 'State', 'snap-events' ) }
                value={ meta.state || '' }
                onChange={ ( value ) => updateMeta( 'state', value ) }
            />

            <TextControl
                label={ __( 'Country', 'snap-events' ) }
                value={ meta.country || '' }
                onChange={ ( value ) => updateMeta( 'country', value ) }
            />
        </PluginDocumentSettingPanel>
    );
}
