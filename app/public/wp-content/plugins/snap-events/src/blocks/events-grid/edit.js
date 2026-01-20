/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ToggleControl,
    TextControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Edit component for the Events Grid block
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {JSX.Element} Block edit component.
 */
export default function Edit( { attributes, setAttributes } ) {
    const {
        count,
        columns,
        showExcerpt,
        showImage,
        showDate,
        showLocation,
        city,
        state,
    } = attributes;

    const blockProps = useBlockProps();
    
    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Display Settings', 'snap-events' ) }>
                    <RangeControl
                        label={ __( 'Number of Events', 'snap-events' ) }
                        value={ count }
                        onChange={ ( value ) => setAttributes( { count: value } ) }
                        min={ 1 }
                        max={ 24 }
                    />
                    <RangeControl
                        label={ __( 'Columns', 'snap-events' ) }
                        value={ columns }
                        onChange={ ( value ) => setAttributes( { columns: value } ) }
                        min={ 1 }
                        max={ 4 }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Content Options', 'snap-events' ) } initialOpen={ false }>
                    <ToggleControl
                        label={ __( 'Show Featured Image', 'snap-events' ) }
                        checked={ showImage }
                        onChange={ ( value ) => setAttributes( { showImage: value } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Date', 'snap-events' ) }
                        checked={ showDate }
                        onChange={ ( value ) => setAttributes( { showDate: value } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Location', 'snap-events' ) }
                        checked={ showLocation }
                        onChange={ ( value ) => setAttributes( { showLocation: value } ) }
                    />
                    <ToggleControl
                        label={ __( 'Show Excerpt', 'snap-events' ) }
                        checked={ showExcerpt }
                        onChange={ ( value ) => setAttributes( { showExcerpt: value } ) }
                    />
                </PanelBody>

                <PanelBody title={ __( 'Filter Events', 'snap-events' ) } initialOpen={ false }>
                    <TextControl
                        label={ __( 'City', 'snap-events' ) }
                        value={ city }
                        onChange={ ( value ) => setAttributes( { city: value } ) }
                        help={ __( 'Filter events by city name', 'snap-events' ) }
                    />
                    <TextControl
                        label={ __( 'State', 'snap-events' ) }
                        value={ state }
                        onChange={ ( value ) => setAttributes( { state: value } ) }
                        help={ __( 'Filter events by state/province', 'snap-events' ) }
                    />
                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                <ServerSideRender
                    block="snap-events/events-grid"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
