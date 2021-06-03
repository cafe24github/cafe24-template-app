<?php

namespace App\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class GuzzleLibrary
 * @package App\Library
 *
 * @author joven <joven@cafe24corp.com>,
 * @version 1.0
 * @date 12/3/2020 4:24 PM
 */
class GuzzleLibrary
{
    /**
     * [URL] End point to use for
     * API requests
     * @var string $sEndpoint
     */
    private $sEndpoint;

    /**
     * Return value of guzzle
     * @var array|string $mGuzzleResponse
     */
    private $mGuzzleResponse;

    /**
     * Generate a Guzzle/HTTP request.
     * @param $sUri
     * @param $aRequest
     * @param $sRequestType
     * @return array|string
     */
    static function requestGuzzle($sRequestType, $sUri, $aRequest)
    {
        try {
            $oClient = new Client(['verify' => false]);
            $oResponse = $oClient->request($sRequestType, $sUri, $aRequest);
            $mResponse = $oResponse->getBody()->getContents();
        } catch (GuzzleException $e) {
            return self::requestGuzzle($sRequestType, $sUri, $aRequest);
        }
        return $mResponse;
    }

    /**
     * Set API endpoint
     * @param  string $sEndpoint
     * @return $this
     */
    public function setEndpoint($sEndpoint)
    {
        $this->sEndpoint = $sEndpoint;
        return $this;
    }


    /**
     * @param array  $aRequest
     * @param array  $aHeaders
     * @param $sRequestBodyType
     * @return $this
     */
    private function setLogs(array $aRequest, array $aHeaders, $sRequestBodyType)
    {
        Log::info($this->mGuzzleResponse);
        Log::info([
            'request'   => $aRequest,
            'headers'   => $aHeaders,
            'body_type' => $sRequestBodyType
        ]);
        return $this;
    }


    /**
     * Send Request to API
     *
     * @param array  $aParams
     * @param string $sMethod
     * @return mixed
     */
    public function guzzleApi($aParams = [], $sMethod = 'GET')
    {
        $sUri = env('API_URL') . $this->sEndpoint;
        $aParams = [
            'json' => $aParams
        ];

        $this->mGuzzleResponse = GuzzleLibrary::requestGuzzle($sMethod, $sUri, $aParams);
        $this->setLogs($aParams, $aHeaders = [], $sRequestBodyType = '');
        if (is_array($this->mGuzzleResponse) === true) {
            return $this->mGuzzleResponse;
        }

        return json_decode($this->mGuzzleResponse, true);
    }

}
