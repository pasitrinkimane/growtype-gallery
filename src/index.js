/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor. All other files
 * get applied to the editor only.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './../resources/styles/growtype-gallery.scss';
import './editor.scss';

/**
 * WordPress dependencies
 */
import { SVG, Path } from '@wordpress/primitives';

const icon = (
    <SVG width="35" height="35" viewBox="0 0 35 35" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0.579738 26.2702H9.31891C9.31891 28.1128 14.952 28.8498 14.952 24.5329V23.2168C13.5832 25.0067 12.2144 25.3752 10.0033 25.3752C3.15937 25.3752 -0.052009 20.9003 0.000636649 14.8987C0.0532823 8.89715 3.31731 4.47492 9.74008 4.52756C11.688 4.52756 13.7938 5.10667 15.2152 6.84397L15.3732 4.84344H24.007V24.5329C24.007 37.6417 0.579738 37.0626 0.579738 26.2702ZM9.21362 15.2146C9.21362 19.0578 14.8467 19.0578 14.8467 15.162C14.8467 11.2662 9.21362 11.2136 9.21362 15.2146Z" fill="#315344"/>
    </SVG>
);

/**
 * Internal dependencies
 */

import initBlock from './utils/init-block';
import edit from './edit-wrapper';
import save from './save';
import metadata from './block.json';
import deprecated from './deprecated';
import transforms from './transforms';

const {name} = metadata;
export {metadata, name};

export const settings = {
    icon,
    example: {
        attributes: {
            columns: 2,
        },
        innerBlocks: [
            {
                name: 'core/image',
                attributes: {
                    url: 'https://s.w.org/images/core/5.3/Glacial_lakes%2C_Bhutan.jpg',
                },
            },
            {
                name: 'core/image',
                attributes: {
                    url: 'https://s.w.org/images/core/5.3/Sediment_off_the_Yucatan_Peninsula.jpg',
                },
            },
        ],
    },
    transforms,
    edit,
    save,
    deprecated,
};

initBlock({name, metadata, settings})
