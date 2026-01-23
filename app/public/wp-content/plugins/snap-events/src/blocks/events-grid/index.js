/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';
import save from './save';

/**
 * Import editor sidebar plugin
 */
import '../../editor/event-sidebar';

/**
 * Register the block
 */
registerBlockType( metadata.name, {
    /**
     * The edit function describes the structure of the block in the editor.
     */
    edit: Edit,

    /**
     * The save function returns null because this is a dynamic block
     * that renders on the server via PHP.
     */
    save,
} );
