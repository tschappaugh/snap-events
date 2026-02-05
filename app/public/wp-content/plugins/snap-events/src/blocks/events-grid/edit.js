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
    ColorPalette,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
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
        cardBackgroundColor,
        cardTextColor,
        cardHeadingColor,
        cardLinkColor,
        cardPadding,
        cardBorderRadius,
        cardBoxShadow,
        cardBorderWidth,
        cardBorderColor,
        gridGap,
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
                    <RangeControl
                        label={ __( 'Columns', 'snap-events' ) }
                        value={ columns }
                        onChange={ ( value ) => setAttributes( { columns: value } ) }
                        min={ 1 }
                        max={ 4 }
                    />
                    <RangeControl
                        label={ __( 'Gap (px)', 'snap-events' ) }
                        value={ gridGap }
                        onChange={ ( value ) => setAttributes( { gridGap: value } ) }
                        min={ 0 }
                        max={ 60 }
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

                <PanelBody title={ __( 'Card Styles', 'snap-events' ) } initialOpen={ false }>
                    <p>{ __( 'Background Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ cardBackgroundColor }
                        onChange={ ( value ) => setAttributes( { cardBackgroundColor: value } ) }
                    />
                    <p>{ __( 'Text Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ cardTextColor }
                        onChange={ ( value ) => setAttributes( { cardTextColor: value } ) }
                    />
                    <p>{ __( 'Heading Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ cardHeadingColor }
                        onChange={ ( value ) => setAttributes( { cardHeadingColor: value } ) }
                    />
                    <p>{ __( 'Link Color', 'snap-events' ) }</p>
                    <ColorPalette
                        colors={ themeColors }
                        value={ cardLinkColor }
                        onChange={ ( value ) => setAttributes( { cardLinkColor: value } ) }
                    />
                    <RangeControl
                        label={ __( 'Padding (px)', 'snap-events' ) }
                        value={ cardPadding }
                        onChange={ ( value ) => setAttributes( { cardPadding: value } ) }
                        min={ 0 }
                        max={ 60 }
                    />
                    <RangeControl
                        label={ __( 'Border Radius (px)', 'snap-events' ) }
                        value={ cardBorderRadius }
                        onChange={ ( value ) => setAttributes( { cardBorderRadius: value } ) }
                        min={ 0 }
                        max={ 30 }
                    />
                    <ToggleControl
                        label={ __( 'Drop Shadow', 'snap-events' ) }
                        checked={ cardBoxShadow }
                        onChange={ ( value ) => setAttributes( { cardBoxShadow: value } ) }
                    />
                    <RangeControl
                        label={ __( 'Border Width (px)', 'snap-events' ) }
                        value={ cardBorderWidth }
                        onChange={ ( value ) => setAttributes( { cardBorderWidth: value } ) }
                        min={ 0 }
                        max={ 5 }
                    />
                    { cardBorderWidth > 0 && (
                        <>
                            <p>{ __( 'Border Color', 'snap-events' ) }</p>
                            <ColorPalette
                                colors={ themeColors }
                                value={ cardBorderColor }
                                onChange={ ( value ) => setAttributes( { cardBorderColor: value } ) }
                            />
                        </>
                    ) }
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
