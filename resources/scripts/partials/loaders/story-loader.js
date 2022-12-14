/**
 * Instagram like stories
 */
export function storyLoader() {
    $ = jQuery;

    let delayBetweenSlides = 3000;

    window.growtypeGallery = {
        loader: {}
    }

    function initStoryLoaderContainer(item = null, loaderIsActive = true) {
        initStoryLoader()
    }

    initStoryLoaderContainer();

    /**
     * @param item
     * @param loaderIsActive
     */
    function previewStoryLoaderItem(item = null, loaderIsActive = true) {
        let sliderKey = item.closest('.growtype-gallery-wrapper').attr('id');
        let loader = item.find('.loader');

        loader.fadeOut();

        if (item.find('.overlay').is(':visible')) {
            returnStoryLoaderItemToInitialState(sliderKey)
            item.find('.overlay').fadeOut();
            if (loaderIsActive) {
                showLoader(loader);
            }
        }
    }

    function showLoader(loader) {
        let sliderKey = loader.closest('.growtype-gallery-wrapper').attr('id');

        window.growtypeGallery.loader[sliderKey].progress = 0;

        loader.find('.loader-inner').attr("data-progress", "0")
        loader.fadeIn()

        loaderProgress(loader)
    }

    function loaderProgress(loader) {
        let loaderInner = loader.find('.loader-inner');
        let sliderKey = loader.closest('.growtype-gallery-wrapper').attr('id');
        let progress = window.growtypeGallery.loader[sliderKey].progress;

        let progressValueUpdated = parseInt(progress) + 1;

        window.growtypeGallery.loader[sliderKey].progress = progressValueUpdated;

        loaderInner.css('width', progressValueUpdated + '%');

        if (progress >= 0 && progress <= 99) {
            setTimeout(function () {
                loaderProgress(loader)
            }, delayBetweenSlides / 100);
        } else if (progress === 100) {
            window.growtypeGallery.loader[sliderKey].progress = 0
            changeStoryLoaderItem(sliderKey);
        }
    }

    function returnStoryLoaderItemToInitialState(sliderKey) {
        $('#' + sliderKey).find('.loader').fadeOut();
        $('#' + sliderKey).find('.overlay').fadeIn();
    }

    /**
     * Mouse enter
     */
    $('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').on('mouseenter touchstart', function () {
        if ($(this).find('.slick-slider').length > 0) {
            let sliderKey = $(this).attr('id');
            stopStoryLoader(sliderKey);
        }
    });

    $('.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').on('mouseenter touchstart', function () {
        if ($(this).closest('.growtype-gallery-wrapper').find('.slick-slider').length === 0) {
            let sliderKey = $(this).closest('.growtype-gallery-wrapper').attr('id');
            stopStoryLoader(sliderKey);
        }

        previewStoryLoaderItem($(this), false);
    });

    /**
     * Mouse leave for entire story loader
     */
    $('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').on('mouseleave touchend', function () {
        if ($(this).find('.slick-slider').length > 0) {
            let sliderKey = $(this).attr('id');
            window.growtypeGallery.loader[sliderKey].stopped = false;

            window.growtypeGallery.loader[sliderKey].startStoryLoader = setTimeout(function () {
                startStoryLoader(sliderKey)
            }, 1000);
        }
    });

    /**
     * Mouse leave for story loader item
     */
    $('.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').on('mouseleave touchend', function () {
        let sliderKey = $(this).closest('.growtype-gallery-wrapper').attr('id');

        window.growtypeGallery.loader[sliderKey].counter = parseInt($(this).closest('.wp-block-image').attr('data-index')) + 1
        window.growtypeGallery.loader[sliderKey].progress = 0;

        returnStoryLoaderItemToInitialState(sliderKey)

        if ($(this).closest('.growtype-gallery-wrapper').find('.slick-slider').length === 0) {
            window.growtypeGallery.loader[sliderKey].stopped = false;

            window.growtypeGallery.loader[sliderKey].startStoryLoader = setTimeout(function () {
                startStoryLoader(sliderKey)
            }, 1000);
        }
    });

    /**
     * Each story slider init interval
     */
    function initStoryLoader() {
        if ($('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').length > 0) {
            $('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').each(function (index, element) {
                let sliderKey = $(element).attr('id');

                window.growtypeGallery.loader[sliderKey] = {
                    counter: 0,
                    progress: 0,
                    stopped: false
                }

                startStoryLoader(sliderKey);
            });
        }
    }

    /**
     * Init slider
     * @param sliderKey
     */
    function startStoryLoader(sliderKey) {
        if (!window.growtypeGallery.loader[sliderKey].stopped) {
            changeStoryLoaderItem(sliderKey);
        }
    }

    function changeStoryLoaderItem(sliderKey) {
        if (window.growtypeGallery.loader[sliderKey]['counter'] > $('#' + sliderKey + '.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').length - 1) {
            window.growtypeGallery.loader[sliderKey]['counter'] = 0
        }

        let activeSlide = $('#' + sliderKey + '.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').find('.wp-block-image[data-index="' + window.growtypeGallery.loader[sliderKey]['counter'] + '"]')

        if (activeSlide.length > 0) {
            previewStoryLoaderItem(activeSlide);

            if (activeSlide.hasClass('slick-slide')) {
                $('#' + sliderKey + '.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .slick-slider').slick('slickGoTo', window.growtypeGallery.loader[sliderKey]['counter'])
            }
        }

        window.growtypeGallery.loader[sliderKey]['counter']++
    }

    /**
     * Stop story slider
     */
    function stopStoryLoader(sliderKey) {
        window.growtypeGallery.loader[sliderKey].stopped = true;
        clearTimeout(window.growtypeGallery.loader[sliderKey].startStoryLoader);
        window.growtypeGallery.loader[sliderKey].progress = -1;
    }
}
