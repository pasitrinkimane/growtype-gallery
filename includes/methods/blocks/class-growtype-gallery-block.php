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
    public static function render_images($params)
    {
        $grid_colums = $params['grid_colums'] ?? 4;
        $images_details = $params['images_details'] ?? [];
        $gallery_id = $params['gallery_id'] ?? md5('growtype-gallery');
        $parent_figure_class = $params['parent_figure_class'] ?? 'wp-block-gallery-1 wp-block-growtype-gallery has-nested-images columns-' . $grid_colums . ' is-cropped is-layout-flex wp-block-gallery-is-layout-flex';
        $loader_active = $params['loader_active'] ?? true;
        $loader_type = $params['loader_type'] ?? 'basic';
        $preview_grid_style = $params['preview_grid_style'] ?? 'none';
        $image_preview_format = $params['image_preview_format'] ?? 'original';
        $animation_on_scroll_effect = $params['animation_on_scroll_effect'] ?? '2';

        $images_html = '';

        foreach ($images_details as $image_details) {
            $template_path = 'preview.basic.index';
            $images_html .= growtype_gallery_include_view($template_path, $image_details);
        }

        if (empty($images_html)) {
            return '';
        }

        ob_start();
        ?>
        <div id="growtype-gallery-<?php echo esc_attr($gallery_id); ?>" class="growtype-gallery-wrapper <?php echo esc_attr($loader_active ? 'loader-active' : ''); ?>"
             data-loader-type="<?php echo esc_attr($loader_type); ?>"
             data-preview-grid-style="<?php echo esc_attr($preview_grid_style); ?>"
             data-preview-format="<?php echo esc_attr($image_preview_format); ?>">
            <div
                id="growtype-gallery-grid-<?php echo esc_attr($gallery_id); ?>"
                class="<?php echo esc_attr($parent_figure_class); ?> growtype-gallery-grid"
                data-grid-effect="<?php echo esc_attr($animation_on_scroll_effect); ?>"
            >
                <?php 
                // SECURITY: $images_html is safe - generated internally from WordPress functions and controlled templates
                echo $images_html; 
                ?>
            </div>
        </div>
        <?php

        $block_content = ob_get_clean();

        add_action('wp_footer', function () use ($gallery_id) {
            ?>
            <script type="text/javascript">
                new AnimOnScroll(document.getElementById('growtype-gallery-grid-<?php echo esc_attr($gallery_id); ?>'), {
                    minDuration: 0.4,
                    maxDuration: 0.6,
                    viewportFactor: 0.2
                });
            </script>
            <?php
        }, 100);

        return $block_content;
    }

    function growtype_gallery_render_block($block_content, $parsed_block)
    {
        if ('growtype/gallery' !== $parsed_block['blockName'] || is_admin()) {
            return $block_content;
        }

        if (empty($block_content)) {
            return $block_content;
        }

        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($block_content);
        libxml_clear_errors();

        $xpath = new DOMXPath($doc);
        $parent_figure_class = $xpath->evaluate("string(//figure/@class)");

        $attrs = $parsed_block['attrs'];
        $link_to = $attrs['linkTo'];
        $has_overlay = $attrs['hasOverlay'] ?? '';
        $overlay_color = $attrs['overlayColor'] ?? '';
        $watermark = $attrs['watermark'] ?? '';
        $image_border_radius = isset($attrs['imageBorderRadius']) ? $attrs['imageBorderRadius'] . 'px' : '0';
        $image_preview_format = $attrs['imagePreviewFormat'] ?? 'original';
        $animation_on_scroll_effect = $attrs['animationOnScrollEffect'] ?? '2';
        $group_name = $attrs['groupName'] ?? '';
        $image_height = isset($attrs['imageHeight']) ? $attrs['imageHeight'] . '%' : apply_filters('growtype_gallery_default_image_height', '100%');
        $image_padding = isset($attrs['imagePadding']) ? $attrs['imagePadding'] . 'px' : '5px';
        $loader_active = $attrs['loaderActive'] ?? false;
        $loader_type = $attrs['loaderType'] ?? 'basic';
        $gallery_id = $attrs['galleryId'] ?? md5(rand());
        $image_preview_size = $attrs['imagePreviewSize'] ?? 'large';
        $image_main_size = $attrs['sizeSlug'] ?? 'full';
        $preview_grid_style = $attrs['previewGridStyle'] ?? 'none';

        $images_details = [];

        foreach ($parsed_block['innerBlocks'] as $inner_block) {
            if ('core/image' !== $inner_block['blockName']) {
                continue;
            }

            $img_unique_id = 'growtype-gallery-image-' . uniqid();
            $original_img_html = $inner_block['innerContent'][0];

            $doc = new DOMDocument();
            $doc->loadHTML($original_img_html);
            $xpath = new DOMXPath($doc);

            $original_figure_class = $xpath->evaluate("string(//figure/@class)");
            $original_img_src = $xpath->evaluate("string(//img/@src)");
            $original_img_class = $xpath->evaluate("string(//img/@class)");
            $original_img_alt = $xpath->evaluate("string(//img/@alt)");
            $original_img_caption = $xpath->evaluate("string(//figcaption)") ?? '';
            $original_img_id = str_replace('wp-image-', '', $original_img_class);
            $caption_url = $xpath->evaluate("string(//a/@href)");
            $image_url = $xpath->evaluate("string(//a/@href)");

            $img_original_src = wp_get_attachment_image_src($original_img_id, $image_main_size);
            $img_original_url = $img_original_src[0] ?? '';

            $img_preview_src = wp_get_attachment_image_src($original_img_id, $image_preview_size);
            $img_preview_url = $img_preview_src[0] ?? '';

            $image_details = [
                'img_preview_src' => $img_preview_src,
                'img_width' => $img_preview_src[1] ?? '',
                'img_height' => $img_preview_src[2] ?? '',
                'id' => $inner_block['attrs']['id'],
                'img_preview_url' => $img_preview_url,
                'image_padding' => $image_padding,
                'src' => $img_original_url,
                'parent_class' => $original_figure_class,
                'child_class' => $original_img_class,
                'alt' => $original_img_alt,
                'caption' => utf8_decode($original_img_caption),
                'caption_link' => $caption_url,
                'overlay' => $has_overlay,
                'overlay_color' => $overlay_color,
                'overlay_icon' => $attrs['overlayIcon'] ?? false,
                'watermark' => $watermark,
                'image_preview_format' => $image_preview_format,
                'link_to' => $link_to,
                'group_name' => $group_name,
                'image_height' => $image_height,
                'image_url' => $image_url,
                'image_border_radius' => $image_border_radius,
                'loader_active' => $loader_active,
                'loader_type' => $loader_type,
                'index' => count($images_details),
                'img_unique_id' => $img_unique_id,
                'post_id' => strpos($image_url, '#id_') !== false ? str_replace('#id_', '', $image_url) : url_to_postid($image_url),
                'preview_grid_style' => $preview_grid_style,
            ];

            $images_details[] = $image_details;
        }

        $params = [
            'images_details' => $images_details,
            'gallery_id' => $gallery_id,
            'parent_figure_class' => $parent_figure_class,
            'loader_active' => $loader_active,
            'loader_type' => $loader_type,
            'preview_grid_style' => $preview_grid_style,
            'image_preview_format' => $image_preview_format,
            'animation_on_scroll_effect' => $animation_on_scroll_effect,
        ];

        $block_content = self::render_images($params);

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

