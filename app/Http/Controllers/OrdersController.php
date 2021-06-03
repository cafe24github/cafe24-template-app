<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Service\App\OrderService;

/**
 * Class OrdersController
 * @package App\Http\Controllers
 *
 * @author marican <marican@cafe24corp.com.ph>
 * @version 1.0
 * @date 01/25/2021 1:24 PM
 */
class OrdersController extends Controller
{
    /**
     * $oOrderService
     * @var obj
     */
    private $oOrderService;

    /**
     * __construct
     * @param OrderService $oService [description]
     */
    public function __construct(OrderService $oService)
    {
        $this->oOrderService = $oService;
    }

    /**
     * Display order list table
     * @param  Request $oRequest
     * @return view
     */
    public function displayList(Request $oRequest)
    {
        $oData = $this->oOrderService->getOrdersToDisplay($oRequest->all());
        return view('orders', $oData);
    }

    /**
     * Search product page
     * @return view
     */
    public function searchProduct()
    {
        return view('order_search_prod');
    }

    /**
     * Show order details page
     * @return view
     */
    public function showOrderDetails()
    {
        return view('order_details');
    }

    /**
     * Get list of products
     * @param  Request $oRequest
     * @return mixed
     */
    public function getProductList(Request $oRequest)
    {
        return response()->json($this->oOrderService->getProducts($oRequest->all()));
    }

    /**
     * Get bundle products
     * @param Request $oRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBundle(Request $oRequest)
    {
        return response()->json($this->oOrderService->getBundleProducts($oRequest->all()));
    }

    /**
     * Get list of products
     * @param  Request $oRequest
     * @return mixed
     */
    public function getOrderDetails(Request $oRequest)
    {
        return response()->json($this->oOrderService->getOrderDetails($oRequest->all()));
    }
}
