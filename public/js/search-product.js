$(function() {
    var oProduct = {
        aProductList: [],
        iDefaultPage: 1,
        bFilters: false,
        sSearchKey: '',

        /**
         * Initial function for product search page
         */
        initProduct: function() {
            $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
            });

            this.sSearchKey = decodeURIComponent(window.location.search.substring(1)).split('=')[1];
            this.DOMElements();
            this.getProducts(oProduct.iDefaultPage);
        },

        /**
         * Cache DOM Elements and bind event into it
         * @constructor
         */
        DOMElements: function() {
            this.oSearchFilter = $('#select_product_filter');
            this.oProductValue = $('#input_filter_value');
            this.oBtnSearchProduct = $('#btnSearch');

            this.oProductTable = $('#product_table');
            this.oProductTemplate = $('#product_template');
            this.oPagination = $('.pagination');

            this.oProductTable.on('click', '.selected_product', function() {
                oProduct.getProductDetail($(this));
            });

            this.oBtnSearchProduct.click(function() {
                oProduct.bSearch = true;
                oProduct.getProducts(oProduct.iDefaultPage);
            });
        },

        /**
         * Get product list (search product)
         * @param  int iPage
         * @param  obj aParams
         */
        getProducts: function(iPage) {
            let aParams = {};
            if (oProduct.bSearch === true) {
                aParams = oProduct.setSearchFilters();
            }
            aParams['page'] = iPage;
            libUtil.sendRequest('/productList', aParams, 'POST').done(function(oProducts) {
                if (oProducts['error'] === true) {
                    libUtil.oToast.fire({
                        icon: 'error',
                        title: oProducts['message']
                    });
                    return false;
                }
                oProduct.aProductList = oProducts['products'];
            }).then(function(oProducts) {
                oProduct.getBundleProducts(oProducts);
            });
        },

        /**
         * Get the bundled products
         * @param oProducts
         */
        getBundleProducts: function(oProducts) {
            let aProductCodes = [];
            $.each(oProducts['products'], function(iKey, oProdDetails) {
                aProductCodes.push(oProdDetails['product_code']);
            });
            libUtil.sendRequest('/bundle', {'products': aProductCodes}, 'POST').done(function(oResult) {
                if (oResult['error'] === true) {
                    libUtil.oToast.fire({
                        icon: 'error',
                        title: oResult['message']
                    });
                    return false;
                }
                $.each(oProduct.aProductList, function(iKey) {
                    oProduct.aProductList[iKey]['bundle_product'] = oResult['bundle'][iKey];
                });
                oProduct.displayList(oProduct.aProductList);
            }).then(function() {
                oPagination.setPageNumbers(oProducts['count'], oProduct);
            });
        },

        /**
         * Populate rows for product result
         * @param  object oProducts
         * @param  int iPage
         */
        displayList: function(oProducts) {
            oProduct.oProductTable.find('tbody').empty();
            if (oProducts.length < 1) {
                $('ul.pagination').empty();
                oProduct.oProductTable.find('tbody').append('<tr><td colspan="4">No data available</td></tr>');
                return false;
            }
            $.each(oProducts, function(iKey, oProdDetails) {
                let sTemplate = oProduct.oProductTemplate.clone().prop('hidden', false);
                sTemplate.removeAttr('id');
                sTemplate.attr('id', oProdDetails['variants'][0]['variant_code']);
                sTemplate.find('.code').text(oProdDetails['product_code']);
                sTemplate.find('.name').text(oProdDetails['product_name']);
                sTemplate.find('.bundle').text(oProdDetails['bundle_product']);
                sTemplate.find('.bundle').next().text((oProdDetails['options'] === null) ? 'F' : oProdDetails['options']['has_option']);
                sTemplate.find('.selected_product').append('<a class="btn btn-sm btn-primary">Select</a>');
                oProduct.oProductTable.find('tbody').append(sTemplate);
            });
        },

        /**
         * Load another set of products (for pagination purposes)
         * @param  int iPage
         */
        loadOtherPages: function(iPage) {
            oProduct.getProducts(iPage);
        },

        /**
         * Set product search filters
         * @return array
         */
        setSearchFilters: function() {
            let sProduct = $.trim(oProduct.oProductValue.val());
            let aParams = {};

            if (sProduct !== '') {
                aParams[oProduct.oSearchFilter.val()] = sProduct;
            }
            return aParams;
        },

        /**
         * Get product detail based on the search jkey (product code, product name or variant code)
         * @param  obj oElement
         */
        getProductDetail: function(oElement) {
            let sProductInfo = '';
            if (oProduct.sSearchKey === 'product_code') {
                sProductInfo = oElement.closest('tr').find('.code').text()
            } else if (oProduct.sSearchKey === 'variant_code') {
                sProductInfo = oElement.closest('tr').attr('id');
            }

            window.opener.$('#input_product_value').val(sProductInfo);
            self.close();
        }
    };

    oProduct.initProduct();
});