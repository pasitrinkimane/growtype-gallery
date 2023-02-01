/**
 * External dependencies
 */
import classnames from 'classnames';
import {find} from 'lodash';

/**
 * WordPress dependencies
 */
import {compose} from '@wordpress/compose';
import {
    BaseControl,
    PanelBody,
    SelectControl,
    ToggleControl,
    RangeControl,
    Spinner,
    TextControl,
    ColorPicker,
    __experimentalNumberControl as NumberControl
} from '@wordpress/components';

import {
    store as blockEditorStore,
    MediaPlaceholder,
    InspectorControls,
    useBlockProps,
    BlockControls,
    MediaReplaceFlow, InspectorAdvancedControls,
} from '@wordpress/block-editor';

import {Platform, useEffect, useMemo} from '@wordpress/element';
import {__, _x, sprintf} from '@wordpress/i18n';
import {useSelect, useDispatch} from '@wordpress/data';
import {withViewportMatch} from '@wordpress/viewport';
import {View} from '@wordpress/primitives';
import {createBlock} from '@wordpress/blocks';
import {createBlobURL} from '@wordpress/blob';
import {store as noticesStore} from '@wordpress/notices';

import blockJson from './block.json';

/**
 * Internal dependencies
 */
import {sharedIcon} from './shared-icon';
import {defaultColumnsNumber, pickRelevantMediaFiles} from './shared';
import {getHrefAndDestination} from './utils';
import {
    getUpdatedLinkTargetSettings,
    getImageSizeAttributes,
} from './image/utils';
import Gallery from './gallery';
import {
    LINK_DESTINATION_ATTACHMENT,
    LINK_DESTINATION_LIGHTBOX,
    LINK_DESTINATION_MEDIA,
    LINK_DESTINATION_NONE,
    LINK_DESTINATION_POPUP,
} from './constants';

import useImageSizes from './use-image-sizes';
import useGetNewImages from './use-get-new-images';
import useGetMedia from './use-get-media';
import GapStyles from './gap-styles';

const MAX_COLUMNS = 8;
const linkOptions = [
    {value: LINK_DESTINATION_ATTACHMENT, label: __('Attachment Page')},
    {value: LINK_DESTINATION_MEDIA, label: __('Media File')},
    {value: LINK_DESTINATION_NONE, label: __('None')},
    {value: LINK_DESTINATION_LIGHTBOX, label: __('Lightbox')},
    {value: LINK_DESTINATION_POPUP, label: __('Popup')},
];

const ALLOWED_MEDIA_TYPES = ['image'];

const PLACEHOLDER_TEXT = Platform.isNative
    ? __('ADD MEDIA')
    : __('Drag images, upload new ones or select files from your library.');

const MOBILE_CONTROL_PROPS_RANGE_CONTROL = Platform.isNative
    ? {type: 'stepper'}
    : {};

function GalleryEdit(props) {
    const {
        setAttributes,
        attributes,
        className,
        clientId,
        isSelected,
        insertBlocksAfter,
    } = props;

    const {
        columns,
        imageCrop,
        linkTarget,
        linkTo,
        sizeSlug,
        hasOverlay,
        overlayColor,
        loaderActive,
    } = attributes;

    const {
        __unstableMarkNextChangeAsNotPersistent,
        replaceInnerBlocks,
        updateBlockAttributes,
        selectBlock,
        clearSelectedBlock,
    } = useDispatch(blockEditorStore);

    const {createSuccessNotice, createErrorNotice} =
        useDispatch(noticesStore);

    const {getBlock, getSettings, preferredStyle} = useSelect((select) => {
        const settings = select(blockEditorStore).getSettings();
        const preferredStyleVariations =
            settings.__experimentalPreferredStyleVariations;
        return {
            getBlock: select(blockEditorStore).getBlock,
            getSettings: select(blockEditorStore).getSettings,
            preferredStyle: preferredStyleVariations?.value?.['core/image'],
        };
    }, []);

    const innerBlockImages = useSelect(
        (select) => {
            return select(blockEditorStore).getBlock(clientId)?.innerBlocks;
        },
        [clientId]
    );

    const wasBlockJustInserted = useSelect(
        (select) => {
            return select(blockEditorStore).wasBlockJustInserted(
                clientId,
                'inserter_menu'
            );
        },
        [clientId]
    );

    const images = useMemo(
        () =>
            innerBlockImages?.map((block) => ({
                clientId: block.clientId,
                id: block.attributes.id,
                url: block.attributes.url,
                attributes: block.attributes,
                fromSavedContent: Boolean(block.originalContent),
            })),
        [innerBlockImages]
    );

    const imageData = useGetMedia(innerBlockImages);

    const newImages = useGetNewImages(images, imageData);

    useEffect(() => {
        newImages?.forEach((newImage) => {
            // Update the images data without creating new undo levels.
            __unstableMarkNextChangeAsNotPersistent();
            updateBlockAttributes(newImage.clientId, {
                ...buildImageAttributes(newImage.attributes),
                id: newImage.id,
                align: undefined,
            });
        });
        if (newImages?.length > 0) {
            clearSelectedBlock();
        }
    }, [newImages]);

    const imageSizeOptions = useImageSizes(
        imageData,
        isSelected,
        getSettings
    );

    /**
     * Determines the image attributes that should be applied to an image block
     * after the gallery updates.
     *
     * The gallery will receive the full collection of images when a new image
     * is added. As a result we need to reapply the image's original settings if
     * it already existed in the gallery. If the image is in fact new, we need
     * to apply the gallery's current settings to the image.
     *
     * @param {Object} imageAttributes Media object for the actual image.
     * @return {Object}                Attributes to set on the new image block.
     */
    function buildImageAttributes(imageAttributes) {
        const image = imageAttributes.id
            ? find(imageData, {id: imageAttributes.id})
            : null;

        let newClassName;
        if (imageAttributes.className && imageAttributes.className !== '') {
            newClassName = imageAttributes.className;
        } else {
            newClassName = preferredStyle
                ? `is-style-${preferredStyle}`
                : undefined;
        }

        let newLinkTarget;
        if (imageAttributes.linkTarget || imageAttributes.rel) {
            // When transformed from image blocks, the link destination and rel attributes are inherited.
            newLinkTarget = {
                linkTarget: imageAttributes.linkTarget,
                rel: imageAttributes.rel,
            };
        } else {
            // When an image is added, update the link destination and rel attributes according to the gallery settings
            newLinkTarget = getUpdatedLinkTargetSettings(
                linkTarget,
                attributes
            );
        }

        return {
            ...pickRelevantMediaFiles(image, sizeSlug),
            ...getHrefAndDestination(
                image,
                linkTo,
                imageAttributes?.linkDestination
            ),
            ...newLinkTarget,
            className: newClassName,
            sizeSlug,
            caption: imageAttributes.caption || image.caption?.raw,
            alt: imageAttributes.alt || image.alt_text,
        };
    }

    function isValidFileType(file) {
        return (
            ALLOWED_MEDIA_TYPES.some(
                (mediaType) => file.type?.indexOf(mediaType) === 0
            ) || file.url?.indexOf('blob:') === 0
        );
    }

    function updateImages(selectedImages) {
        const newFileUploads =
            Object.prototype.toString.call(selectedImages) ===
            '[object FileList]';

        const imageArray = newFileUploads
            ? Array.from(selectedImages).map((file) => {
                if (!file.url) {
                    return pickRelevantMediaFiles({
                        url: createBlobURL(file),
                    });
                }

                return file;
            })
            : selectedImages;

        if (!imageArray.every(isValidFileType)) {
            createErrorNotice(
                __(
                    'If uploading to a gallery all files need to be image formats'
                ),
                {id: 'gallery-upload-invalid-file', type: 'snackbar'}
            );
        }

        const processedImages = imageArray
            .filter((file) => file.url || isValidFileType(file))
            .map((file) => {
                if (!file.url) {
                    return pickRelevantMediaFiles({
                        url: createBlobURL(file),
                    });
                }

                return file;
            });

        // Because we are reusing existing innerImage blocks any reordering
        // done in the media library will be lost so we need to reapply that ordering
        // once the new image blocks are merged in with existing.
        const newOrderMap = processedImages.reduce(
            (result, image, index) => (
                (result[image.id] = index), result
            ),
            {}
        );

        const existingImageBlocks = !newFileUploads
            ? innerBlockImages.filter((block) =>
                processedImages.find(
                    (img) => img.id === block.attributes.id
                )
            )
            : innerBlockImages;

        const newImageList = processedImages.filter(
            (img) =>
                !existingImageBlocks.find(
                    (existingImg) => img.id === existingImg.attributes.id
                )
        );

        const newBlocks = newImageList.map((image) => {
            return createBlock('core/image', {
                id: image.id,
                url: image.url,
                caption: image.caption,
                alt: image.alt,
            });
        });

        if (newBlocks?.length > 0) {
            selectBlock(newBlocks[0].clientId);
        }

        replaceInnerBlocks(
            clientId,
            existingImageBlocks
                .concat(newBlocks)
                .sort(
                    (a, b) =>
                        newOrderMap[a.attributes.id] -
                        newOrderMap[b.attributes.id]
                )
        );
    }

    function onUploadError(message) {
        createErrorNotice(message, {type: 'snackbar'});
    }

    function setLinkTo(value) {
        setAttributes({linkTo: value});
        const changedAttributes = {};
        const blocks = [];
        getBlock(clientId).innerBlocks.forEach((block) => {
            blocks.push(block.clientId);
            const image = block.attributes.id
                ? find(imageData, {id: block.attributes.id})
                : null;
            changedAttributes[block.clientId] = getHrefAndDestination(
                image,
                value
            );
        });
        updateBlockAttributes(blocks, changedAttributes, true);
        const linkToText = [...linkOptions].find(
            (linkType) => linkType.value === value
        );

        createSuccessNotice(
            sprintf(
                /* translators: %s: image size settings */
                __('All gallery image links updated to: %s'),
                linkToText.label
            ),
            {
                id: 'gallery-attributes-linkTo',
                type: 'snackbar',
            }
        );
    }

    function setColumnsNumber(value) {
        setAttributes({columns: value});
    }

    function toggleImageCrop() {
        setAttributes({imageCrop: !imageCrop});
    }

    function getImageCropHelp(checked) {
        return checked
            ? __('Thumbnails are cropped to align.')
            : __('Thumbnails are not cropped.');
    }

    function toggleOpenInNewTab(openInNewTab) {
        const newLinkTarget = openInNewTab ? '_blank' : undefined;
        setAttributes({linkTarget: newLinkTarget});
        const changedAttributes = {};
        const blocks = [];
        getBlock(clientId).innerBlocks.forEach((block) => {
            blocks.push(block.clientId);
            changedAttributes[block.clientId] = getUpdatedLinkTargetSettings(
                newLinkTarget,
                block.attributes
            );
        });
        updateBlockAttributes(blocks, changedAttributes, true);
        const noticeText = openInNewTab
            ? __('All gallery images updated to open in new tab')
            : __('All gallery images updated to not open in new tab');
        createSuccessNotice(noticeText, {
            id: 'gallery-attributes-openInNewTab',
            type: 'snackbar',
        });
    }

    function toggleOverlay(overlay) {
        setAttributes({hasOverlay: overlay});
    }

    function updateImagesSize(newSizeSlug) {
        setAttributes({sizeSlug: newSizeSlug});
        const changedAttributes = {};
        const blocks = [];
        getBlock(clientId).innerBlocks.forEach((block) => {
            blocks.push(block.clientId);
            const image = block.attributes.id
                ? find(imageData, {id: block.attributes.id})
                : null;
            changedAttributes[block.clientId] = getImageSizeAttributes(
                image,
                newSizeSlug
            );
        });
        updateBlockAttributes(blocks, changedAttributes, true);
        const imageSize = imageSizeOptions.find(
            (size) => size.value === newSizeSlug
        );

        createSuccessNotice(
            sprintf(
                /* translators: %s: image size settings */
                __('All gallery image sizes updated to: %s'),
                imageSize.label
            ),
            {
                id: 'gallery-attributes-sizeSlug',
                type: 'snackbar',
            }
        );
    }

    function setWatermark(value) {
        setAttributes({watermark: value});
    }

    function setOverlayColor(value) {
        setAttributes({overlayColor: value});
    }

    function setGroupName(value) {
        setAttributes({groupName: value});
    }

    function toggleLoader(value) {
        setAttributes({loaderActive: value});
    }

    useEffect(() => {
        // linkTo attribute must be saved so blocks don't break when changing image_default_link_type in options.php.
        if (!linkTo) {
            __unstableMarkNextChangeAsNotPersistent();
            setAttributes({
                linkTo:
                    window?.wp?.media?.view?.settings?.defaultProps?.link ||
                    LINK_DESTINATION_LIGHTBOX,
            });
        }
    }, [linkTo]);

    const hasImages = !!images.length;

    const hasImageIds = hasImages && images.some((image) => !!image.id);
    const imagesUploading = images.some((img) =>
        !Platform.isNative
            ? !img.id && img.url?.indexOf('blob:') === 0
            : img.url?.indexOf('file:') === 0
    );

    // MediaPlaceholder props are different between web and native hence, we provide a platform-specific set.
    const mediaPlaceholderProps = Platform.select({
        web: {
            addToGallery: false,
            disableMediaButtons: imagesUploading,
            value: {},
        },
        native: {
            addToGallery: hasImageIds,
            isAppender: hasImages,
            disableMediaButtons:
                (hasImages && !isSelected) || imagesUploading,
            value: hasImageIds ? images : {},
            autoOpenMediaUpload:
                !hasImages && isSelected && wasBlockJustInserted,
        },
    });

    const mediaPlaceholder = (
        <MediaPlaceholder
            handleUpload={false}
            icon={sharedIcon}
            labels={{
                title: __('Gallery'),
                instructions: PLACEHOLDER_TEXT,
            }}
            onSelect={updateImages}
            accept="image/*"
            allowedTypes={ALLOWED_MEDIA_TYPES}
            multiple
            onError={onUploadError}
            {...mediaPlaceholderProps}
        />
    );

    const blockProps = useBlockProps({
        className: classnames(className, 'has-nested-images'),
    });

    if (!hasImages) {
        return <View {...blockProps}>{mediaPlaceholder}</View>;
    }

    const hasLinkTo = linkTo && linkTo !== 'none';

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Settings')}>
                    <SelectControl
                        label={__('Link to')}
                        value={linkTo}
                        onChange={setLinkTo}
                        options={linkOptions}
                        hideCancelButton={true}
                    />

                    {(linkTo === LINK_DESTINATION_MEDIA || linkTo === LINK_DESTINATION_ATTACHMENT) && (
                        <ToggleControl
                            label={__('Open in new tab', 'growtype-gallery')}
                            checked={linkTarget === '_blank'}
                            onChange={toggleOpenInNewTab}
                        />
                    )}

                    {linkTo === 'lightbox' && (
                        <TextControl
                            label={__('Group name', 'growtype-gallery')}
                            help={__('Images are related by group name.', 'growtype-gallery')}
                            onChange={setGroupName}
                            value={attributes.groupName}
                        />
                    )}

                    {imageSizeOptions?.length > 0 && (linkTo === LINK_DESTINATION_MEDIA || linkTo === LINK_DESTINATION_ATTACHMENT || linkTo === LINK_DESTINATION_LIGHTBOX) && (
                        <SelectControl
                            label={__('Main image size')}
                            value={sizeSlug}
                            options={imageSizeOptions}
                            onChange={updateImagesSize}
                            hideCancelButton={true}
                        />
                    )}

                    {Platform.isWeb && !imageSizeOptions && hasImageIds && (
                        <BaseControl className={'gallery-image-sizes'}>
                            <BaseControl.VisualLabel>
                                {__('Image size')}
                            </BaseControl.VisualLabel>
                            <View className={'gallery-image-sizes__loading'}>
                                <Spinner/>
                                {__('Loading options…')}
                            </View>
                        </BaseControl>
                    )}
                </PanelBody>
                <PanelBody
                    title={__('Preview', 'growtype-gallery')}
                >
                    <SelectControl
                        label={__('Image preview size', 'growtype-gallery')}
                        value={attributes.imagePreviewSize}
                        options={blockJson.attributes.imagePreviewSize.options}
                        onChange={(value) => setAttributes({imagePreviewSize: value})}
                        hideCancelButton={true}
                    />
                    <SelectControl
                        label={__('Image preview format', 'growtype-gallery')}
                        value={attributes.imagePreviewFormat}
                        options={blockJson.attributes.imagePreviewFormat.options}
                        onChange={(value) => setAttributes({imagePreviewFormat: value})}
                        hideCancelButton={true}
                    />
                    <SelectControl
                        label={__('Grid style', 'growtype-gallery')}
                        value={attributes.previewGridStyle}
                        options={blockJson.attributes.previewGridStyle.options}
                        onChange={(value) => setAttributes({previewGridStyle: value})}
                        hideCancelButton={true}
                    />
                    {images.length > 1 && (
                        <RangeControl
                            label={__('Columns', 'growtype-gallery')}
                            value={
                                columns
                                    ? columns
                                    : defaultColumnsNumber(images.length)
                            }
                            onChange={setColumnsNumber}
                            min={blockJson.attributes.columns.minimum}
                            max={Math.min(MAX_COLUMNS, images.length)}
                            {...MOBILE_CONTROL_PROPS_RANGE_CONTROL}
                            required
                        />
                    )}
                    {images.length > 1 && (
                        <RangeControl
                            label={__('Columns - mobile', 'growtype-gallery')}
                            value={attributes.columnsMobile}
                            onChange={(value) => setAttributes({columnsMobile: value})}
                            min={blockJson.attributes.columnsMobile.minimum}
                            max={Math.min(MAX_COLUMNS, images.length)}
                            {...MOBILE_CONTROL_PROPS_RANGE_CONTROL}
                            required
                        />
                    )}
                    <RangeControl
                        label={__('Image height (%)', 'growtype-gallery')}
                        value={attributes.imageHeight}
                        onChange={(value) => setAttributes({imageHeight: value})}
                        min={blockJson.attributes.imageHeight.minimum}
                        max={blockJson.attributes.imageHeight.maximum}
                        {...MOBILE_CONTROL_PROPS_RANGE_CONTROL}
                        required
                    />
                    <RangeControl
                        label={__('Image border radius (px)', 'growtype-gallery')}
                        value={attributes.imageBorderRadius}
                        onChange={(value) => setAttributes({imageBorderRadius: value})}
                        min={blockJson.attributes.imageBorderRadius.minimum}
                        max={blockJson.attributes.imageBorderRadius.maximum}
                        {...MOBILE_CONTROL_PROPS_RANGE_CONTROL}
                        required
                    />
                    <RangeControl
                        label={__('Image padding (px)', 'growtype-gallery')}
                        value={attributes.imagePadding}
                        onChange={(value) => setAttributes({imagePadding: value})}
                        min={blockJson.attributes.imagePadding.minimum}
                        max={blockJson.attributes.imagePadding.maximum}
                        {...MOBILE_CONTROL_PROPS_RANGE_CONTROL}
                        required
                    />
                    <ToggleControl
                        label={__('Crop images')}
                        checked={!imageCrop}
                        onChange={toggleImageCrop}
                        help={getImageCropHelp}
                    />
                    <ToggleControl
                        label={__('Has overlay')}
                        checked={hasOverlay}
                        onChange={toggleOverlay}
                    />
                    {hasOverlay && (
                        <ColorPicker
                            label={__('Overlay color')}
                            color={overlayColor}
                            onChange={setOverlayColor}
                            enableAlpha="true"
                            defaultValue=""
                            copyFormat="rgb"
                        />
                    )}
                    <TextControl
                        label={__('Watermark', 'growtype-gallery')}
                        help={__('Visible on every image.', 'growtype-gallery')}
                        onChange={setWatermark}
                        value={attributes.watermark}
                    />
                </PanelBody>
                <PanelBody
                    title={__('Loading', 'growtype-gallery')}
                >
                    <ToggleControl
                        label={__('Loader active')}
                        checked={loaderActive}
                        onChange={toggleLoader}
                    />
                    <SelectControl
                        label={__('Loader type', 'growtype-gallery')}
                        value={attributes.loaderType}
                        options={blockJson.attributes.loaderType.options}
                        onChange={(value) => setAttributes({loaderType: value})}
                        hideCancelButton={true}
                    />
                    <SelectControl
                        label={__('Animation on scroll effect', 'growtype-gallery')}
                        value={attributes.animationOnScrollEffect}
                        options={blockJson.attributes.animationOnScrollEffect.options}
                        onChange={(value) => setAttributes({animationOnScrollEffect: value})}
                        hideCancelButton={true}
                    />
                </PanelBody>
            </InspectorControls>

            <InspectorAdvancedControls>
                <TextControl
                    label={__('Gallery ID', 'growtype-gallery')}
                    value={attributes.galleryId}
                    onChange={galleryId => setAttributes({galleryId})}
                />
            </InspectorAdvancedControls>

            <BlockControls group="other">
                <MediaReplaceFlow
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    accept="image/*"
                    handleUpload={false}
                    onSelect={updateImages}
                    name={__('Add')}
                    multiple={true}
                    mediaIds={images
                        .filter((image) => image.id)
                        .map((image) => image.id)}
                    addToGallery={hasImageIds}
                />
            </BlockControls>
            {Platform.isWeb && (
                <GapStyles
                    blockGap={attributes.style?.spacing?.blockGap}
                    clientId={clientId}
                />
            )}
            <Gallery
                {...props}
                images={images}
                mediaPlaceholder={
                    !hasImages || Platform.isNative
                        ? mediaPlaceholder
                        : undefined
                }
                blockProps={blockProps}
                insertBlocksAfter={insertBlocksAfter}
            />
        </>
    );
}

export default (
    GalleryEdit
);
