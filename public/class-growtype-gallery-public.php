<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Growtype_Gallery
 * @subpackage Growtype_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Growtype_Gallery
 * @subpackage Growtype_Gallery/public
 * @author     Your Name <email@example.com>
 */
class Growtype_Gallery_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $Growtype_Gallery The ID of this plugin.
     */
    private $Growtype_Gallery;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $Growtype_Gallery The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($Growtype_Gallery, $version)
    {
        $this->Growtype_Gallery = $Growtype_Gallery;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Growtype_Gallery_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Growtype_Gallery_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->Growtype_Gallery, GROWTYPE_GALLERY_URL_PUBLIC . 'styles/growtype-gallery.css', array (), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * Imagesloaded
         */
        wp_enqueue_script('imagesloaded.js', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/imagesloaded/imagesloaded.pkgd.min.js', ['jquery'], null, true);

        /**
         * classie
         */
        wp_enqueue_script('classie.js', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/classie/index.js', ['jquery'], null, true);

        /**
         * modernizr
         */
        wp_enqueue_script('modernizr.js', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/modernizr/index.js', ['jquery'], null, true);

        /**
         * Masonry
         */
        wp_enqueue_script('masonry.js', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/masonry-layout/dist/masonry.pkgd.min.js', ['jquery'], null, true);

        /**
         * animOnScroll
         */
        wp_enqueue_script('animOnScroll.js', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/animOnScroll/index.js', ['jquery'], null, true);
        wp_enqueue_style('animOnScroll.css', GROWTYPE_GALLERY_URL_PUBLIC . 'vendor/animOnScroll/component.css', false, null);

        /**
         * Main
         */
        wp_enqueue_script($this->Growtype_Gallery, GROWTYPE_GALLERY_URL_PUBLIC . 'scripts/growtype-gallery.js', array ('jquery'), $this->version, true);
    }

}
