<?php

namespace App\Http\Controllers;

use App\Service\App\ProductService;
use Illuminate\Http\Request;

/**
 * Class ProductController
 * @package App\Http\Controllers
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class ProductController extends BaseController
{
    /**
     * @var ProductService
     */
    private $oProductService;

    /**
     * ProductController constructor.
     * @param ProductService $oProductService
     */
    public function __construct(ProductService $oProductService)
    {
        $this->oProductService = $oProductService;
    }

    /**
     * @param Request $aRequest
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function getProducts(Request $aRequest)
    {
        $oData = $this->oProductService->getDataToDisplay($aRequest->all());
        return view('products', $oData);
    }

    /**
     * Save product in app database
     * @param $iProductNo
     * @return array|mixed
     */
    public function saveProductApp($iProductNo)
    {
        return $this->oProductService->saveProduct($iProductNo);
    }

    /**
     * Use to get product by product number in app database
     * @param $iProductNo
     * @return mixed
     */
    public function getProductByNo($iProductNo)
    {
        return $this->oProductService->getProductByNo($iProductNo);
    }

    /**
     * Use to delete product by product number in app database
     * @param $iProductNo
     * @return mixed
     */
    public function deleteProduct($iProductNo)
    {
        return $this->oProductService->deleteProduct($iProductNo);
    }

    /**
     * Use to toggle script tag
     * @return mixed
     */
    public function toggleScriptTag()
    {
        return $this->oProductService->toggleScriptTag();
    }
    /**
     * Use to toggle script tag
     * @return mixed
     */
    public function getScriptTagStatus()
    {
        return $this->oProductService->getScriptTagStatus();
    }
}
