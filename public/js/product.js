$(function() {
    /**
     * Product list manager
     */
    let oProductList = function () {
        const oSelf = this;
        /**
         * Use to initialize events
         */
        this.init = function () {
            this.initData();
            this.setEvent();
        };

        /**
         * Use to initialize data
         */
        this.initData = function () {
            oProductList.aWholeCategory = [];
            oProductList.aParameters = [];
            this.setCategories();
        };

        /**
         * Use to bind events
         */
        this.setEvent = function () {
            getConstant.oSelCategory.change(this.changeCategory);
            getConstant.oProductTable.on('click', '.deleteProduct', this.deleteProduct);
            getConstant.oProductTable.on('click', '.saveProduct', this.saveProduct);
            getConstant.oFilterItem.on('change', this.setSearchParameter);
            getConstant.oBtnReset.on('click', this.resetFilters);
        };

        /**
         * Use to toggle product
         * @param sType
         * @param oElement
         */
        this.toggleProduct = function (sType, oElement) {
            let mProductNo = oElement.data('id');
            if (mProductNo === undefined) {
                oElement = oElement.parent();
                mProductNo = oElement.data('id');
            }

            const oToggle = {
                oSave : {
                    oButton : libToggle.getToggleButton('untoggle', mProductNo),
                    oHttpVerb : 'POST'
                },
                oDelete : {
                    oButton : libToggle.getToggleButton('toggle', mProductNo),
                    oHttpVerb : 'DELETE'
                },
            };
            let oParams = sType === 'Save' ? oToggle['oSave'] : oToggle['oDelete'];
            oElement.addClass('disabled');
            libUtil.sendRequest('/product/' + mProductNo, null, oParams.oHttpVerb).done(function(mResponse) {
                let sIcon = mResponse.error === true ? 'error' : 'success';
                libUtil.oToast.fire({
                    icon: sIcon,
                    title: mResponse.message
                });

                oElement.removeClass('disabled');
            }).then(function(mResponse) {
                if (mResponse.error === true) {
                    oSelf.limitSavedProduct(mResponse.data);
                    return;
                }

                oElement.replaceWith(oParams.oButton);
                oSelf.limitSavedProduct(mResponse.data);
            }).fail(function() {
                oSelf.toggleProduct(sType, oElement);
            });
        };

        /**
         * Use to check if the current saved in the db reach its max for products
         * @param iCount
         */
        this.limitSavedProduct = function (iCount) {
            const iLimit = parseInt(getConstant.oTxtLimit.val(), 10);
            // Need to declare again the selector because there are dynamically added elements
            const oSelector = $('.saveProduct');
            if (iCount < iLimit) {
                oSelector.removeClass('disabled');
                return;
            }

            oSelector.addClass('disabled');
        };

        /**
         * Use to delete product in app db
         * @param oEvent
         */
        this.deleteProduct = function (oEvent) {
            oSelf.toggleProduct('Delete', $(oEvent.target));
        };

        /**
         * Use to save product in app db
         * @param oEvent
         */
        this.saveProduct = function (oEvent) {
            oSelf.toggleProduct('Save', $(oEvent.target));
        };

        /**
         * Action when category select is changed
         * @returns {boolean}
         */
        this.changeCategory = function () {
            const oSelect = $(this);
            const iDepth = parseInt(oSelect.data('depth'), 10);
            const iNextDepth = iDepth + 1;
            const iSelectedCategoryNo = parseInt(oSelect.find('option:selected').val(), 10);
            if (iDepth === 4) {
                return false;
            }

            oSelf.resetCategoryField(iDepth);
            const oNextCategory = $('#selSubCategory' + iNextDepth);
            const aCategory = oSelf.getCategoryByParent(iSelectedCategoryNo);
            const oCategoryFields = oSelf.createCategoryFields(aCategory);
            const sCategoryText = oSelf.getCategoryText(iNextDepth);
            const oOptionHeader = oSelf.createSelectedField(sCategoryText);
            oNextCategory.html('');
            oNextCategory.append(oOptionHeader);
            oNextCategory.append(oCategoryFields.html());
        };

        /**
         * Create selected field for category
         * @param sCategoryText
         * @returns {string}
         */
        this.createSelectedField = function (sCategoryText) {
            return '<option value="0" selected>' + sCategoryText + '</option>';
        };

        /**
         * Reset category field downwards
         * @param iDepth
         */
        this.resetCategoryField = function (iDepth) {
            for (let iCounter = iDepth + 1; iCounter <= 4; iCounter++) {
                const sCategoryText = this.getCategoryText(iCounter);
                const oOptionField = oSelf.createSelectedField(sCategoryText);
                $('#selSubCategory' + iCounter).html(oOptionField);
            }
        };

        /**
         * Use to get the default category text by depth
         * @param iDepth
         * @returns {string}
         */
        this.getCategoryText = function (iDepth) {
            switch(iDepth) {
                case 1:
                    return 'Main Category';
                case 2:
                    return 'Sub Category 1';
                case 3:
                    return 'Sub Category 2';
                case 4:
                    return 'Sub Category 3';
            }
        };

        /**
         * Add selected category
         */
        this.addSelectedCategory = function() {
            for (let iCounter = 4; iCounter >= 1; iCounter--) {
                const mCategoryNo = $('#selSubCategory' + iCounter).val();
                if (parseInt(mCategoryNo, 10) === 0) {
                    continue;
                }

                oSelf.addInFormParams('category', mCategoryNo);
                return;
            }
        };

        /**
         * Set categories after loading http request
         */
        this.setCategories = function () {
            libUtil.sendRequest('/categories', null, 'GET').done(function(oResponse) {
                oProductList.aWholeCategory = oResponse['data']['categories'];
            }).then(function() {
                const aMainCategory = oSelf.getCategoryByDepth(1);
                const oCategoryFields = oSelf.createCategoryFields(aMainCategory);
                getConstant.oSelMainCategory.append(oCategoryFields.html());
                oSelf.setSearchParameter();
                oSelf.setSelectedCategories();
                getConstant.oBtnSearch.removeClass('disabled');
                getConstant.oBtnReset.removeClass('disabled');
                $('.filter').removeAttr('disabled');
            });
        };

        /**
         * Set selected Categories base on current set in query parameter
         */
        this.setSelectedCategories = function () {
            const mSelectedCategory = oSelf.getSearchedParameter('category');
            if (mSelectedCategory === false) {
                return;
            }

            const iSelectedCategory = parseInt(mSelectedCategory, 10);
            $.each(oProductList.aWholeCategory, function (iKey, oVal) {
                if (oVal['category_no'] === iSelectedCategory) {
                    oSelf.setSelectedCategory(oVal['full_category_no']);
                }
            });

        };

        /**
         * Use to set the categories base on the filter
         * @param oCategory
         */
        this.setSelectedCategory = function (oCategory) {
            $.each(oCategory, function (iKey, oVal) {
                if (oVal === null) {
                    return;
                }

                $('#selSubCategory' + iKey).val(oVal).change();
            });
        };

        /**
         * Set search parameter
         */
        this.setSearchParameter = function () {
            oSelf.getSearchParameter();
            let sQueryString = objectToQueryString(oProductList.aParameters);
            getConstant.oBtnSearch.attr('href', '?' + sQueryString);
        };

        /**
         * Convert object into query string
         * @param oItem
         * @returns {string}
         */
        function objectToQueryString(oItem) {
            let oContainer = [];
            for (let iIndex in oItem) {
                if (oItem.hasOwnProperty(iIndex) === true) {
                    oContainer.push(encodeURIComponent(oItem[iIndex]['sKey']) + '=' + encodeURIComponent(oItem[iIndex]['sValue']));
                }
            }

            return oContainer.join('&');
        }

        /**
         * Use to get specific search parameter
         * @param sKey
         * @returns {string|boolean}
         */
        this.getSearchedParameter = function (sKey) {
            const mResults = new RegExp('[\?&]' + sKey + '=([^&#]*)').exec(window.location.href);
            if (mResults == null){
                return false;
            }

            return decodeURI(mResults[1]) || 0;
        };

        /**
         * Use to get the search parameter
         */
        this.getSearchParameter = function () {
            oSelf.aParameters = [];
            oSelf.addClassificationParam();
            oSelf.addDisplayStatusParam('selling');
            oSelf.addDisplayStatusParam('display');
            oSelf.addSelectedCategory();
        };

        /**
         * Use to add parameters in form data
         * @param sKey
         * @param sValue
         */
        this.addInFormParams = function (sKey, sValue) {
            const mExisting = libUtil.checkKeyInJson(oProductList.aParameters, sKey);
            if (mExisting !== false) {
                oProductList.aParameters.splice(mExisting, 1);
            }

            oProductList.aParameters.push({
                sKey : sKey,
                sValue : sValue
            });
        };

        /**
         * Add classification filters in  parameters
         */
        this.addClassificationParam = function () {
            const sClassificationType = getConstant.oSelClassification.val();
            const sClassificationText = getConstant.oTxtClassification.val();
            if (sClassificationText.trim().length === 0) {
                return;
            }

            oSelf.addInFormParams(sClassificationType, sClassificationText);
        };

        /**
         * Add display filter in parameters
         * @param sType
         */
        this.addDisplayStatusParam = function (sType) {
            let sTempType = getConstant.sDisplayStatusName;
            if (sType === 'selling') {
                sTempType = getConstant.sSellingStatusName;
            }

            const aValidStatus = ['A', 'T', 'F'];
            const sStatus = $('input[name="' + sTempType + '"]:checked').val();
            if (aValidStatus.indexOf(sStatus) === -1) {
                return;
            }

            if (sStatus === 'A') {
                return;
            }

            oSelf.addInFormParams(sType, sStatus);
        };

        /**
         * Get category using its depth
         * @param iDepth
         * @returns {[]}
         */
        this.getCategoryByDepth = function (iDepth) {
            let aTempCategory = [];
            oProductList.aWholeCategory.forEach(function(oCategory) {
                if (oCategory['category_depth'] === iDepth) {
                    aTempCategory.push(oCategory);
                }
            });

            return aTempCategory;
        };

        /**
         * Get the category using its parent number
         * @param iCategoryNo
         * @returns {[]}
         */
        this.getCategoryByParent = function (iCategoryNo) {
            let aTempCategory = [];
            oProductList.aWholeCategory.forEach(function(oCategory) {
                if (oCategory['parent_category_no'] === iCategoryNo) {
                    aTempCategory.push(oCategory);
                }
            });

            return aTempCategory;
        };

        /**
         * Use to create category fields
         * @param aCategory
         * @returns {MediaStream | Response | MediaStreamTrack | Request | *}
         */
        this.createCategoryFields = function (aCategory) {
            const oSelCategoryTemplate = getConstant.oSelCategoryTemplate.clone();
            aCategory.forEach(function(oCategory) {
                let oTemplate = getConstant.oSelCategoryOptionTemplate.find('option').clone();
                const sCategoryName = oCategory['category_name'];
                const iCategoryNumber = oCategory['category_no'];
                oTemplate.html(sCategoryName);
                oTemplate.val(iCategoryNumber);
                oSelCategoryTemplate.append(oTemplate);
            });

            return oSelCategoryTemplate;
        };

        /**
         * Use to reset current filters
         */
        this.resetFilters = function () {
            getConstant.oTxtClassification.val('');
            getConstant.oSelClassification.val('product_name');
            getConstant.oSelMainCategory.val('0').change();
            $('input:radio[name=radDisplayStatus][value=A]').click();
            $('input:radio[name=radSellingStatus][value=A]').click();
            oProductList.aParameters = [];
            oSelf.setSearchParameter();
        }
    };

    oProductList = new oProductList();
    oProductList.init();
});
