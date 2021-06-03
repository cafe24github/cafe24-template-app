<?php

namespace App\Service\App;

use App\Library\AppEndpointLibrary;
use App\Library\ConstantsLibrary;
use App\Library\GuzzleLibrary;
use App\Library\libPaging;
use App\Service\BaseService;
use App\Service\Exception\ProductException;
use Illuminate\Http\Request;

/**
 * Class ProductService
 * @package App\Service\Auth
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class ProductService extends BaseService
{
    /**
     * Valid parameter to accept
     */
    const VALID_PARAMS = [
        'page',
        'limit',
        'product_code',
        'product_name',
        'selling',
        'display',
        'category',
        'offset'
    ];

    /**
     * @var GuzzleLibrary $oGuzzleLibrary
     */
    private $oGuzzleLibrary;

    /**
     * Contains parameter for filter of product
     * @var array
     */
    private $oParameter = [];

    /**
     * ProductService constructor.
     * @param GuzzleLibrary $oGuzzleLibrary
     */
    public function __construct(GuzzleLibrary $oGuzzleLibrary)
    {
        $this->oGuzzleLibrary = $oGuzzleLibrary;
    }

    /**
     * Get product list
     * @return mixed
     */
    public function getProductList()
    {
        $this->setRedisParams();
        $aParam = array_merge($this->oRedisParams, $this->oParameter);
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_PRODUCTS)->guzzleApi($aParam);
    }

    /**
     * Count products
     * @return mixed
     */
    public function countProduct()
    {
        $this->setRedisParams();
        $aParam = array_merge($this->oRedisParams, $this->oParameter);
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::COUNT_PRODUCT)->guzzleApi($aParam);
    }

    /**
     * Use to get data for the current page
     * @return mixed
     */
    public function getProductPageData()
    {
        $this->setRedisParams();
        $aParam = array_merge($this->oRedisParams, $this->oParameter);
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::PAGE_DATA_PRODUCT)->guzzleApi($aParam);
    }


    /**
     * Get data that will be use in blade file
     * @param $aParams
     * @return array
     */
    public function getDataToDisplay($aParams)
    {
        $iPage = $this->getPage($aParams);
        $iOffset = ConstantsLibrary::DEFAULT_LIMIT * ((int) $iPage - 1);
        $aParams['offset'] = $iOffset;
        $this->setSearchParameters($aParams);
        $oData = $this->getProductPageData();
        $iCount = $oData['iCount'];
        $oPaging = $this->getPaging($iPage, $iCount);
        $oDefaultImage = ConstantsLibrary::PROD_IMAGE_DEFAULT;

        return [
            'oPaging'            => $oPaging,
            'aProduct'           => $oData['aProduct'],
            'iCount'             => $iCount,
            'oDefaultImage'      => $oDefaultImage,
            'iOffset'            => $iOffset,
            'iCountSavedProduct' => $oData['iCountSavedProduct'],
            'aSavedProduct'      => $oData['aSavedProduct'],
            'oParams'            => $this->oParameter
        ];
    }

    /**
     * Get marked products
     * @return array
     */
    public function getMarkedProducts()
    {
        $aMarkedProduct = [];
        $oDefaultImage = ConstantsLibrary::PROD_IMAGE_DEFAULT;

        $oMarkedProducts = $this->getSavedProduct();
        foreach($oMarkedProducts as $markedProduct) {
            $aData = $this->getProductByNo($markedProduct);
            $aData['products'][0]['default_image'] = $oDefaultImage;
            $aMarkedProduct[] = $aData['products'][0];
        }

        return $aMarkedProduct;
    }

    /**
     * Get pagination
     * @param $iPage
     * @param $iCount
     * @return string
     */
    private function getPaging($iPage, $iCount)
    {
        if ($iCount === 0) {
            return '';
        }

        return libPaging::getHtml($iPage, $iCount, ConstantsLibrary::DEFAULT_LIMIT, $this->oParameter);
    }


    /**
     * Use to set search parameter in product
     * @param $aParams
     */
    private function setSearchParameters($aParams)
    {
        foreach ($aParams as $sKey => $sValue) {
            if (in_array($sKey, self::VALID_PARAMS) === false) {
                continue;
            }

            $this->oParameter[$sKey] = $sValue;
        }
    }

    /**
     * Use to save product inside app db
     * @param $iProductNo
     * @return array|mixed
     */
    public function saveProduct($iProductNo)
    {
        $this->setRedisParams();
        $sEndpoint = sprintf(AppEndpointLibrary::SAVE_PRODUCT, $iProductNo);
        $this->oRedisParams['product_no'] = $iProductNo;
        return $this->oGuzzleLibrary->setEndpoint($sEndpoint)->guzzleApi($this->oRedisParams);
    }

    /**
     * Get all saved product
     * @return array
     */
    public function getSavedProduct()
    {
        $this->setRedisParams();
        $aProduct = $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_SAVED_PRODUCT)->guzzleApi($this->oRedisParams);
        return $this->getOnlyProductNo($aProduct);
    }

    /**
     * Get the script tag status
     * @return mixed
     */
    private function getAppInstalled()
    {
        $this->setRedisParams();
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_SCRIPTTAG_STATUS)->guzzleApi($this->oRedisParams);
    }

    /**
     * Get a product using product number
     * @param $iProductNo
     * @return mixed
     */
    public function getProductByNo($iProductNo)
    {
        $this->setRedisParams();
        $sEndpoint = sprintf(AppEndpointLibrary::GET_PRODUCT, $iProductNo);
        return $this->oGuzzleLibrary->setEndpoint($sEndpoint)->guzzleApi($this->oRedisParams);
    }

    /**
     * Delete a product in app db
     * @param $iProductNo
     * @return mixed
     */
    public function deleteProduct($iProductNo)
    {
        $this->setRedisParams();
        $sEndpoint = sprintf(AppEndpointLibrary::DELETE_PRODUCT, $iProductNo);
        return $this->oGuzzleLibrary->setEndpoint($sEndpoint)->guzzleApi($this->oRedisParams, ConstantsLibrary::DELETE);
    }

    /**
     * Use to toggle script tag in redis
     * @return mixed
     */
    public function toggleScriptTag()
    {
        $this->setRedisParams();
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::POST_SCRIPTTAG)->guzzleApi($this->oRedisParams, ConstantsLibrary::POST);
    }

    /**
     * Use to get script tag status
     * @return mixed
     */
    public function getScriptTagStatus()
    {
        $this->setRedisParams();
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_SCRIPTTAG_STATUS)->guzzleApi($this->oRedisParams);
    }

}
