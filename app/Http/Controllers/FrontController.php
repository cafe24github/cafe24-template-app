<?php

namespace App\Http\Controllers;

use App\Service\App\FrontService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/**
 * Class FrontController
 * @package App\Http\Controllers
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/10/2021 9:35 AM
 */
class FrontController extends Controller
{
    /**
     * @var FrontService
     */
    private $oFrontService;

    /**
     * FrontController constructor.
     * @param FrontService $oFrontService
     */
    public function __construct(FrontService $oFrontService)
    {
        $this->oFrontService = $oFrontService;
    }

    /**
     * Use to get javascript file
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getScript()
    {
        return $this->oFrontService->getScriptTag();
    }

    /**
     * Use to get saved product
     * @param Request $aRequest
     * @return array
     */
    public function getSavedProduct(Request $aRequest)
    {
        return $this->oFrontService->getSavedProduct($aRequest->all());
    }

    /**
     * Get the script tag status in the current mall
     * @param Request $aRequest
     * @return mixed
     */
    public function getScriptTagStatus(Request $aRequest)
    {
        return $this->oFrontService->getScriptTagStatus($aRequest->all());
    }

}
