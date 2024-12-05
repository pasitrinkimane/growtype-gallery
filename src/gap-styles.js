/**
 * WordPress dependencies
 */
import { createContext, useContext, createPortal } from '@wordpress/element';
import { __experimentalGetGapCSSValue as getGapCSSValue } from '@wordpress/blocks';

// Create a context for BlockContextProvider to use
const BlockContext = createContext();

export default function GapStyles({ blockGap, clientId }) {
	// Use BlockContext to access the BlockContextProvider
	const blockContext = useContext(BlockContext);
	const fallbackValue = `var(--wp--style--gallery-gap-default, var(--gallery-block--gutter-size, var(--wp--style--block-gap, 0.5em)))`;
	let gapValue = fallbackValue;
	let column = fallbackValue;
	let row;

	if (!!blockGap) {
		row =
			typeof blockGap === 'string'
				? getGapCSSValue(blockGap)
				: getGapCSSValue(blockGap?.top) || fallbackValue;
		column =
			typeof blockGap === 'string'
				? getGapCSSValue(blockGap)
				: getGapCSSValue(blockGap?.left) || fallbackValue;
		gapValue = row === column ? row : `${row} ${column}`;
	}

	const gap = `#block-${clientId} {
		--wp--style--unstable-gallery-gap: ${column === '0' ? '0px' : column};
		gap: ${gapValue}
	}`;

	const GapStyle = () => {
		return <style>{gap}</style>;
	};

	return gap && blockContext && blockContext.BlockList && blockContext.BlockList.BlockContextProvider
		? createPortal(<GapStyle />, blockContext.BlockList.BlockContextProvider)
		: null;
}
