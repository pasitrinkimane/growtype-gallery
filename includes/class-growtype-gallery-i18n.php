<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Gallery
 * @subpackage Growtype_Gallery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Growtype_Gallery
 * @subpackage Growtype_Gallery/includes
 * @author     Your Name <email@example.com>
 */
class Growtype_Gallery_i18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'growtype-gallery',
            false,
            GROWTYPE_GALLERY_TEXT_DOMAIN . '/languages/'
        );
    }
}
