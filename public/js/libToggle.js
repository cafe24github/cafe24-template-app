/**
 * Toggle library
 * @type {{oToast: *, checkKeyInJson: *, getToggleButton: *, sendRequest: *}}
 */
const libToggle = function () {
    /**
     * Use to get Toggle button
     * @param sType
     * @param sId
     * @returns {string}
     */
    function getToggleButton(sType, sId) {
        if (sType === 'toggle') {
            return '<a type="button" class="btn btn-secondary saveProduct" data-id="' + sId + '"><i class="fa fa-eye-slash"></i></a>';
        }

        return '<a type="button" class="btn btn-primary deleteProduct" data-id="' + sId + '"><i class="fa fa-eye"></i></a>';
    }

    function toggleProduct(sType, oElement) {
        let mProductNo = oElement.attr('data-id');
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
        });
    }

    return {
        getToggleButton : getToggleButton,
        toggleProduct : toggleProduct
    }
}();


