<?php

namespace App\Service\Api\v1;

use App\Library\CStoreLibrary;
use App\Repository\Token\AccessTokenRepository;
use App\Service\BaseService;
use App\Validator\Api\v1\OrdersApiValidator;
use App\Service\Exception\CStoreException;
use Illuminate\Session\SessionManager;

/**
 * Class OrdersApiService
 * @package App\Service\Api\v1
 *
 * @author joven <joven@cafe24corp.com>, nico<nico@cafe24corp.com>, kenneth03 <kenneth03@cafe24corp.com>, marican <marican@cafe24corp.com>
 * @version 1.0
 * @date 12/11/2020 1:53 PM
 */
class OrdersApiService extends BaseService
{
    const ORDER_DEFAULT_FIELDS = '&fields=order_id,member_id,member_email,items,buyer';

    /**
     * OrdersApiService constructor.
     * @param CStoreLibrary         $oCStoreLibrary
     * @param SessionManager        $oSessionManager
     * @param AccessTokenRepository $oAccessTokenRepository
     * @param OrdersApiValidator    $oOrdersValidator
     */
    function __construct(CStoreLibrary $oCStoreLibrary, SessionManager $oSessionManager, AccessTokenRepository $oAccessTokenRepository, OrdersApiValidator $oOrdersValidator)
    {
        $this->oCStoreLibrary = $oCStoreLibrary;
        $this->oSession = $oSessionManager;
        $this->oTokenRepository = $oAccessTokenRepository;
        $this->oValidator = $oOrdersValidator;
    }

    /**
     * Returns order list and count
     * @param $aFilters
     * @return array|mixed
     */
    public function getOrderPage($aFilters)
    {
        $aCount = $this->getOrderCount($aFilters);
        if ($aCount['error'] === true) {
            return $aCount;
        }

        $aOrders = $this->getAllOrders($aFilters);
        if ($aOrders['error'] === true) {
            return $aOrders;
        }

        return [
            'error' => false,
            'data' => [
                'count'  => $aCount['data']['count'],
                'orders' => $aOrders['data']['orders']
            ]
        ];
    }

    /**
     * Service method for retrieving the list of orders
     * @param $aFilters
     * @return array|mixed
     */
    public function getAllOrders($aFilters)
    {
        try {
            if (array_key_exists('embed', $aFilters) === true) {
                $aFilters['embed'] = $this->setEmbedParams($aFilters['embed']);
            }
            $aParams = $this->generateListParameters($aFilters);
            $mValidationResult = $this->oValidator->getAllOrdersValidator($aParams);
            if ($mValidationResult->fails() === true) {
                return $this->setErrorResponse($mValidationResult->errors()->all());
            }
            $sUrlParameters = $this->generateGetUrlParameters($aParams, $this->oTokenRepository, $this->oCStoreLibrary);
            $this->setRedisParams($aParams)->setCStoreToken($this->oRedisParams);
            $sUrlParameters .= self::ORDER_DEFAULT_FIELDS;

            $mResponse = $this->oCStoreLibrary->getOrders($sUrlParameters);
            if ($this->validateApiResponse($mResponse) === false) {
                $this->saveRefreshedToken($aParams['shop_no'], $mResponse);
                return $this->getAllOrders($aFilters);
            }
            return $this->setAPIResponse($mResponse);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * This method sends API request in getting the details of a specific order
     * @param $iOrderId
     * @param $aParams
     * @return array|mixed
     */
    public function getOrderByParams($iOrderId, $aParams)
    {
        try {
            if (array_key_exists('embed', $aParams) === false) {
                $aParams['embed'] = self::DEFAULT_EMBED;
            }
            $mValidationResult = $this->oValidator->getOrderByParamsValidator($iOrderId, $aParams);
            if ($mValidationResult->fails() === true) {
                return $this->setErrorResponse($mValidationResult->errors()->all());
            }
            $sUrlParameters = 'embed=' . $this->setEmbedParams($aParams['embed']);
            unset($aParams['embed']);

            $sUrlParameters .= $this->generateGetUrlParameters($aParams, $this->oTokenRepository, $this->oCStoreLibrary);
            $sUrlParameters .= '&fields=order_id,order_date,buyer,items,receivers';
            $this->setRedisParams($aParams)->setCStoreToken($this->oRedisParams);

            $mResponse = $this->oCStoreLibrary->getOrderByParams($iOrderId, $sUrlParameters);
            if ($this->validateApiResponse($mResponse) === false) {
                $this->saveRefreshedToken($aParams['shop_no'], $mResponse);
                return $this->getOrderByParams($iOrderId, $aParams);
            }
            return $this->setAPIResponse($mResponse);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Sends request for the count of orders in the API
     * @param $aParams
     * @return array|mixed
     */
    public function getOrderCount($aParams)
    {
        try {
            $mValidationResult = $this->oValidator->countOrdersValidator($aParams);
            if ($mValidationResult->fails() === true) {
                return $this->setErrorResponse($mValidationResult->errors()->all());
            }
            $sUrlParameters = $this->generateGetUrlParameters($aParams, $this->oTokenRepository, $this->oCStoreLibrary);
            $this->setRedisParams($aParams)->setCStoreToken($this->oRedisParams);
            $mResponse = $this->oCStoreLibrary->countOrders($sUrlParameters);
            if ($this->validateApiResponse($mResponse) === false) {
                $this->saveRefreshedToken($aParams['shop_no'], $mResponse);
                return $this->getOrderCount($aParams);
            }
            return $this->setAPIResponse($mResponse);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * This method processes parameters and sends request for the updating of order details
     * @param $iOrderId
     * @param $aParams
     * @return array|mixed
     */
    public function updateOrder($iOrderId, $aParams)
    {
        $mValidationResult = $this->oValidator->updateOrderValidator($iOrderId, $aParams);

        if ($mValidationResult->fails() === true) {
            return $this->setErrorResponse($mValidationResult->errors()->all());
        }

        $aRequestParams = [
            'shop_no' => $aParams['shop_no'],
            'request' => [
                'process_status' => $aParams['request']['process_status'],
                'order_item_code' => array_key_exists('order_item_code', $aParams['request']) ? $aParams['request']['order_item_code'] : ''
            ]
        ];

        unset($aParams['mall_id'], $aParams['shop_no'], $aParams['request']);

        if ($aRequestParams['request']['order_item_code'] === '' || $aRequestParams['request']['order_item_code'] === null) {
            unset($aRequestParams['request']['order_item_code']);
        }
        $this->setRedisParams($aParams)->setCStoreToken($this->oRedisParams);
        return $this->oCStoreLibrary->updateOrder($iOrderId, $aParams);
    }

    /**
     * Set embed params for search orders
     * @param  mixed $mEmbed
     * @return string
     */
    private function setEmbedParams($mEmbed)
    {
        if (is_array($mEmbed) === true) {
            return implode(',', $mEmbed);
        }

        return $mEmbed;
    }

    /**
     * Validate api results
     * @param $mResult
     * @return bool
     */
    protected function validateApiResponse($mResult)
    {
        $aErrorCodes = [401, 429, 500, 503];
        if (is_array($mResult) === true && array_key_exists('error', $mResult) === true) {
            if (in_array($mResult['error']['code'], $aErrorCodes) === true) {
                return false;
            }
        }

        return true;
    }


    /**
     * Use to set success response from the API
     * @param mixed $mResponse
     * @return array
     */
    private function setAPIResponse($mResponse)
    {
        if (array_key_exists('error', $mResponse) === true) {
            return [
                'error'     => true,
                'message'   => $mResponse['error']['message']
            ];
        }

        return [
            'error'   => false,
            'message' => '',
            'data'    => $mResponse
        ];
    }
}
