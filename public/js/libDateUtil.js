/**
 * Date library
 */
var libDateUtil = {

    /** error message for invalid dates */
    INVALID_DATE_MSG: 'Selected dates are invalid',

    /**
     * Initial function
     */
    init: function() {
        this.oStartCal = $('#start_date_cal');
        this.oEndCal = $('#end_date_cal');
        this.oStartDate = $('#start');
        this.oEndDate = $('#end');
        this.oBtnDate = $('.btnDate');

        this.oBtnDate.click(function() {
            libDateUtil.setSelectedDate($(this).attr('id'));
            libDateUtil.oBtnDate.removeAttr('class', 'btn-primary');
            libDateUtil.oBtnDate.addClass('btn btn-sm btn-secondary btnDate');
            $(this).removeAttr('class', 'btn-secondary');
            $(this).addClass('btn btn-sm btn-primary btnDate');
        });
    },

    /**
     * Set default selected date
     */
    setInitDate : function() {
        var oInitialStart = new Date();
        oInitialStart.setDate(oInitialStart.getDate() + 1);
        oInitialStart.setMonth(oInitialStart.getMonth() - 3);
        libDateUtil.oStartDate.val(libDateUtil.formatDate(oInitialStart));

        var oInitialEndDate = libDateUtil.formatDate(new Date());
        libDateUtil.oEndDate.val(oInitialEndDate);
        libDateUtil.setCalendarDate(libDateUtil.formatDate(oInitialStart), oInitialEndDate);
        libDateUtil.setDefaultDateRange();
    },

    /**
     * Set default date range button
     */
    setDefaultDateRange: function() {
        libDateUtil.oBtnDate.addClass('btn btn-sm btn-secondary btnDate');
        $('#month_3').removeAttr('class', 'btn-secondary');
        $('#month_3').addClass('btn btn-sm btn-primary btnDate');
    },

    /**
     * Set start and end dates based on the selected range
     * @param string sDate
     */
    setSelectedDate: function(sDateRange) {
        let oEndDate = libDateUtil.oEndDate.val();
        let oSelected = sDateRange.split('_');
        let oDate = new Date(oEndDate);

        if (oSelected[0] === 'day') {
            oDate.setDate(oDate.getDate() - parseInt(oSelected[1], 10));
            libDateUtil.oStartDate.val(libDateUtil.formatDate(oDate));
        }

        if (oSelected[0] === 'month') {
            oDate.setDate(oDate.getDate() + 1);
            oDate.setMonth(oDate.getMonth() - parseInt(oSelected[1], 10));
            libDateUtil.oStartDate.val(libDateUtil.formatDate(oDate));
        }

        libDateUtil.setCalendarDate(libDateUtil.oStartDate.val(), oEndDate);
    },

    /**
     * Set calendar's selected date
     * @param date oStartDat
     * @param date oEndDate
     */
    setCalendarDate: function(oStartDate, oEndDate) {
        libDateUtil.oStartCal.datepicker('setDate', oStartDate);
        libDateUtil.oEndCal.datepicker('setDate', oEndDate);
    },

    /**
     * Check if selected dates are valid
     * @param  obj oCalendar
     * @param  obj oDate
     */
    validateDate: function(oCalendar, mDate) {
        let oEndDate = new Date(libDateUtil.oEndDate.val());
        let oStart = new Date(libDateUtil.oStartDate.val());


        if (oCalendar.attr('id') === 'start_date_cal') {
            if (libDateUtil.formatDate(mDate) > libDateUtil.formatDate(oEndDate)) {
                libDateUtil.oStartDate.val(libDateUtil.formatDate(oEndDate));
                oCalendar.datepicker('setDate', libDateUtil.formatDate(oEndDate));
                return;
            }
            libDateUtil.oStartDate.val(libDateUtil.formatDate(mDate));
        }

        if (oCalendar.attr('id') === 'end_date_cal') {
            if (libDateUtil.formatDate(mDate) < libDateUtil.formatDate(oStart)) {
                libDateUtil.oEndDate.val(libDateUtil.formatDate(oStart));
                oCalendar.datepicker('setDate', libDateUtil.formatDate(oStart));
                return;
            }
            libDateUtil.oEndDate.val(libDateUtil.formatDate(mDate));
        }
    },

    /**
     * Format date to yyyy-mm-dd
     * @param  mixed mDate
     * @return formatted date
     */
    formatDate: function(mDate) {
        var sYear,
            sMonth,
            sDay;

        sYear = mDate.getFullYear().toString();
        sMonth = (mDate.getMonth() + 1).toString();
        sDay = mDate.getDate().toString();

        if (sMonth.length < 2) {
            sMonth = '0' + sMonth;
        }

        if (sDay.length < 2) {
            sDay = '0' + sDay;
        }

        return [
            sYear,
            sMonth,
            sDay
        ].join('-');
    }
};


libDateUtil.init();

