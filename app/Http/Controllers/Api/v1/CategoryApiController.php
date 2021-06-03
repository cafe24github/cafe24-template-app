<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Service\Api\v1\CategoryApiService;
use App\Service\Api\v1\ProductsApiService;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

/**
 * Class CategoryApiController
 * @package App\Http\Controllers\Api\v1
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class CategoryApiController extends Controller
{
    /**
     * ProductsApiController constructor.
     * @param CategoryApiService $oService
     */
    public function __construct(CategoryApiService $oService)
    {
        $this->oService = $oService;
    }

    /**
     * Use to get Categories
     * @param Request $aRequest
     * @return mixed
     */
    public function getCategories(Request $aRequest)
    {
        $aParams = $aRequest->all();
        return $this->oService->getCategories($aParams);
    }

}
