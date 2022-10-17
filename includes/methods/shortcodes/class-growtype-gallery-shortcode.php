<?php

/**
 *
 */
class Growtype_Gallery_Shortcode
{
    function __construct()
    {
        if (!is_admin()) {
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
//        extract(shortcode_atts(array (
//            'post_type' => 'post',
//        ), $atts));
//
//        return $render;
    }
}
