let mix = require('laravel-mix');

mix.setResourceRoot('./')

/**
 * Block
 */
mix.setPublicPath('./public');

/**
 * General
 */
mix.sass('resources/styles/growtype-gallery.scss', 'styles')
mix.sass('resources/styles/growtype-gallery-admin.scss', 'styles')

mix.js('resources/scripts/growtype-gallery.js', 'scripts')
mix.js('resources/scripts/growtype-gallery-admin.js', 'scripts')

/**
 * Plugins
 */
mix
    .copyDirectory('node_modules/slick-carousel', 'public/plugins/slick-carousel')
    .copy('node_modules/imagesloaded/', 'public/vendor/imagesloaded')
    .copy('node_modules/masonry-layout/', 'public/vendor/masonry-layout')
    .copy('resources/vendor/animOnScroll', 'public/vendor/animOnScroll')
    .copy('resources/vendor/classie', 'public/vendor/classie')
    .copy('resources/vendor/modernizr', 'public/vendor/modernizr');

mix
    .sourceMaps()
    .version();
