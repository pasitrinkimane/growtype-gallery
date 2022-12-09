<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://newcoolstudio.com
 * @since             1.0.0
 * @package           GROWTYPE_GALLERY
 *
 * @wordpress-plugin
 * Plugin Name:       Growtype - Gallery
 * Plugin URI:        http://growtype.com/
 * Description:       Advanced gallery functionality for modern websites.
 * Version:           1.0.0
 * Author:            Growtype
 * Author URI:        http://growtype.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       growtype-gallery
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('Growtype_Gallery_Version', '1.0.0');

/**
 * Plugin base name
 */
define('GROWTYPE_GALLERY_TEXT_DOMAIN', 'growtype-gallery');

/**
 * Plugin dir path
 */
define('GROWTYPE_GALLERY_PATH', plugin_dir_path(__FILE__));

/**
 * Plugin url
 */
define('GROWTYPE_GALLERY_URL', plugin_dir_url(__FILE__));

/**
 * Plugin url public
 */
define('GROWTYPE_GALLERY_URL_PUBLIC', plugin_dir_url(__FILE__) . 'public/');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-growtype-gallery-activator.php
 */
function activate_growtype_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-growtype-gallery-activator.php';
    GROWTYPE_GALLERY_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-growtype-gallery-deactivator.php
 */
function deactivate_growtype_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-growtype-gallery-deactivator.php';
    GROWTYPE_GALLERY_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_growtype_gallery');
register_deactivation_hook(__FILE__, 'deactivate_growtype_gallery');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-growtype-gallery.php';

/**
 * @return mixed
 */
function GROWTYPE_GALLERY()
{
    return GROWTYPE_GALLERY::instance();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function RUN_GROWTYPE_GALLERY()
{

    $plugin = new GROWTYPE_GALLERY();
    $plugin->run();

}

RUN_GROWTYPE_GALLERY();
