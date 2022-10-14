<?php

/**
 *
 */
class Growtype_Gallery_Block
{
    function __construct()
    {
        add_action('init', array ($this, 'create_block_growtype_gallery_block_init'));
    }

    function create_block_growtype_gallery_block_init()
    {
        register_block_type_from_metadata(GROWTYPE_GALLERY_PATH . 'build', [
            'render_callback' => array ($this, 'render_callback_growtype_gallery'),
        ]);
    }

    // Optional: Moved render callback to separate function to keep logic clear
    function render_callback_growtype_gallery($block_attributes, $content)
    {
        return do_shortcode($content);
    }
}
