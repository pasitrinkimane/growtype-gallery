<?php

/**
 * @param $path
 * @param null $data
 * @return mixed
 * Include view
 */
if (!function_exists('growtype_gallery_include_view')) {
    function growtype_gallery_include_view($file_path, $variables = array (), $print = false)
    {
        $output = null;

        $fallback_view = GROWTYPE_GALLERY_PATH . 'resources/views/' . str_replace('.', '/', $file_path) . '.php';
        $child_blade_view = get_stylesheet_directory() . '/growtype-gallery/' . str_replace('.', '/', $file_path) . '.blade.php';
        $child_view = get_stylesheet_directory() . '/growtype-gallery/' . str_replace('.', '/', $file_path) . '.php';

        $template_path = $fallback_view;

        if (file_exists($child_blade_view)) {
            return App\template($child_blade_view, $variables);
        } elseif (file_exists($child_view)) {
            $template_path = $child_view;
        }

        if (file_exists($template_path)) {
            // Extract the variables to a local namespace
            extract($variables);

            // Start output buffering
            ob_start();

            // Include the template file
            include $template_path;

            // End buffering and return its contents
            $output = ob_get_clean();
        }

        if ($print) {
            print $output;
        }

        return $output;
    }
}
