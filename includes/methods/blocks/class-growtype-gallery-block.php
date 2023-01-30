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

    /**
     * @return void
     */
    function create_block_growtype_gallery_block_init()
    {
        register_block_type_from_metadata(GROWTYPE_GALLERY_PATH . 'build', [
            'render_callback' => array ($this, 'render_callback_growtype_gallery'),
        ]);
    }

    /**
     * @param $attributes
     * @param $content
     * @return array|string|string[]|null
     */
    function render_callback_growtype_gallery($attributes, $content)
    {
        $gap = _wp_array_get($attributes, array ('style', 'spacing', 'blockGap'));

        if (is_array($gap)) {
            foreach ($gap as $key => $value) {
                $value = is_string($value) ? $value : '';
                $value = $value && preg_match('%[\\\(&=}]|/\*%', $value) ? null : $value;

                if (is_string($value) && str_contains($value, 'var:preset|spacing|')) {
                    $index_to_splice = strrpos($value, '|') + 1;
                    $slug = _wp_to_kebab_case(substr($value, $index_to_splice));
                    $value = "var(--wp--preset--spacing--$slug)";
                }

                $gap[$key] = $value;
            }
        } else {
            $gap = is_string($gap) ? $gap : '';
            $gap = $gap && preg_match('%[\\\(&=}]|/\*%', $gap) ? null : $gap;

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

        $fallback_gap = 'var( --wp--style--gallery-gap-default, var( --gallery-block--gutter-size, var( --wp--style--block-gap, 0.5em ) ) )';
        $gap_value = $gap ? $gap : $fallback_gap;
        $gap_column = $gap_value;

        if (is_array($gap_value)) {
            $gap_row = isset($gap_value['top']) ? $gap_value['top'] : $fallback_gap;
            $gap_column = isset($gap_value['left']) ? $gap_value['left'] : $fallback_gap;
            $gap_value = $gap_row === $gap_column ? $gap_row : $gap_row . ' ' . $gap_column;
        }

        if ('0' === $gap_column) {
            $gap_column = '0px';
        }

        $style = '.wp-block-gallery.' . $class . '{ --wp--style--unstable-gallery-gap: ' . $gap_column . '; gap: ' . $gap_value . '}';

        wp_enqueue_block_support_styles($style, 11);

        return $content;
    }

    /**
     * @param $block_content
     * @param $parsed_block
     * @return false|mixed|string
     */
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
                        $image_border_radius = isset($parsed_block["attrs"]['imageBorderRadius']) ? $parsed_block["attrs"]['imageBorderRadius'] . 'px' : '0';
                        $image_preview_format = $parsed_block["attrs"]['imagePreviewFormat'] ?? 'original';
                        $animation_on_scroll_effect = $parsed_block["attrs"]['animationOnScrollEffect'] ?? '2';
                        $group_name = $parsed_block["attrs"]['groupName'] ?? '';
                        $image_height = isset($parsed_block["attrs"]['imageHeight']) ? $parsed_block["attrs"]['imageHeight'] . '%' : '100%';
                        $image_padding = isset($parsed_block["attrs"]['imagePadding']) ? $parsed_block["attrs"]['imagePadding'] . 'px' : '5px';
                        $original_img_html = $parsed_block['innerBlocks'][$key]['innerContent'][0];
                        $loader_active = $parsed_block["attrs"]['loaderActive'] ?? false;
                        $loader_type = $parsed_block["attrs"]['loaderType'] ?? 'basic';
                        $gallery_id = isset($parsed_block["attrs"]['galleryId']) && !empty($parsed_block["attrs"]['galleryId']) ? $parsed_block["attrs"]['galleryId'] : md5(rand());
                        $imagePreviewSize = $parsed_block["attrs"]["imagePreviewSize"] ?? 'large';

                        /**
                         * get original html parts
                         */
                        $doc = new DOMDocument();
                        $doc->loadHTML($original_img_html);

                        $links_amount = $doc->getElementsByTagName('a')->length;

                        $xpath = new DOMXPath($doc);
                        $original_figure_class = $xpath->evaluate("string(//figure/@class)");
                        $original_img_src = $xpath->evaluate("string(//img/@src)");
                        $original_img_class = $xpath->evaluate("string(//img/@class)");
                        $original_img_alt = $xpath->evaluate("string(//img/@alt)");
                        $original_img_caption = $xpath->evaluate("string(//figcaption)");
                        $original_img_caption = !empty($original_img_caption) ? utf8_decode($original_img_caption) : '';
                        $original_img_id = str_replace('wp-image-', '', $original_img_class);
                        $caption_url = $xpath->evaluate("string(//a/@href)");
                        $image_url = '';

                        if ($links_amount === 3 || !empty($original_img_caption) && $links_amount === 2) {
                            $caption_url = '';
                            $image_url = $xpath->evaluate("string(//a/@href)");
                        } elseif (empty($original_img_caption) && $links_amount === 1) {
                            $image_url = $xpath->evaluate("string(//a/@href)");
                        }

                        $original_image_url = wp_get_attachment_url($original_img_id);

                        $img_preview_src = wp_get_attachment_image_src($original_img_id, $imagePreviewSize);
                        $img_preview_url = $img_preview_src[0] ?? '';
                        $img_preview_width = $img_preview_src[1] ?? '';
                        $img_preview_height = $img_preview_src[2] ?? '';

                        $template_path = 'preview.' . $preview_style . '.index';

                        $new_img_html = growtype_gallery_include_view($template_path,
                            [
                                'img_preview_src' => $img_preview_src,
                                'img_width' => $img_preview_width,
                                'img_height' => $img_preview_height,
                                'id' => $inner_block['attrs']['id'],
                                'img_preview_url' => $img_preview_url,
                                'image_padding' => $image_padding,
                                'src' => $original_image_url,
                                'parent_class' => $original_figure_class,
                                'child_class' => $original_img_class,
                                'alt' => $original_img_alt,
                                'caption' => $original_img_caption,
                                'caption_link' => $caption_url,
                                'overlay' => $has_overlay,
                                'overlay_color' => $overlay_color,
                                'overlay_icon' => $overlay_icon ?? false,
                                'watermark' => $watermark,
                                'image_preview_format' => $image_preview_format,
                                'link_to' => $link_to,
                                'group_name' => $group_name,
                                'image_height' => $image_height,
                                'image_url' => $image_url,
                                'image_border_radius' => $image_border_radius,
                                'loader_active' => $loader_active,
                                'loader_type' => $loader_type,
                                'index' => $key,
                            ]
                        );

                        array_push($images, $new_img_html);
                    }
                }

                $loader_active = $parsed_block["attrs"]['loaderActive'] ?? false;
                $loader_type = $parsed_block["attrs"]['loaderType'] ?? '';

                $images = implode('', $images);

                ob_start();

                ?>
                <div id="growtype-gallery-<?php echo $gallery_id ?>" class="growtype-gallery-wrapper <?php echo $loader_active ? 'loader-active' : '' ?>"
                     data-loader-type="<?php echo $loader_type ?>"
                >
                    <div id="growtype-gallery-grid-<?php echo $gallery_id ?>" class="<?php echo $parent_figure_class ?> growtype-gallery-grid" data-grid-effect="<?php echo $animation_on_scroll_effect ?>">
                        <?php echo $images ?>
                    </div>
                </div>
                <?php

                $parameters['gallery_id'] = $gallery_id;

                /**
                 * Pass values to frontend
                 */
                add_action('wp_footer', function () use ($parameters) { ?>
                    <script type="text/javascript">
                        new AnimOnScroll(document.getElementById('growtype-gallery-grid-<?php echo $parameters['gallery_id'] ?>'), {
                            minDuration: 0.4,
                            maxDuration: 0.6,
                            viewportFactor: 0.2
                        });
                    </script>
                <?php }, 100);

                $block_content = ob_get_clean();
            }
        }

        return $block_content;
    }

    /**
     * @param $parsed_block
     * @return array
     */
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

