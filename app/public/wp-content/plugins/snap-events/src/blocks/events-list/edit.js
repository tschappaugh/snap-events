/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
    PanelBody,
    RangeControl,
    ToggleControl,
    ColorPalette,
    SelectControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Edit component for the Events List block
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update attributes.
 * @return {JSX.Element} Block edit component.
 */
export default function Edit( { attributes, setAttributes } ) {
    const {
        count,
        showExcerpt,
        showImage,
        showDate,
        showLocation,
        enableLoadMore,
        enableSort,
        defaultSortOrder,
        textColor,
        headingColor,
        linkColor,
        borderColor,
        borderWidth,
        itemPadding,
    } = attributes;

    const blockProps = useBlockProps();

    const themeColors = useSelect( ( select ) =>
        select( 'core/block-editor' ).getSettings().colors
    );
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

                    <ToggleControl
                        label={ __( 'Enable Load More', 'snap-events' ) }
                        checked={ enableLoadMore }
                        onChange={ ( value ) => setAttributes( { enableLoadMore: value } ) }
                        help={ __( 'Show a button to load additional events', 'snap-events' ) }
                    />

                    <ToggleControl
                        label={ __( 'Enable Sort Toggle', 'snap-events' ) }
                        checked={ enableSort }
                        onChange={ ( value ) => setAttributes( { enableSort: value } ) }
                        help={ __( 'Allow visitors to change sort order', 'snap-events' ) }
                    />

                    { enableSort && (
                        <SelectControl
                            label={ __( 'Default Sort Order', 'snap-events' ) }
                            value={ defaultSortOrder }
                            options={ [
                                { label: __( 'Soonest First (ASC)', 'snap-events' ), value: 'ASC' },
                                { label: __( 'Furthest Out First (DESC)', 'snap-events' ), value: 'DESC' },
                            ] }
                            onChange={ ( value ) => setAttributes( { defaultSortOrder: value } ) }
                        />
                    ) }
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
                <PanelBody title={ __( 'List Styles', 'snap-events' ) } initialOpen={ false }>
                    <p>{ __( 'Text Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ textColor }
                        onChange={ ( value ) => setAttributes( { textColor: value } ) }
                    />
                    <p>{ __( 'Heading Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ headingColor }
                        onChange={ ( value ) => setAttributes( { headingColor: value } ) }
                    />
                    <p>{ __( 'Link Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ linkColor }
                        onChange={ ( value ) => setAttributes( { linkColor: value } ) }
                    />
                    <p>{ __( 'Border Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ borderColor }
                        onChange={ ( value ) => setAttributes( { borderColor: value } ) }
                    />
                    <RangeControl
                        label={ __( 'Border Width (px)', 'snap-events' ) }
                        value={ borderWidth }
                        onChange={ ( value ) => setAttributes( { borderWidth: value } ) }
                        min={ 0 }
                        max={ 5 }
                    />
                    <RangeControl
                        label={ __( 'Item Padding (px)', 'snap-events' ) }
                        value={ itemPadding }
                        onChange={ ( value ) => setAttributes( { itemPadding: value } ) }
                        min={ 0 }
                        max={ 60 }
                    />
                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
                <ServerSideRender
                    block="snap-events/events-list"
                    attributes={ attributes }
                />
            </div>
        </>
    );
}
