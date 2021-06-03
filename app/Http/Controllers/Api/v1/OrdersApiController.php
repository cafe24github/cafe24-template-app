<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Service\Api\v1\OrdersApiService;
use Illuminate\Http\Request;

/**
 * Class OrdersApiController
 * @package App\Http\Controllers\Api\v1
 *
 * @author kenneth03 <kenneth03@cafe24corp.com.ph>
 * @version 1.0
 * @date 12/4/2020 1:24 PM
 */
class OrdersApiController extends Controller
{
    /**
     * @var OrdersApiService
     */
    private $oOrdersApiService;

    /**
     * OrdersApiController constructor.
     * @param OrdersApiService $oOrdersApiService
     */
    function __construct(OrdersApiService $oOrdersApiService)
    {
        $this->oOrdersApiService = $oOrdersApiService;
    }

    /**
     * Returns all the orders from the API based on the parameter given
     * @param Request $oRequest
     * @return mixed
     */
    public function getOrderPage(Request $oRequest)
    {
        $aParams = $oRequest->all();
        return $this->oOrdersApiService->getOrderPage($aParams);
    }

    /**
     * Returns all the orders from the API based on the parameter given
     * @param Request $oRequest
     * @return mixed
     */
    public function getAllOrders(Request $oRequest)
    {
        $aParams = $oRequest->all();
        return $this->oOrdersApiService->getAllOrders($aParams);
    }

    /**
     * Returns the details of a specific order
     * @param Request $oRequest
     * @param $iOrderId
     * @return array|mixed
     */
    public function getOrderByParams($iOrderId, Request $oRequest)
    {
        $aParams = $oRequest->all();
        return $this->oOrdersApiService->getOrderByParams($iOrderId, $aParams);
    }

    /**
     * This function passes parameters to Service class for updating of order details.
     * @param Request $oRequest
     * @param $iOrderId
     * @return array|mixed
     */
    public function updateOrder($iOrderId, Request $oRequest)
    {
        $aParams = $oRequest->all();
        return $this->oOrdersApiService->updateOrder($iOrderId, $aParams);
    }

    /**
     * Returns the count of orders listed based on the parameters given
     * @param Request $oRequest
     * @return mixed
     */
    public function getOrderCount(Request $oRequest)
    {
        $aParams = $oRequest->all();
        return $this->oOrdersApiService->getOrderCount($aParams);
    }
}
