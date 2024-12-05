<?php

/**
 *
 */
class Growtype_Gallery_Shortcode
{
    function __construct()
    {
        if (!is_admin() && !wp_is_json_request()) {
            add_shortcode('growtype_gallery', array ($this, 'growtype_gallery_shortcode'));
        }
    }

    /**
     * @param $atts
     * @return string
     * Posts shortcode
     */
    function growtype_gallery_shortcode($atts)
    {
        $params = apply_filters('growtype_gallery_shortcode_params', [], $atts);
        $content = Growtype_Gallery_Block::render_images($params);

        return $content;
    }
}
