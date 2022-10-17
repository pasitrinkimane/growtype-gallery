export function gallerySlider() {
    $ = jQuery;

    let gallerySlider = $('.growtype-gallery-wrapper.slider-active .wp-block-growtype-gallery');

    gallerySlider.map(function (index, element) {
        let slidesAmountToShow = $(element).closest('.growtype-gallery-wrapper').attr('data-slides-amount-to-show')

        let sliderKey = 'slider' + index;

        window.growtypeGallery = {
            [sliderKey]: {
                infinite: true,
                slidesToShow: slidesAmountToShow,
                centerMode: false,
                arrows: true,
                // speed: 8000,
                // autoplaySpeed: 0,
                // cssEase: 'linear',
                slidesToScroll: 1,
                // variableWidth: true,
                dots: false,
                autoplay: false,
                responsive: [{
                    breakpoint: 500,
                    settings: {
                        slidesToShow: 2,
                    },
                }],
            }
        }

        $(element)
            .attr('data-slider-key', sliderKey)
            .slick(window.growtypeGallery[sliderKey]);
    })
}
