var oPagination = {
    BLOCK_LIMIT: 10,
    REQUEST_LIMIT: 10,
    TOTAL_PAGE: 1,

    /**
     * Populate pagination elements
     * @param iCount
     * @param oModule
     */
    setPageNumbers: function(iCount, oModule) {
        this.TOTAL_PAGE = Math.ceil(parseInt(iCount, 10) / this.REQUEST_LIMIT);

        if (this.TOTAL_PAGE === 1) {
            oModule.oPagination.twbsPagination('destroy');
            return;
        }

        oModule.oPagination.twbsPagination({
            totalPages: this.TOTAL_PAGE,
            initiateStartPageClick: false,
            prev: '<span aria-hidden="true">&laquo;</span>',
            next: '<span aria-hidden="true">&raquo;</span>',
            first: '',
            last: '',
            onPageClick: function(oEvent, iPage) {
                oModule.loadOtherPages(iPage);
            }
        });
    },
};