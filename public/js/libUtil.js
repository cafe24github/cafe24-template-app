/**
 * Functionality that can be use in all pages are in here
 * @type {{oToast: *, sendRequest: *}}
 */
const libUtil = function () {
    /**
     * Use to make an http request
     * @param sUrl
     * @param mData
     * @param sType
     * @returns {*}
     */
    function sendRequest(sUrl, mData, sType) {
        return $.ajax({
            url: sUrl,
            type: sType,
            data: mData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            statusCode: {
                403: function() {
                    window.location.href = '/error';
                }
            }
        });
    }

    /**
     * Use to make a toast alert
     */
    const oToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: function (oToast) {
            oToast.addEventListener('mouseenter', Swal.stopTimer);
            oToast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    /**
     * Check if key passed is existing in a json
     * @param aArray
     * @param sKey
     * @returns {boolean}
     */
    function checkKeyInJson(aArray, sKey) {
        let mFlag = false;
        aArray.forEach(function(oItem, iIndex) {
            if (oItem['sKey'] === sKey) {
                mFlag = iIndex;
            }
        });

        return mFlag;
    }

    return {
        sendRequest     : sendRequest,
        oToast          : oToast,
        checkKeyInJson  : checkKeyInJson
    }
}();


