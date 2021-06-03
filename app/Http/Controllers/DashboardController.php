<?php

namespace App\Http\Controllers;

use App\Service\App\ProductService;
use Illuminate\Http\Request;


/**
 * Class DashboardController
 *
 * @author jean <jean@cafe24corp.com>
 * @version 1.0
 * @date 2/3/2021 08:40 AM
 */
class DashboardController extends Controller
{
    /**
     * @var ProductService
     */
    private $oProductService;

    /**
     * DashboardController constructor.
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
    public function getMarkedProducts(Request $aRequest)
    {
        $aData = $this->oProductService->getMarkedProducts();
        return view('dashboard')->with('aData', $aData);
    }
}
