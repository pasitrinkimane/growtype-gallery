function initGrowtypeMasonry() {
    let $container = $('.wp-block-growtype-gallery');

    $container.imagesLoaded(function () {
        $container.masonry({
            // options...
            itemSelector: '.wp-block-image',
            columnWidth: 0
        });
    });
}

export {initGrowtypeMasonry};

