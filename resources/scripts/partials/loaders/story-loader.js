/**
 * Instagram like stories
 */

export function storyLoader() {
    $ = jQuery;

    let delayBetweenSlides = 3000;

    window.growtypeGallery = {
        loader: {}
    }

    function autoplayStoryItems(item = null, loaderIsActive = true) {
        initStoryDisplayInterval()
    }

    autoplayStoryItems();

    /**
     * @param item
     * @param loaderIsActive
     */
    function displayStoryItem(item = null, loaderIsActive = true) {
        let $this = item;

        let loader = item.find('.loader');

        loader.fadeOut();

        if ($this.find('.overlay').is(':visible')) {
            returnStoryItemsToInitialState($this)
            item.find('.overlay').fadeOut();
            if (loaderIsActive) {
                showLoader(loader);
            }
        }
    }

    function showLoader(loader) {
        loader.find('.loader-inner').attr("data-progress", "0")
        loader.fadeIn()
        loaderProgress(loader)
    }

    function loaderProgress(loader) {
        let loaderInner = loader.find('.loader-inner');
        let progress = loaderInner.attr("data-progress")

        let progressValueUpdated = parseInt(progress) + 1;

        loaderInner.attr("data-progress", progressValueUpdated);

        loaderInner.css('width', progressValueUpdated + '%');

        if (progress < 99) {
            setTimeout(function () {
                loaderProgress(loader)
            }, delayBetweenSlides / 100);
        }
    }

    function returnStoryItemsToInitialState($this) {
        $this.closest('.growtype-gallery-wrapper').find('.loader').fadeOut();
        $this.closest('.growtype-gallery-wrapper').find('.overlay').fadeIn();
    }

    /**
     * Mouse enter
     */
    $('.growtype-gallery-wrapper').mouseenter(function () {
        let sliderKey = $(this).find('.slick-slider').attr('data-slider-key');
        stopStoryDisplayInterval($(this).find('.slick-slider').attr('data-slider-key'));
    });

    $('.growtype-gallery-wrapper[data-loader-type="story"].slider-active .slick-slide').mouseenter(function () {
        displayStoryItem($(this), false);
    });

    /**
     * Mouse leave
     */
    $('.growtype-gallery-wrapper').mouseleave(function () {
        let sliderKey = $(this).find('.slick-slider').attr('data-slider-key');
        startStoryDisplayInterval(sliderKey)
    });

    $('.growtype-gallery-wrapper[data-loader-type="story"].slider-active .slick-slide').mouseleave(function () {
        returnStoryItemsToInitialState($(this))
    });

    /**
     *
     */
    function initStoryDisplayInterval() {
        if ($('.growtype-gallery-wrapper[data-loader-type="story"].slider-active').length > 0) {
            $('.growtype-gallery-wrapper[data-loader-type="story"].slider-active').each(function (index, element) {
                let sliderKey = $(element).find('.slick-slider').attr('data-slider-key');

                startStoryDisplayInterval(sliderKey);
            });
        }
    }

    function startStoryDisplayInterval(sliderKey) {
        window.growtypeGallery.loader[sliderKey] = {
            interval: null,
            counter: 0,
        }

        window.growtypeGallery.loader[sliderKey]['interval'] = setInterval(function () {
            if (document.hasFocus()) {
                if (window.growtypeGallery.loader[sliderKey]['counter'] > $('.slick-slider[data-slider-key="' + sliderKey + '"] .slick-slide:not(.slick-cloned)').length - 1) {
                    window.growtypeGallery.loader[sliderKey]['counter'] = 0
                }

                let activeSlide = $('.slick-slider[data-slider-key="' + sliderKey + '"]').find('.slick-slide[data-slick-index="' + window.growtypeGallery.loader[sliderKey]['counter'] + '"]')

                if (activeSlide.length > 0) {
                    displayStoryItem(activeSlide);

                    if (!activeSlide.hasClass('slick-active')) {
                        $('.slick-slider[data-slider-key="' + sliderKey + '"]').slick('slickGoTo', window.growtypeGallery.loader[sliderKey]['counter'])
                    }
                }

                window.growtypeGallery.loader[sliderKey]['counter']++
            }
        }, delayBetweenSlides);
    }

    function stopStoryDisplayInterval(sliderKey) {
        if (window.growtypeGallery.loader[sliderKey] !== undefined) {
            clearInterval(window.growtypeGallery.loader[sliderKey]['interval']);
        }
    }
}
