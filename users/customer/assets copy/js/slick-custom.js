(function($) {
    /// Home Banner Slider slider ///
    $(".h-banner-slider").slick({
        dots: false,
        slidesToShow: 1,
        infinite: true,
        centerMode: true,
        centerPadding: "50px",
        arrows: false,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 475,
                settings: {
                    centerPadding: "20px",
                },
            },

            {
                breakpoint: 375,
                settings: {
                    centerPadding: "15px",
                },
            },
        ],
    });

    /// Product Slider ///
    $(".product-slider").slick({
        dots: false,
        slidesToShow: 3, // Adjust based on your layout
        slidesToScroll: 1, // Number of slides to scroll at once
        arrows: false, // Keep arrows disabled if preferred
        centerMode: false, // Disable center mode for left alignment
        infinite: false, // Ensure that infinite scrolling is off to start the slides at the left side
        responsive: [{
                breakpoint: 567,
                settings: {
                    slidesToShow: 3,
                    centerMode: false, // Ensure center mode is disabled for small screens
                }
            },
            {
                breakpoint: 475,
                settings: {
                    slidesToShow: 2,
                    centerMode: false, // Ensure center mode is disabled for small screens
                }
            },
            {
                breakpoint: 375,
                settings: {
                    slidesToShow: 1,
                    centerMode: false, // Ensure center mode is disabled
                    centerPadding: "0px", // No padding for smaller screens
                }
            }
        ]
    });





    /// Product Page Banner Slider ///
    $('.product-banner').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true
    });

    /// Product Slider ///
    $(".product-recent-slider").slick({
        dots: false,
        slidesToShow: 3,
        centerMode: true,
        centerPadding: "50px",
        arrows: false,
        slidesToScroll: 1,
        responsive: [{
                breakpoint: 567,
                settings: {
                    slidesToShow: 3,
                    centerPadding: "25px",
                },
            },
            {
                breakpoint: 475,
                settings: {
                    slidesToShow: 2,
                    centerPadding: "25px",
                },
            },
        ],
    });

    /// Onboarding Slider ///
    $('.onboarding-slider').slick({
        dots: true,
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        adaptiveHeight: true
    });

    /// Onboarding Slider ///

    if ($(window).width() > '767') {
        $('.recently-list-slider').slick({
            speed: 300,
            slidesToShow: 4,
            centerMode: true,
            centerPadding: "30px ",
            responsive: [{
                    breakpoint: 1367,
                    settings: {
                        slidesToShow: 5,
                        centerPadding: "10px ",
                    },
                },
                {
                    breakpoint: 1199,
                    settings: {
                        slidesToShow: 5,
                        centerPadding: "20px ",
                    },
                },
                {
                    breakpoint: 991,
                    settings: {
                        slidesToShow: 5,
                        centerPadding: "30px ",
                    },
                },
            ]
        });
    }

    /// Coupon-slider ///
    if ($(window).width() > '767') {
        $('.coupon-slider').slick({
            speed: 300,
            slidesToShow: 3,
            centerMode: true,
            centerPadding: "30px ",

        });
    }
})(jQuery);