<?php

namespace App\Library;

use App\Repository\App\ProductRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Library for CStoreV2API Functions
 * @author jon<jon@cafe24corp.com>
 * @since: 11/09/2018
 * @version: 1
 */
class CStoreLibrary
{

    /**
     * Access Token
     * @var array $aToken
     */
    private $aToken;

    /**
     * @var $oRepository
     */
    private $oRepository;

    /**
     * Use to flag and check if the api wants to check if the endpoint is cached in the db
     * @var bool
     */
    public $bCache = false;

    /**
     * Resets value for access token
     * @param $mNewToken
     */
    public function setToken($mNewToken)
    {
        $this->aToken = $mNewToken;
    }

    /**
     * gets all products with limit and offset
     * @param $iLimit
     * @param $iOffset
     * @return mixed
     */
    public function getAllProducts($iLimit, $iOffset)
    {
        return $this->runRequest('get', 'products?limit=' . $iLimit . '&offset=' . $iOffset . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets count of categories
     * @return mixed
     */
    public function getCategoryCount()
    {
        return $this->runRequest('get', 'categories/count?display=T&selling=T');
    }

    /**
     * gets product categories
     * @param $iLimit
     * @return mixed
     */
    public function getProductCategories($iLimit = '')
    {
        return $this->runRequest('get', 'categories?limit=100');
    }

    /**
     * gets product count
     * @param $sParam
     * @return mixed
     */
    public function getProductCount($sParam = '')
    {
        return $this->runRequest('get', 'products/count?' . $sParam);
    }

    /**
     * gets products by name
     * @param $iLimit
     * @param $iOffset
     * @param $sSearchString
     * @return mixed
     */
    public function getProductsByName($iLimit, $iOffset, $sSearchString)
    {
        return $this->runRequest('get', 'products?&limit=' . $iLimit . '&offset=' . $iOffset . '&product_name=' . $sSearchString . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets product count by name
     * @param $sSearchString
     * @return mixed
     */
    public function getProductCountByName($sSearchString)
    {
        return $this->runRequest('get', 'products/count?product_name=' . $sSearchString . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets products by category
     * @param $iLimit
     * @param $iOffset
     * @param $iCategoryNum
     * @return mixed
     */
    public function getProductsByCategory($iLimit, $iOffset, $iCategoryNum)
    {
        return $this->runRequest('get', 'products?&limit=' . $iLimit . '&offset=' . $iOffset . '&category=' . $iCategoryNum . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets product count by category
     * @param $iCategoryNum
     * @return mixed
     */
    public function getProductCountByCategory($iCategoryNum)
    {
        return $this->runRequest('get', 'products/count?category=' . $iCategoryNum . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets products by tags
     * @param $iLimit
     * @param $iOffset
     * @param $sTag
     * @return mixed
     */
    public function getProductsByTags($iLimit, $iOffset, $sTag)
    {
        return $this->runRequest('get', 'products?&limit=' . $iLimit . '&offset=' . $iOffset . '&product_tag=' . $sTag . ConstantsLibrary::DISPLAY_SELLING);
    }

    /**
     * gets product count by tags
     * @param $sTag
     * @return mixed
     */
    public function getProductCountByTags($sTag)
    {
        return $this->runRequest('get', 'products/count?product_tag=' . $sTag);
    }

    /**
     * get products by date
     * @param $iLimit
     * @param $iOffset
     * @param $sStartDate
     * @param $sEndDate
     * @return mixed
     */
    public function getProductsByDate($iLimit, $iOffset, $sStartDate, $sEndDate)
    {
        return $this->runRequest('get', 'products?&limit=' . $iLimit . '&offset=' . $iOffset . '&created_start_date=' . $sStartDate . '&created_end_date=' . $sEndDate);
    }

    /**
     * gets product count by date
     * @param $sStartDate
     * @param $sEndDate
     * @return mixed
     */
    public function getProductCountByDate($sStartDate, $sEndDate)
    {
        return $this->runRequest('get', 'products/count?created_start_date=' . $sStartDate . '&created_end_date=' . $sEndDate);
    }

    /**
     * get products by the given parameter
     * @param $sParam
     * @return mixed
     */
    public function getProducts($sParam = '')
    {
        return $this->runRequest('get', 'products?' . $sParam);
    }

    /**
     * get multiple products
     * @param $sParam
     * @return mixed
     */
    public function getProductByProductNumber($sParam)
    {
        return $this->runRequest('get', 'products?product_no=' . $sParam);
    }

    /**
     * returns the number of the products from the filtered search
     * @param $sParam
     * @return mixed
     */
    public function getFilteredCount($sParam)
    {
        return $this->runRequest('get', 'products/count?' .ConstantsLibrary::DISPLAY_SELLING .$sParam);
    }

    /**
     * uploads the image to mall storage
     * @param $sImage
     * @return mixed
     */
    public function uploadImage($sImage)
    {
        return $this->runRequest('POST', 'products/images', ['image' => $sImage]);
    }

    /**
     * get shop
     * @return mixed
     */
    public function getShop()
    {
        return $this->runRequest('get', ConstantsLibrary::SHOP_SETTING);
    }

    /**
     * Get Categories
     * @return mixed
     */
    public function getCategories()
    {
        return $this->runRequest('get', ConstantsLibrary::SHOP_SETTING);
    }

    /**
     * Gets Orders
     * @param $sParam
     * @return mixed
     */
    public function getOrders($sParam)
    {
        return $this->runRequest('GET', 'orders?'. $sParam);
    }

    /**
     * Gets a Specific Order by Request Parameters
     * @param $sParam
     * @param $iOrderId
     * @return mixed
     */
    public function getOrderByParams($iOrderId, $sParam)
    {
        return $this->runRequest('GET', 'orders/' . $iOrderId . '?' . $sParam);
    }

    /**
     * Updates a Specific Order
     * @param $iOrderId
     * @param $aParams
     * @return mixed
     */
    public function updateOrder($iOrderId, $aParams)
    {
        return $this->runRequest('PUT', 'orders/' . $iOrderId, $aParams['request'], $aParams['shop_no']);
    }

    /**
     * Gets Orders
     * @param $sParam
     * @return mixed
     */
    public function countOrders($sParam)
    {
        return $this->runRequest('GET', 'orders/count?'. $sParam);
    }

    /**
     * get products by the given parameter
     * @param $sParam
     * @return mixed
     */
    public function getBundleProducts($sParam = '')
    {
        return $this->runRequest('get', 'bundleproducts?' . $sParam);
    }

    /**
     * runs guzzle request
     * @param       $sMethod
     * @param       $sFields
     * @param array $aParams
     * @param int   $iShopNo
     * @return mixed
     */
    public function runRequest($sMethod, $sFields, $aParams = [], $iShopNo = 1)
    {
        $oClient = new Client();
        try {
            $aOptions = ['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . $this->aToken['access_token'], 'X-Cafe24-Api-Version' => config('auth.cafe24_version')],];
            if (strtoupper($sMethod) === 'POST' || strtoupper($sMethod) === 'PUT') {
                $aOptions['json'] = ['shop_no' => $iShopNo, 'request' => $aParams];
            }
            $sUri = 'https://' . $this->aToken['mall_id'] . '.cafe24api.com/api/v2/admin/' . $sFields;
            $aParam = [
                'endpoint' => $sUri,
                'method'   => $sMethod,
                'shop_no'  => $iShopNo,
                'mall_id'  => $this->aToken['mall_id']
            ];

            $mData = $this->checkInRedis($aParam, $sMethod);
            if ($mData !== false) {
                return json_decode($mData,true);

            }

            $oResult = $oClient->request($sMethod, $sUri, $aOptions);
            $sReturnBody = $oResult->getBody()->getContents();
            $aParam['return'] = $sReturnBody;
            $this->saveApiReturn($aParam,$sMethod);
        } catch (GuzzleException $oException) {
            $sReturnBody = $oException->getResponse()->getBody()->getContents();
        }

        return json_decode($sReturnBody, true);
    }

    /**
     * Use to save cache for cstore
     * @param $aParam
     * @param $sMethod
     * @return bool|void
     */
    private function saveApiReturn($aParam, $sMethod)
    {
        if (strtoupper($sMethod) !== 'GET') {
            return false;
        }

        if ($this->bCache === false) {
            return;
        }

        $this->oRepository->saveApiReturn($aParam);
    }

    /**
     * Use to check if the request is in cache
     * @param $aParam
     * @param $sMethod
     * @return bool
     */
    private function checkInRedis($aParam, $sMethod)
    {
        if (strtoupper($sMethod) !== 'GET') {
            return false;
        }

        if ($this->bCache === false) {
            return false;
        }

        $this->oRepository = new ProductRepository(new RedisLibrary());
        $mData = $this->oRepository->getApiReturn($aParam);
        if ($mData !== false) {
            return $mData['return'];
        }

        return false;
    }

    /**
     * Use to install script tag in mall
     * @param $sParam
     * @return mixed
     */
    public function installScriptTag($sParam)
    {
        return $this->runRequest('POST', 'scripttags', $sParam);
    }

    /**
     * Use to count script install in mall
     * @return mixed
     */
    public function countScriptTag()
    {
        return $this->runRequest('GET', 'scripttags/count');
    }

}
