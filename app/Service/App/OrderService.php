<?php

namespace App\Service\App;

use App\Library\AppEndpointLibrary;
use App\Library\ConstantsLibrary;
use App\Library\GuzzleLibrary;
use App\Service\BaseService;
use App\Library\libPaging;
use Carbon\Carbon;

/**
 * Class OrderService
 * @package App\Service
 * @author marican <marican@cafe24corp.com.ph>
 * @version 1.0
 * @since 01/27/2021 9:40 AM
 */
class OrderService extends BaseService
{
    /**
     * Limit per request
     * @var int
     */
    const REQUEST_LIMIT = 10;

    /**
     * Default custom_variant_code value
     * @var string
     */
    const DEFAULT_CUSTOM_VARIANT = 'default';

    /**
     * Default embed value for product search
     */
    const EMBED_VARIANT = 'variants';

    /**
     * Default embedded params in getting the order details
     * @var array
     */
    const ORDER_DETAILS_EMBED = ['embed' => ['items', 'buyer', 'receivers']];

    /**
     * $oCurrentDate
     * @var obj
     */
    private $oCurrentDate;

    /**
     * $aParams
     * @var array
     */
    private $aParams;

    /**
     * @var GuzzleLibrary $oGuzzleService
     */
    private $oGuzzleService;


    /**
     * $aValidSearchEmbed
     * @var array
     */
    private $aValidSearchEmbed = ['item_code', 'variant_code'];

    /**
     * OrderService constructor.
     * @param $oGuzzleService
     */
    public function __construct(GuzzleLibrary $oGuzzleService)
    {
        $this->oGuzzleService = $oGuzzleService;
        $this->oCurrentDate = Carbon::now()->timezone('Asia/Seoul');
    }

    /**
     * Get order list and total count
     * @param $aFilters
     * @return array|mixed
     */
    public function getOrdersToDisplay($aFilters)
    {
        $aDate = [];
        if (array_key_exists('selected_date', $aFilters) === true) {
            $aDate['selected_date'] = $aFilters['selected_date'];
            unset($aFilters['selected_date']);
        }
        $this->aParams = $aFilters;
        $aOrders = $this->getOrders($aFilters);

        if ($aOrders['error'] === true) {
            return [$aOrders, 'oParams'   => array_merge($aDate, $aFilters)];
        }

        $iPage = $this->getPage($aFilters);
        $iCount = $aOrders['data']['count'];

        return [
            'oOrders'   => $aOrders['data']['orders'],
            'oPaging'   => $this->getPaging($iPage, $iCount),
            'oParams'   => array_merge($aDate, $aFilters)
        ];
    }

    /**
     * Get order list and total count
     * @param $aParam
     * @return array|mixed
     */
    public function getBundleProducts($aParam)
    {
        $aSearchParams = $this->setDefaultParams();
        $aSearchParams['fields'] = 'product_no,product_code';
        $aSearchParams['product_code'] = implode(',', $aParam['products']);
        $aBundleList = $this->oGuzzleService->setEndpoint(AppEndpointLibrary::BUNDLE_PRODUCT)
            ->guzzleApi(array_merge($aSearchParams, $aParam), ConstantsLibrary::GET);

        if (array_key_exists('error', $aBundleList) === true) {
            return [
                'error'     => true,
                'message'   => $aBundleList['error']['message']
            ];
        }
        $aBundleProducts = [];
        foreach ($aParam['products'] as $sProductCode) {
            $bBundled = 'F';
            foreach ($aBundleList['bundleproducts'] as $aBundle) {
                if ($sProductCode === $aBundle['product_code']) {
                    $bBundled = 'T';
                }
            }
            array_push($aBundleProducts, $bBundled);
        }

        return [
            'error'    => false,
            'bundle'   => $aBundleProducts
        ];
    }

    /**
     * Get list of products
     * @param  array  $aParams
     * @return array
     */
    public function getProducts($aParams = [])
    {
        $aSearchParams = $this->setProductSearchFilter($aParams);
        $mResponse = $this->oGuzzleService->setEndpoint(AppEndpointLibrary::COUNT_PRODUCT)
            ->guzzleApi($aSearchParams, ConstantsLibrary::GET);
        if ($mResponse['error'] === true) {
            return $mResponse;
        }
        $aProductList = $this->oGuzzleService->setEndpoint(AppEndpointLibrary::GET_PRODUCTS)
            ->guzzleApi($aSearchParams, ConstantsLibrary::GET);
        if (array_key_exists('error', $aProductList) === true) {
            return ['error' => true, 'message' => $aProductList['error']['message']];
        }

        return [
            'error'     => false,
            'products'  => $aProductList['products'],
            'count'     => $mResponse['data']['count']
        ];
    }

    /**
     * Get details of a specific order
     * @param  array $aParams
     * @return array
     */
    public function getOrderDetails($aParams)
    {
        if (array_key_exists('order', $aParams) === false) {
            return [
                'error'   => true,
                'message' => ConstantsLibrary::ORDER_ID_ERROR
            ];
        }
        $this->setRedisParams();
        $aSearchParams = array_merge($this->setDefaultParams(), self::ORDER_DETAILS_EMBED);
        $sOrderEndpoint = sprintf(AppEndpointLibrary::ORDER_DETAILS, $aParams['order']);

        $aDetails = $this->oGuzzleService->setEndpoint($sOrderEndpoint)->guzzleApi($aSearchParams, ConstantsLibrary::GET);
        if ($aDetails['error'] === true) {
            return $aDetails;
        }
        $aSaved = $this->oGuzzleService->setEndpoint(AppEndpointLibrary::GET_SAVED_PRODUCT)->guzzleApi($this->oRedisParams, ConstantsLibrary::GET);
        if ($aSaved['error'] === true) {
            return $aSaved;
        }

        return [
            'error' => false,
            'order' => $aDetails['data']['order'],
            'saved' => $aSaved['data']
        ];
    }

    /**
     * Get list of orders with pagination
     * @param  array  $aFilters
     * @return mixed
     */
    public function getOrders($aFilters)
    {
        $aParams = $this->setRequiredParams($aFilters);
        return $this->oGuzzleService->setEndpoint(AppEndpointLibrary::GET_COUNT_ORDERS)
            ->guzzleApi($aParams, ConstantsLibrary::GET);
    }

    /**
     * Get the actual count of orders
     * @param  array $aSearchParam
     * @return array
     */
    public function countOrders($aSearchParam)
    {
        $aParams = $this->setRequiredParams($aSearchParam);
        return $this->oGuzzleService->setEndpoint(AppEndpointLibrary::COUNT_ORDERS)
            ->guzzleApi($aParams, ConstantsLibrary::GET);

    }

    /**
     * Set product search filter
     * @param array $aParams
     * @return array
     */
    private function setProductSearchFilter($aParams)
    {
        $aSearchParams = $this->setDefaultParams();
        if (count($aParams) > 0) {
            foreach ($aParams as $sKey => $mValue) {
                $aSearchParams[$sKey] = rawurldecode($mValue);
            }
        }

        if (array_key_exists('page', $aParams) === true) {
            $aSearchParams['offset'] = ((int) $aParams['page'] - 1) * (self::REQUEST_LIMIT);
            unset($aSearchParams['page']);
        }

        if (array_key_exists('product_code', $aParams) === true || array_key_exists('product_name', $aParams) === true) {
            $aSearchParams['custom_product_code'] = self::DEFAULT_CUSTOM_VARIANT;
        } else {
            $aSearchParams['embed'] = self::EMBED_VARIANT;
        }

        return $aSearchParams;
    }

    /**
     * Set required search parameters (filters, date, offset, embed, etc)
     * @param array $aFilters
     * @return array
     */
    private function setRequiredParams($aFilters)
    {
        $aParams = $this->setDefaultParams();

        if (array_key_exists('start_date', $aFilters) === false &&
            array_key_exists('end_date', $aFilters) === false) {
            $aParams += $this->setDefaultDate();
        } else {
            $aParams['start_date'] = $aFilters['start_date'];
            $aParams['end_date'] = $aFilters['end_date'];
        }

        if (array_key_exists('page', $aFilters) === true) {
            $aParams['offset'] = ((int)$aFilters['page'] - 1) * (self::REQUEST_LIMIT);
            unset($aParams['page']);
        }

        $aEmbed = $this->setEmbedParams($aFilters);
        if (array_key_exists('filters', $aEmbed) === true) {
            $aParams += $aEmbed['filters'];
        }

        $aParams['embed'] = $aEmbed['embed_key'];
        return $aParams;
    }

    /**
     * Set embed params for searching of orders
     * @param array $aParams
     * @return array
     */
    private function setEmbedParams($aParams)
    {
        $aDefaultEmbed = ['items', 'buyer'];
        if (array_key_exists('embed', $aParams) === false) {
            return ['embed_key' => $aDefaultEmbed];
        }

        foreach ($aParams['embed'] as $sEmbedKey => $sEmbedVal) {
            if (in_array($sEmbedKey, $this->aValidSearchEmbed) === true) {
                array_push($aDefaultEmbed, $sEmbedKey);
            }
        }

        return [
            'embed_key' => $aDefaultEmbed,
            'filters'   => $aParams['embed']
        ];
    }


    /**
     * Set defaukt date parameters (3 months range)
     * @return array
     */
    private function setDefaultDate()
    {
        return [
            'start_date' => Carbon::now()->timezone('Asia/Seoul')->subMonths(3)->addDays(1)->toDateString(),
            'end_date'   => $this->oCurrentDate->toDateString()
        ];
    }

    /**
     * Set mall_id and shop_no as a default search parameters
     * @return array
     */
    private function setDefaultParams()
    {
        return [
            'mall_id'  => session()->get('mall_id'),
            'shop_no'  => session()->get('shop_no')
        ];
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

        return libPaging::getHtml($iPage, $iCount, ConstantsLibrary::DEFAULT_LIMIT, $this->aParams);
    }
}
