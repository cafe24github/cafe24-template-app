/**
 * Use to get constant values
 * @type {{oSelCategoryOptionTemplate: *, oSaveProductBtn: *, oSelCategoryTemplate: *, oBtnSearch: *, oSelMainCategory: *, oDeleteProductBtn: *, oSelCategory: *, oSaveBtnTemplate: *, sSellingStatusName: *, oDeleteBtnTemplate: *, oSelClassification: *, oBtnReset: *, sDisplayStatusName: *, oTxtClassification: *, oProductTable: *, oFilterItem: *}}
 */
const getConstant = function () {
    const oSelCategoryTemplate = $('#selCategoryTemplate');
    const oSelCategoryOptionTemplate = $('#selCategoryOptionTemplate');
    const oSelMainCategory = $('#selSubCategory1');
    const oSelCategory = $('.selCategory');
    const oSaveProductBtn = $('.saveProduct');
    const oDeleteProductBtn = $('.deleteProduct');
    const oProductTable = $('#tblProduct');
    const oDeleteBtnTemplate = $('#btnDeleteTemplate');
    const oSaveBtnTemplate = $('#btnSaveTemplate');
    const oSelClassification = $('#selClassification');
    const oTxtClassification = $('#txtClassification');
    const oBtnSearch = $('#btnSearch');
    const oBtnReset = $('#btnReset');
    const oTxtLimit = $('#txtLimit');
    const oFilterItem = $('.filter');
    const sDisplayStatusName = 'radDisplayStatus';
    const sSellingStatusName = 'radSellingStatus';

    return {
        oSelCategoryOptionTemplate : oSelCategoryOptionTemplate,
        oSelCategoryTemplate       : oSelCategoryTemplate,
        oSelMainCategory           : oSelMainCategory,
        oSelCategory               : oSelCategory,
        oSaveProductBtn            : oSaveProductBtn,
        oDeleteProductBtn          : oDeleteProductBtn,
        oProductTable              : oProductTable,
        oDeleteBtnTemplate         : oDeleteBtnTemplate,
        oSaveBtnTemplate           : oSaveBtnTemplate,
        oSelClassification         : oSelClassification,
        oTxtClassification         : oTxtClassification,
        oBtnSearch                 : oBtnSearch,
        oBtnReset                  : oBtnReset,
        oFilterItem                : oFilterItem,
        oTxtLimit                  : oTxtLimit,
        sDisplayStatusName         : sDisplayStatusName,
        sSellingStatusName         : sSellingStatusName
    }
}();



