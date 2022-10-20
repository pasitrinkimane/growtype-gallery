<?php

/**
 *
 */
class Growtype_Gallery_Block
{
    function __construct()
    {
        add_action('init', array ($this, 'create_block_growtype_gallery_block_init'));

        add_filter('render_block', array ($this, 'growtype_gallery_render_block'), 10, 2);
        add_filter('render_block_data', array ($this, 'growtype_gallery_render_block_data'));
    }

    function create_block_growtype_gallery_block_init()
    {
        register_block_type_from_metadata(GROWTYPE_GALLERY_PATH . 'build', [
            'render_callback' => array ($this, 'render_callback_growtype_gallery'),
        ]);
    }

    // Optional: Moved render callback to separate function to keep logic clear
    function render_callback_growtype_gallery($attributes, $content)
    {
        $gap = _wp_array_get($attributes, array ('style', 'spacing', 'blockGap'));

        // Skip if gap value contains unsupported characters.
        // Regex for CSS value borrowed from `safecss_filter_attr`, and used here
        // because we only want to match against the value, not the CSS attribute.
        if (is_array($gap)) {
            foreach ($gap as $key => $value) {
                // Make sure $value is a string to avoid PHP 8.1 deprecation error in preg_match() when the value is null.
                $value = is_string($value) ? $value : '';
                $value = $value && preg_match('%[\\\(&=}]|/\*%', $value) ? null : $value;

                // Get spacing CSS variable from preset value if provided.
                if (is_string($value) && str_contains($value, 'var:preset|spacing|')) {
                    $index_to_splice = strrpos($value, '|') + 1;
                    $slug = _wp_to_kebab_case(substr($value, $index_to_splice));
                    $value = "var(--wp--preset--spacing--$slug)";
                }

                $gap[$key] = $value;
            }
        } else {
            // Make sure $gap is a string to avoid PHP 8.1 deprecation error in preg_match() when the value is null.
            $gap = is_string($gap) ? $gap : '';
            $gap = $gap && preg_match('%[\\\(&=}]|/\*%', $gap) ? null : $gap;

            // Get spacing CSS variable from preset value if provided.
            if (is_string($gap) && str_contains($gap, 'var:preset|spacing|')) {
                $index_to_splice = strrpos($gap, '|') + 1;
                $slug = _wp_to_kebab_case(substr($gap, $index_to_splice));
                $gap = "var(--wp--preset--spacing--$slug)";
            }
        }

        $class = wp_unique_id('wp-block-gallery-');
        $content = preg_replace(
            '/' . preg_quote('class="', '/') . '/',
            'class="' . $class . ' ',
            $content,
            1
        );

        // --gallery-block--gutter-size is deprecated. --wp--style--gallery-gap-default should be used by themes that want to set a default
        // gap on the gallery.
        $fallback_gap = 'var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) )';
        $gap_value = $gap ? $gap : $fallback_gap;
        $gap_column = $gap_value;

        if (is_array($gap_value)) {
            $gap_row = isset($gap_value['top']) ? $gap_value['top'] : $fallback_gap;
            $gap_column = isset($gap_value['left']) ? $gap_value['left'] : $fallback_gap;
            $gap_value = $gap_row === $gap_column ? $gap_row : $gap_row . ' ' . $gap_column;
        }

        // The unstable gallery gap calculation requires a real value (such as `0px`) and not `0`.
        if ('0' === $gap_column) {
            $gap_column = '0px';
        }

        // Set the CSS variable to the column value, and the `gap` property to the combined gap value.
        $style = '.wp-block-gallery.' . $class . '{ --wp--style--unstable-gallery-gap: ' . $gap_column . '; gap: ' . $gap_value . '}';

        wp_enqueue_block_support_styles($style, 11);

        return $content;
    }

    function growtype_gallery_render_block($block_content, $parsed_block)
    {
        if ('growtype/gallery' === $parsed_block['blockName'] && !is_admin()) {
            if (!empty($block_content)) {
                $doc = new DOMDocument();
                libxml_use_internal_errors(true);
                $doc->loadHTML($block_content);
                libxml_clear_errors();
                $xpath = new DOMXPath($doc);
                $parent_figure_class = $xpath->evaluate("string(//figure/@class)");

                $images = [];
                foreach ($parsed_block['innerBlocks'] as $key => $inner_block) {
                    if ('core/image' === $inner_block['blockName']) {
                        $link_to = $parsed_block["attrs"]["linkTo"];
                        $preview_style = 'basic';
                        $has_overlay = $parsed_block["attrs"]['hasOverlay'] ?? '';
                        $overlay_color = isset($parsed_block["attrs"]['overlayColor']) ? $parsed_block["attrs"]['overlayColor'] : '';
                        $watermark = $parsed_block["attrs"]['watermark'] ?? '';
                        $image_border_radius = $parsed_block["attrs"]['imageBorderRadius'] ?? '0px';
                        $group_name = $parsed_block["attrs"]['groupName'] ?? '';
                        $image_height = $parsed_block["attrs"]['imageHeight'] ?? '100%';
                        $original_img_html = $parsed_block['innerBlocks'][$key]['innerContent'][0];
                        $loader_active = $parsed_block["attrs"]['loaderActive'] ?? false;
                        $loader_type = $parsed_block["attrs"]['loaderType'] ?? 'basic';

                        /**
                         * get original html parts
                         */
                        $doc = new DOMDocument();
                        $doc->loadHTML($original_img_html);
                        $xpath = new DOMXPath($doc);
                        $original_figure_class = $xpath->evaluate("string(//figure/@class)");
                        $original_img_src = $xpath->evaluate("string(//img/@src)");
                        $original_img_class = $xpath->evaluate("string(//img/@class)");
                        $original_img_alt = $xpath->evaluate("string(//img/@alt)");
                        $original_img_caption = $xpath->evaluate("string(//img/@caption)");
                        $original_img_id = str_replace('wp-image-', '', $original_img_class);

                        $original_image_url = wp_get_attachment_url($original_img_id);

                        $template_path = 'preview.' . $preview_style . '.index';

                        $new_img_html = growtype_gallery_include_view($template_path,
                            [
                                'id' => $inner_block['attrs']['id'],
                                'preview_src' => $original_img_src,
                                'src' => $original_image_url,
                                'parent_class' => $original_figure_class,
                                'child_class' => $original_img_class,
                                'alt' => $original_img_alt,
                                'caption' => $original_img_caption,
                                'overlay' => $has_overlay,
                                'overlay_color' => $overlay_color,
                                'overlay_icon' => $overlay_icon ?? false,
                                'watermark' => $watermark,
                                'link_to' => $link_to,
                                'group_name' => $group_name,
                                'image_height' => $image_height,
                                'image_border_radius' => $image_border_radius,
                                'loader_active' => $loader_active,
                                'loader_type' => $loader_type,
                            ]
                        );

                        array_push($images, $new_img_html);
                    }
                }

                $slider_active = $parsed_block["attrs"]['sliderActive'] ?? false;
                $slider_slides_amount_to_show = $parsed_block["attrs"]['sliderSlidesAmountToShow'] ?? '4';
                $loader_active = $parsed_block["attrs"]['loaderActive'] ?? false;
                $loader_type = $parsed_block["attrs"]['loaderType'] ?? '';
                $slider_overflow = $parsed_block["attrs"]['sliderOverflow'] ?? 'hidden';
                $slider_infinite = isset($parsed_block["attrs"]['sliderInfinite']) && $parsed_block["attrs"]['sliderInfinite'] ? 'true' : 'false';
                $slider_center_mode = isset($parsed_block["attrs"]['sliderCenterMode']) && $parsed_block["attrs"]['sliderCenterMode'] ? 'true' : 'false';

                $images = implode('', $images);

                ob_start();

                ?>
                <div class="growtype-gallery-wrapper <?php echo $slider_active ? 'slider-active' : '' ?> <?php echo $loader_active ? 'loader-active' : '' ?>"
                     data-slides-amount-to-show="<?php echo $slider_slides_amount_to_show ?>"
                     data-slider-overflow="<?php echo $slider_overflow ?>"
                     data-slider-infinite="<?php echo $slider_infinite ?>"
                     data-slider-center-mode="<?php echo $slider_center_mode ?>"
                     data-loader-type="<?php echo $loader_type ?>"
                >
                    <figure class="<?php echo $parent_figure_class ?>">
                        <?php echo $images ?>
                    </figure>
                </div>
                <?php

                $block_content = ob_get_clean();
            }
        }

        return $block_content;
    }

    function growtype_gallery_render_block_data($parsed_block)
    {
        if ('growtype/gallery' === $parsed_block['blockName']) {
            foreach ($parsed_block['innerBlocks'] as $key => $inner_block) {
                if ('core/image' === $inner_block['blockName']) {
                    if (!isset($parsed_block['innerBlocks'][$key]['attrs']['data-id']) && isset($inner_block['attrs']['id'])) {
                        $parsed_block['innerBlocks'][$key]['attrs']['data-id'] = esc_attr($inner_block['attrs']['id']);
                    }
                }
            }
        }

        return $parsed_block;
    }
}

