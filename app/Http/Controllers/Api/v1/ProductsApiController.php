<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Service\Api\v1\ProductsApiService;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

/**
 * Class ProductsApiController
 * @package App\Http\Controllers\Product
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 12/1/2020 12:19 PM
 */
class ProductsApiController extends Controller
{
    /**
     * ProductsApiController constructor.
     * @param ProductsApiService $oService
     */
    public function __construct(ProductsApiService $oService)
    {
        $this->oService = $oService;
    }

    /**
     * Returns all the products from the API base on the parameter given
     * @param Request $aRequest
     * @return mixed
     */
    public function getProducts(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getProducts($aParams);
    }

     /** Use to Count Product
     * @param Request $aRequest
     * @return mixed
     */
    public function countProduct(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->countProduct($aParams);
    }

    /**
     * Use to save product
     * @param Request $aRequest
     * @return array
     */
    public function saveProduct(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->saveProduct($aParams);
    }

    /**
     * Use to get the data for the current page
     * @param Request $aRequest
     * @return array
     */
    public function getProductPageData(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getProductPageData($aParams);
    }

    /**
     * Use to delete product
     * @param         $sProductNo
     * @param Request $aRequest
     * @return array
     */
    public function deleteProduct($sProductNo, Request $aRequest)
    {
        $aParams = $aRequest->all();
        $aParams['product_no'] = (int) $sProductNo;
        return $this->oService->deleteProduct($aParams);
    }

    /**
     * Use to get product save in app database using product number
     * @param         $sProductNo
     * @param Request $aRequest
     * @return array|mixed
     */
    public function getProductByNo($sProductNo, Request $aRequest)
    {
        $aParams = $aRequest->all();
        $iProductNo = (int) $sProductNo;
        return $this->oService->getProductByNo($iProductNo, $aParams);
    }

    /**
     * Use to get all saved product
     * @param Request $aRequest
     * @return array
     */
    public function getSavedProduct(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getSavedProducts($aParams);
    }

    /**
     * Use to get the current script tag status
     * @param Request $aRequest
     * @return array
     * @throws \App\Service\Exception\CStoreException
     */
    public function getScriptTagStatus(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getScriptTagStatus($aParams);
    }
    /**
     * Use to toggle script tag
     * @param Request $aRequest
     * @return array
     */
    public function toggleScriptTag(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->toggleScriptTag($aParams);
    }

    /**
     * Returns all the bundle product from the API base on the parameter given
     * @param Request $aRequest
     * @return mixed
     */
    public function getBundleProducts(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getBundleProducts($aParams);
    }
}
