$(function() {
    /**
     * Script tag manager
     */
    let oTemplateApp = function () {
        const oSelf = this;
        /**
         * Use to initialize events
         */
        this.init = function () {
            this.initCafe24Api();
            this.initData();
            this.runScript();
        };

        /**
         * Use to initialize data
         */
        this.initData = function () {
            oSelf.sMallId = CAFE24API.MALL_ID;
            oSelf.iShopNo = CAFE24API.SHOP_NO;
            // Developer should fill up this field
            // Url needs to be match on the domain of the app
            oSelf.sUrl = '';
            oSelf.sField = '&product_no,product_name,detail_image';
            oSelf.sDefaultImg = '//img.echosting.cafe24.com/thumb/104x104_1.gif';
            oSelf.oCurrency = SHOP_CURRENCY_FORMAT.getInputFormat();
            oSelf.bPlayFlag = true;
            oSelf.oHttpParam = {
                'mall_id' : oSelf.sMallId,
                'shop_no' : oSelf.iShopNo
            };
        };

        /**
         * Check if script tag is runnable base on the http result
         */
        this.runScript = function () {
            $.ajax({
                type: 'GET',
                url: oSelf.sUrl + '/scripttag',
                data: oSelf.oHttpParam,
                success: function (oResponse) {
                    if (oResponse['error'] === true || oResponse['data'] === false) {
                        return;
                    }

                    oSelf.createBanner();
                }
            });
        };

        /**
         * Create banner base on the product saved in app db
         */
        this.createBanner = function () {
            $.ajax({
                type: 'GET',
                url: oSelf.sUrl + '/products/saved',
                data: oSelf.oHttpParam,
                success: function (aProduct) {
                    if (aProduct.length === 0) {
                        return;
                    }

                    oSelf.getProductData(aProduct);
                }
            });
        };

        /**
         * Get products information
         * @param aProduct
         */
        this.getProductData = function (aProduct) {
            const sProductNo = aProduct.join(',');
            CAFE24API.get('/api/v2/products?limit=100&display=T&selling=T&product_no='+sProductNo, function (oError, mResponse) {
                if (typeof mResponse['products'] === 'undefined') {
                    oSelf.getProductData(aProduct);
                }

                if (mResponse['products'].length === 0) {
                    return;
                }

                oSelf.addDisplayBanner(mResponse['products']);
            });
        };

        /**
         * Initialized cafe24 api
         */
        this.initCafe24Api = function () {
            // Developer should fill up this field
            // Client id should should be based on the application's client id
            // from developer center
            (CAFE24API.init({
                version: '2021-03-01',
                client_id: ''
            }));
        };

        /**
         * Use to get the formatted price
         * @param sPrice
         * @returns {string}
         */
        this.getPrice = function (sPrice) {
            let oCurrency = oSelf.oCurrency.head;
            sPrice = parseFloat(sPrice, 10).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            if (oSelf.oCurrency.head === '') {
                oCurrency = oSelf.oCurrency.tail;
                return oCurrency['currency_symbol'] + ' ' + sPrice;
            }

            return oCurrency['currency_symbol'] + ' ' + sPrice;
        };

        /**
         * Display the banner in front
         * @param aProduct
         */
        this.addDisplayBanner = function (aProduct) {
            const sSliderBody = oSelf.getBanner(aProduct);
            $('head').append('<link rel="stylesheet" type="text/css" href="' + oSelf.sUrl  + '/css/template-app.css" media="all" />');
            $('#contents').prepend('<div id="template-app-slider"> <a href="#" class="navigation control_next">></a><a href="#" class="navigation control_prev"><</a>' + sSliderBody + '</div>');
            this.addSliderEvent();
        };

        /**
         * Get the banner base on the products
         * @param aProduct
         * @returns {string}
         */
        this.getBanner = function (aProduct) {
            let sSliderBody = '';
            aProduct.forEach(function(oProduct, iIndex) {
                sSliderBody += oSelf.createBannerSlide(oProduct, iIndex + 1);
            });

            return sSliderBody;
        };

        /**
         * Create slide for banner
         * @param oProduct
         * @param iId
         * @returns {string}
         */
        this.createBannerSlide = function (oProduct, iId) {
            let sImageUrl = oSelf.sDefaultImg;
            let sClass = 'default-image';
            if (oProduct['detail_image'] !== null) {
                sImageUrl = oProduct['detail_image'];
                sClass = '';
            }

            return '<div class="div-slider" id="slide-' + iId + '" > ' +
                '<img src="' + sImageUrl + '" class="' + sClass + '">' +
                '<h2> ' + oProduct['product_name'] + ' </h2>' +
                '<h5> ' + oSelf.getPrice(oProduct['price']) + '</h5>' +
                '</div>';
        };

        /**
         * For binding events in slider
         */
        this.addSliderEvent = function () {
            $('#template-app-slider > div:gt(0)').hide();
            setInterval(oSelf.playBanner, 5000);
            $(document).delegate('#template-app-slider .control_next', 'click', oSelf.clickNext);
            $(document).delegate('#template-app-slider .control_prev', 'click', oSelf.clickPrev);
            $(document).delegate('#template-app-slider .navigation', 'mouseenter', oSelf.hoverNavigation);
            $(document).delegate('#template-app-slider .navigation', 'mouseleave', oSelf.hoverOutNavigation);
        };

        /**
         * Triggers when cursor hover the navigation buttons in slider
         */
        this.hoverNavigation = function () {
            oSelf.bPlayFlag = false;
        };

        /**
         * Triggers when cursor leave the navigation buttons in slider
         */
        this.hoverOutNavigation = function () {
            oSelf.bPlayFlag = true;
        };

        /**
         * Event for next previous button
         */
        this.clickNext = function () {
            const sCurrentSlider = oSelf.getCurrentSlider();
            if (sCurrentSlider.next().length === 0) {
                sCurrentSlider.fadeOut(1000);
                $('#template-app-slider #slide-1').fadeIn(1000);
                return;
            }

            sCurrentSlider.fadeOut(1000).next().fadeIn(1000);
        };

        /**
         * Event for clicking previous button
         */
        this.clickPrev = function () {
            const sCurrentSlider = oSelf.getCurrentSlider();
            if (sCurrentSlider.prev().hasClass('control_prev') === true) {
                sCurrentSlider.fadeOut(1000);
                $('#template-app-slider .div-slider:last').fadeIn(1000);
                return;
            }

            sCurrentSlider.fadeOut(1000).prev().fadeIn(1000);
        };

        /**
         * Go the the next banner
         */
        this.playBanner = function () {
            if (oSelf.bPlayFlag === false) {
                return;
            }
            $('#template-app-slider .control_next').click();
        };

        /**
         * Get the current slider
         * @returns {*}
         */
        this.getCurrentSlider = function () {
            const aSlider = $('#template-app-slider').find('.div-slider');
            let oCurrentSlider = null;
            aSlider.each(function(iIndex, oSlider) {
                const sDisplayType = $(oSlider).css('display');
                if (sDisplayType === 'none') {
                    return;
                }

                oCurrentSlider = $(oSlider);
            });

            return oCurrentSlider;
        };

    };

    oTemplateApp = new oTemplateApp();
    oTemplateApp.init();
});
