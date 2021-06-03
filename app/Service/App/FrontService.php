<?php

namespace App\Service\App;

use App\Library\AppEndpointLibrary;
use App\Library\ConstantsLibrary;
use App\Library\GuzzleLibrary;
use App\Library\libPaging;
use App\Service\BaseService;
use App\Service\Exception\ProductException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

/**
 * Class FrontService
 * @package App\Service\App
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class FrontService extends BaseService
{
    /**
     * @var GuzzleLibrary $oGuzzleLibrary
     */
    private $oGuzzleLibrary;

    /**
     * ProductService constructor.
     * @param GuzzleLibrary $oGuzzleLibrary
     */
    public function __construct(GuzzleLibrary $oGuzzleLibrary)
    {
        $this->oGuzzleLibrary = $oGuzzleLibrary;
    }

    /**
     * Use to get javascript file for script tag
     * This function will replace the values inside template-app.js with our env values
     * and this endpoint will return as a javascript
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getScriptTag()
    {
        $sScript = File::get('./js/template-app.js');
        $sText = $this->modifyUrl($sScript);

        $aHeaders = [
            'Content-Type'  => 'application/javascript',
            'Cache-Control' => 'public, max-age=86400',
            'Accept-Ranges' => 'bytes'
        ];

        return Response::stream(function() use($sText) {
            echo $sText;
        }, 200, $aHeaders);
    }

    /**
     * Alter the env values in js file
     * @param $sScript
     * @return string
     */
    private function modifyUrl($sScript)
    {
        $sClientId = env('CLIENT_ID');
        $aExplodedJS = explode('ENV_CLIENT_ID', $sScript);
        $aExplodedJS = $aExplodedJS[0] . $sClientId . $aExplodedJS[1];
        $aExplodedJS = explode('ENV_APP_URL', $aExplodedJS);
        $aExplodedJS = $aExplodedJS[0] . env('APP_URL') . $aExplodedJS[1];
        $aExplodedJS = explode('ENV_API_VERSION', $aExplodedJS);
        return $aExplodedJS[0] . env('CAFE_24_API_VERSION') . $aExplodedJS[1];
    }

    /**
     * Get saved product in database
     * @param $aParam
     * @return array
     */
    public function getSavedProduct($aParam)
    {
        $this->setRedisParams($aParam);
        $aProduct = $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_SAVED_PRODUCT)->guzzleApi($this->oRedisParams);
        return $this->getOnlyProductNo($aProduct);
    }

    /**
     * Use to get script tag status
     * @param $aParam
     * @return mixed
     */
    public function getScriptTagStatus($aParam)
    {
        $this->setRedisParams($aParam);
        return $this->oGuzzleLibrary->setEndpoint(AppEndpointLibrary::GET_SCRIPTTAG_STATUS)->guzzleApi($this->oRedisParams);
    }

}
