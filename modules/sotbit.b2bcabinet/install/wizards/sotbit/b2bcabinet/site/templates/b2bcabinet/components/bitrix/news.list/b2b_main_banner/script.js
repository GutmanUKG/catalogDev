(function (window) {
    'use strict';

    if (window.JCB2BMainBanner)
        return;


    window.JCB2BMainBanner = function (arParams) {
        this.bannerCount = arParams.BANNER_COUNT || 1;
        this.swiperSelector = arParams.SWIPER_SELECTOR;
        this.swiper_main_page = null;
        this.init();
    }

    window.JCB2BMainBanner.prototype = {
        init: function () {
            if (this.bannerCount > 1) {
                BX.loadExt('sotbit.swiper').then((exports)=>{
                    this.swiper_main_page = new Swiper(this.swiperSelector, {
                        direction: 'horizontal',
                        loop: false,
                        speed: 800,
                        autoplay: {
                            delay: 4000,
                        },

                        pagination: {
                            el: '.slider-pagination',
                        },

                        navigation: {
                            nextEl: '.btn-slider-main--next',
                            prevEl: '.btn-slider-main--prev',
                        },

                        scrollbar: {
                            el: '.swiper-scrollbar',
                        },
                    });
                    BX.addCustomEvent('ToggleMainLayout', this.updateSlider.bind(this));
                });
            }
        },
        updateSlider: function () {
            this.swiper_main_page.update();
        }
    }
})(window);

