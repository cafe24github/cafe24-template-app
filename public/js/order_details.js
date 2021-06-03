$(function() {
    /**
     * Order Details
     */
    let oOrderDetails = function() {
        const oSelf = this;

        /**
         * init function
         */
        this.init = function() {
            this.cacheDOM();
            this.getOrderDetails();
        };

        /**
         * Cache DOM elements
         */
        this.cacheDOM = function() {
            this.oOrder = $('#order_basic_info');
            this.oItems = $('#items');
            this.oCustomer = $('#customer_info');
            this.oShipping = $('#shipping_info');

            this.oItems.find('tbody').on('click', '.saveProduct', this.saveProduct);

            this.oItems.find('tbody').on('click', '.deleteProduct', this.deleteProduct);
        };

        this.saveProduct = function (oEvent) {
            libToggle.toggleProduct('Save', $(oEvent.target));
        };

        this.deleteProduct = function (oEvent) {
            libToggle.toggleProduct('Delete', $(oEvent.target));
        };

        /**
         * Send AJAX request for order details
         */
        this.getOrderDetails = function() {
            let aParams = {'order': decodeURIComponent(window.location.search.substring(1)).split('=')[1]};
            libUtil.sendRequest('/order/info', aParams, 'GET').done(function(mResult) {
                if (mResult['error'] === true) {
                    libUtil.oToast.fire({
                        icon: 'error',
                        title: mResult['message']
                    });
                    return false;
                }
                oSelf.displayOrderDetails(mResult['order'], mResult['saved']);
            });
        };

        /**
         * Display order information (order ID, date ordered)
         * @param aOrderDetails
         * @param aSaved
         */
        this.displayOrderDetails = function(aOrderDetails, aSaved) {
            /** Order information */
            oSelf.oOrder.find('#order_id').text('Order Number: ' + aOrderDetails['order_id']);
            oSelf.oOrder.find('#date_ordered').text('Ordered on: ' + aOrderDetails['order_date']);

            /** Generate list of items*/
            oSelf.displayItems(aOrderDetails['items'], aSaved);
            /** Customer Information */
            oSelf.displayBuyer(aOrderDetails['buyer']);
            /** Receiver's Information */
            oSelf.displayReceiver(aOrderDetails['receivers'][0]);
        };

        /**
         * Display buyer information
         * @param oBuyer
         */
        this.displayBuyer =  function(oBuyer) {
            oSelf.oCustomer.find('#buyer').text(oBuyer['member_id']);
            oSelf.oCustomer.find('#buyer_email').text(oBuyer['email']);
            oSelf.oCustomer.find('#buyer_phone').text(oBuyer['phone']);
            oSelf.oCustomer.find('#buyer_mobile').text(oBuyer['cellphone']);
        };

        /**
         * Display receiver / shipping information
         * @param oReceiver
         */
        this.displayReceiver = function(oReceiver) {
            oSelf.oShipping.find('#receiver_name').text(oReceiver['name']);
            oSelf.oShipping.find('#rec_japan_name').text(oReceiver['name_furigana']);
            oSelf.oShipping.find('#receivers_phone').text(oReceiver['phone']);
            oSelf.oShipping.find('#receiver_mobile').text(oReceiver['cellphone']);
            oSelf.oShipping.find('#country').text(oReceiver['address_state']);
            oSelf.oShipping.find('#city').text(oReceiver['address_city']);
            oSelf.oShipping.find('#province').text(oReceiver['address_state']);
            oSelf.oShipping.find('#address').text(oReceiver['address_full']);
        };

        /**
         * Display ordered items
         * @param oItems
         * @param oSavedProducts
         */
        this.displayItems = function(oItems, oSavedProducts) {
            if (oItems.length < 1) {
                oSelf.oItems.find('tbody').append('<td colspan="8">No available data</td>');
                return false;
            }
            var iTotal = 0, iPrice = 0;
            var bSaved = false;
            $.each(oItems, function(sKey, oItem){
                let sItemTemplate = $('.item-template').clone().prop('hidden', false);
                sItemTemplate.removeAttr('class');
                sItemTemplate.find('.item_code').append('<strong>' + oItem['order_item_code'] + '</strong>' + '<p>' + oItem['shipping_code'] + '</p>');
                sItemTemplate.find('.prod_code').text(oItem['product_code']);
                sItemTemplate.find('.prod_name').text(oItem['product_name']);
                sItemTemplate.find('.prod_variant').text(oItem['variant_code']);
                sItemTemplate.find('.qty').text(oItem['quantity']);
                sItemTemplate.find('.price').text(oSelf.formatNumbers(parseFloat(oItem['product_price']).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')));
                let iSubTotal = parseInt(oItem['quantity'], 10) * parseInt(oItem['product_price'], 10);
                iTotal += iSubTotal
                iPrice += parseInt(oItem['product_price'], 10);
                sItemTemplate.find('.sub_total').text(oSelf.formatNumbers(parseFloat(iSubTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')));
                oSelf.oItems.find('tbody').append(sItemTemplate);
                $.each(oSavedProducts, function(iKey, aValue) {
                    if (aValue['product_no'] === oItem['product_no']) {
                        bSaved = true;
                    }
                });

                if (bSaved === false) {
                    sItemTemplate.find('.mark_unmark').append('<a type="button" class="btn btn-secondary saveProduct" data-id="' + oItem['product_no'] + '"><i class="fa fa-eye"></i></a>');
                } else {
                    sItemTemplate.find('.mark_unmark').append('<a type="button" class="btn btn-primary deleteProduct" data-id="' + oItem['product_no'] + '"><i class="fa fa-eye"></i></a>');
                }
            });
            oSelf.oItems.find('tbody').append('<tr><td colspan="7"><strong>Total</strong></td><td><strong>' + oSelf.formatNumbers(parseFloat(iTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')) + '</strong></td></tr>');

        };

        /**
         * Format number to x,xxx.xx
         * @param mixed mNumber
         * @return formatted number
         */
        this.formatNumbers = function(mNumber) {
            var aNum = mNumber.toString().split('.');
            aNum[0] = aNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return aNum.join('.');
        };
    };

    let oDetails = new oOrderDetails();
    oDetails.init();
});
