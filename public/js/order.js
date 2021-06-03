$(function() {

    /**
     * oOrderList - contains all methods and properties related to order list page
     */
    var oOrderList = {

        /**
         * Initial function (call methods needed upon loading the page)
         */
        init: function() {
            this.setDOMElements();
            this.bindElementEvents();
        },

        /**
         * Cache DOM Elements
         */
        setDOMElements: function() {
            /** SEARCH FILTER ELEMENTS */
            this.oSelectFilter = $('#order_filter');
            this.oInputOrder = $('#order_filter_value');
            this.oBtnSearchOrder = $('#btnSearchOrder');

            this.oSelectProductFilter = $('#select_product_filter');
            this.oInputProduct = $('#input_product_value');
            this.oSearchProduct = $('#searchProduct');
            this.oResetFilters = $('#btnResetOrder');

            /** ORDER TABLE (SEARCH RESULT) */
            this.oTableOrder = $('#tbl_order');
            this.oPagination = $('.pagination');

            this.setSearchFilters();
        },

        /**
         * Set events to DOM Elements
         */
        bindElementEvents: function() {
            this.oTableOrder.on('click', '.tr_order', function(oEvent) {
                window.open('/details?order=' + $(this).text(), '', 'width=1100,height=500,resizable=1,scrollbars=yes');
            });

            this.oSearchProduct.click(function(oEvent) {
                oEvent.preventDefault();
                window.open('/search?filter=' + oOrderList.oSelectProductFilter.val(), '', 'width=1100,height=500,resizable=1,scrollbars=yes');
            });

            libDateUtil.oStartCal.datepicker().on('changeDate', function(oDate) {
                $('.btnDate').attr('class', 'btn btn-sm btn-secondary btnDate');

                let sDate = new Date(oDate.date);
                libDateUtil.validateDate($(this), sDate);
            });

            libDateUtil.oEndCal.datepicker().on('changeDate', function(oDate) {
                $('.btnDate').attr('class', 'btn btn-sm btn-secondary btnDate');
                let sDate = new Date(oDate.date);
                libDateUtil.validateDate($(this), sDate);
            });

            this.oBtnSearchOrder.click(function() {
                let sQuery = $.param(oOrderList.getOrderSearchFilters());
                $(this).attr('href', '?' + sQuery);
            });

            this.oResetFilters.click(function() {
                oOrderList.oSelectFilter.val('order_id');
                oOrderList.oInputOrder.val('');
                oOrderList.oSelectProductFilter.val('product_name');
                oOrderList.oInputProduct.val('');
                libDateUtil.setInitDate();
                $(this).attr('href', '?');
            });
        },

        /**
         * Set order search filters based on the given parameters
         * @param array aParams 
         */
        setSearchFilters: function() {
            let sUrlQuery = window.location.search.substring(1);
            if (sUrlQuery.length === 0) {
                libDateUtil.setInitDate();
                return;
            }

            let aParams = decodeURIComponent(sUrlQuery).split('&');

            let aValues = [], aKeys = [];
            $.each(aParams, function(iKey, sValue) {
                let aData = sValue.split('=');
                aValues[aData[0]] = aData[1];
                aKeys.push(aData[0]);
            });

            if ($.inArray('start_date', aKeys) === -1 || $.inArray('end_date', aKeys) === -1) {
                libDateUtil.setInitDate();
            } else {
                libDateUtil.setCalendarDate(libDateUtil.oStartDate.val(), libDateUtil.oEndDate.val());
            }

            if ($.inArray('selected_date', aKeys) !== -1) {
                libDateUtil.oBtnDate.removeAttr('class', 'btn-primary');
                libDateUtil.oBtnDate.addClass('btn btn-sm btn-secondary btnDate');
                $('#' + aValues['selected_date']).removeAttr('class', 'btn-secondary');
                $('#' + aValues['selected_date']).addClass('btn btn-sm btn-primary btnDate');
            } else {
                libDateUtil.oBtnDate.removeAttr('class', 'btn-primary');
                libDateUtil.oBtnDate.addClass('btn btn-sm btn-secondary btnDate');
            }
        },

        /**
         * Setting search conditions for order list
         */
        getOrderSearchFilters: function() {
            let aParams = {};

            let aFilters = oOrderList.getOrderAndProductFilter();
            if ($.isEmptyObject(aFilters) === false) {
                aParams['embed'] = aFilters;
            }

            /** Get selected date */
            let oSelectedStart = libDateUtil.oStartDate.val();
            let oSelectedEnd = libDateUtil.oEndDate.val();
            if (oSelectedStart > oSelectedEnd || oSelectedEnd < oSelectedStart) {
                libUtil.oToast.fire({
                    icon: 'error',
                    title: libDateUtil.INVALID_DATE_MSG
                });
                libDateUtil.setInitDate();
            }

            aParams['start_date'] = libDateUtil.oStartDate.val();
            aParams['end_date'] = libDateUtil.oEndDate.val();
            let sSelectedRange = $('.date-options').find('a.btn-primary').attr('id');
            if (typeof sSelectedRange !== 'undefined') {
                aParams['selected_date'] = sSelectedRange;
            }
            return aParams;
        },

        /**
         * Get selected product filter condition
         * @return object
         */
        getOrderAndProductFilter: function() {
            let aEmbed = {};

            let sOrderOption = oOrderList.oSelectFilter.val();
            let sOrderValue = $.trim(oOrderList.oInputOrder.val());
            
            if (sOrderValue.length !== 0) {
                aEmbed[sOrderOption] = sOrderValue;
            }

            let sProductOption = oOrderList.oSelectProductFilter.val();
            let sProductValue = $.trim(oOrderList.oInputProduct.val());

            if (sProductValue.length !== 0) {
                aEmbed[sProductOption] = sProductValue;
            }

            return aEmbed;
        }

    };

    oOrderList.init();
});
