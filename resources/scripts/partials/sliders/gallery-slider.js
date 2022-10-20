export function gallerySlider() {
    $ = jQuery;

    let gallerySlider = $('.growtype-gallery-wrapper.slider-active .wp-block-growtype-gallery');

    gallerySlider.map(function (index, element) {
        let slidesToShow = parseInt($(element).closest('.growtype-gallery-wrapper').attr('data-slides-amount-to-show'))
        let sliderInfinite = $(element).closest('.growtype-gallery-wrapper').attr('data-slider-infinite') === 'true' ? true : false
        let sliderCenterMode = $(element).closest('.growtype-gallery-wrapper').attr('data-slider-center-mode') === 'true' ? true : false

        let sliderKey = 'slider' + index;

        window.growtypeGallery = {
            [sliderKey]: {
                infinite: sliderInfinite,
                slidesToShow: slidesToShow,
                centerMode: sliderCenterMode,
                arrows: true,
                // speed: 8000,
                // autoplaySpeed: 0,
                // cssEase: 'linear',
                slidesToScroll: 1,
                // variableWidth: false,
                dots: false,
                autoplay: false,
                responsive: [
                    {
                        breakpoint: 1000,
                        settings: {
                            slidesToShow: slidesToShow >= 4 ? 4 : slidesToShow,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: slidesToShow >= 3 ? 3 : slidesToShow,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 700,
                        settings: {
                            slidesToShow: slidesToShow >= 2 ? 2 : slidesToShow,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 570,
                        settings: {
                            slidesToShow: slidesToShow >= 1 ? 1 : slidesToShow,
                            slidesToScroll: 1
                        }
                    }
                ],
            }
        }

        $(element)
            .attr('data-slider-key', sliderKey)
            .slick(window.growtypeGallery[sliderKey]);
    })
}
