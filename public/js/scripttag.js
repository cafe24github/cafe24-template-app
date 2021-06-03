$(function() {
    /**
     * Script tag manager
     */
    let oScriptTag = function () {
        const oSelf = this;
        /**
         * Use to initialize events
         */
        this.init = function () {
            this.initData();
            this.setDOMElements();
            this.setEvent();
        };

        /**
         * Use to initialize data
         */
        this.initData = function () {
            this.getScriptTagStatus();
        };

        /**
         * Get the script tag current status
         */
        this.getScriptTagStatus = function () {
            libUtil.sendRequest('/scripttag/status', null, 'GET').done(function(oResponse) {
                oSelf.oToggle.removeAttr('disabled');
                oSelf.oToggle.attr('checked', oResponse.data);
            }).fail(function() {
                oSelf.getScriptTagStatus();
            });
        };

        /**
         * Set the dom elements that will be used
         */
        this.setDOMElements = function () {
            this.oToggle = $('#chkInstallApp');
        };

        /**
         * Use to bind events
         */
        this.setEvent = function () {
            oSelf.oToggle.change(this.toggleApp);
        };

        /**
         * Use to toggle script tag
         */
        this.toggleApp = function () {
            oSelf.oToggle.attr('disabled', 'disabled');
            libUtil.sendRequest('/scripttag', null, 'POST').done(function(oResponse) {
                oSelf.oToggle.removeAttr('disabled');
            }).fail(function() {
                oSelf.toggleApp();
            });
        }

    };

    oScriptTag = new oScriptTag();
    oScriptTag.init();
});
