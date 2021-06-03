<?php

namespace App\Service;

use App\Library\ConstantsLibrary;
use App\Library\CStoreLibrary;
use App\Repository\App\ProductRepository;
use App\Repository\Token\AccessTokenRepository;
use App\Repository\Token\GetAccessTokenRepository;
use App\Service\Exception\CStoreException;
use Cafe24ph\OAuth2\Client\Provider\Cafe24;

/**
 * Class BaseService
 * @package App\Service
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 11/27/2020 9:24 AM
 */
class BaseService implements ConstantsLibrary
{
    /**
     * Redis Library
     * @var $oRedisLibrary
     */
    protected $oRedisLibrary;

    /**
     * Repository
     * @var AccessTokenRepository
     */
    protected $oRepository;

    /**
     * @var CStoreLibrary
     */
    protected $oCStoreLibrary;

    /**
     * @var GetAccessTokenRepository
     */
    protected $oTokenRepository;

      /**
     * @var ProductRepository
     */
    protected $oProdRepository;

    /**
     * @var $oSession
     */
    protected $oSession;

    /**
     * @var $oValidator
     */
    protected $oValidator;

    /**
     * @var $oRedisParams
     */
    protected $oRedisParams;

    /**
     * This function generates the parameters for limit and offset if there is no default limit and offset set in parameters
     * @param $aParams
     * @return mixed
     */
    protected function generateListParameters($aParams)
    {
        $iLimit = 10;
        $iOffset = 0;

        if (isset($aParams['limit']) !== true) {
            $aParams['limit'] = $iLimit;
        }
        if (isset($aParams['offset']) !== true) {
            $aParams['offset'] = $iOffset;
        }

        return $aParams;
    }

    /**
     * Generates Get Url Parameters to be passed to API
     * @param $aParams
     * @param $oTokenRepository
     * @param $oCStoreLibrary
     * @return string
     */
    protected function generateGetUrlParameters($aParams, $oTokenRepository, $oCStoreLibrary)
    {
        $this->oTokenRepository = $oTokenRepository;
        $this->oCStoreLibrary = $oCStoreLibrary;

        $aRedisData = [
            'mall_id' => $aParams['mall_id'],
            'shop_no' => $aParams['shop_no']
        ];
        unset($aParams['mall_id'], $aParams['shop_no']);

        $aAccessToken = $this->oTokenRepository->getAccessToken($aRedisData);
        $this->oCStoreLibrary->setToken($aAccessToken);

        return http_build_query($aParams);
    }

    /**
     * Use to set error response
     * @param       $sErrorMessage
     * @param array $aOptionalData
     * @return array
     */
    protected function setErrorResponse($sErrorMessage, $aOptionalData = [])
    {
        $mResponse = [
            'error'   => true,
            'message' => $sErrorMessage,
            'data'    => $aOptionalData
        ];

        return $mResponse;
    }

    /**
     * Use to set redis params in $oRedisParams variable
     * Only use in app service
     * @param bool $mParams
     * @return $this
     */
    protected function setRedisParams($mParams = false)
    {
        $sMallId = session()->get(ConstantsLibrary::MALL_ID);
        $sUserId = session()->get(ConstantsLibrary::USER_ID);
        $sShopNo = session()->get(ConstantsLibrary::SHOP_NO);
        if ($mParams !== false) {
            $sMallId = $mParams[ConstantsLibrary::MALL_ID];
            $sShopNo = $mParams[ConstantsLibrary::SHOP_NO];
            $sUserId = null;
        }

        $this->oRedisParams = [
            ConstantsLibrary::MALL_ID => $sMallId,
            ConstantsLibrary::SHOP_NO => $sShopNo,
            ConstantsLibrary::USER_ID => $sUserId,
        ];

        return $this;
    }

    /**
     * Use to set success response from api
     * @param       $sMessage
     * @param array $aData
     * @return array
     */
    protected function setJsonResponse($sMessage, $aData = [])
    {
        return [
            'error'   => false,
            'message' => $sMessage,
            'data'    => $aData
        ];
    }

    /**
     * Validate api results
     * @param $aResult
     * @return bool
     */
    protected function validateApiReturn($aResult)
    {
        if (is_array($aResult) === true && array_key_exists('error', $aResult) === true) {
            return false;
        }

        return true;
    }

    /**
     * @param $aRedisData
     */
    protected function setCStoreToken($aRedisData)
    {
        $aAccessToken = $this->oTokenRepository->getAccessToken($aRedisData);
        $this->oCStoreLibrary->setToken($aAccessToken);
    }

    /**
     * @param $oResponse
     * @return bool|\League\OAuth2\Client\Token\AccessTokenInterface
     * @throws CStoreException
     */
    public function checkExpiredToken($oResponse)
    {
        if ($oResponse['error']['code'] === ConstantsLibrary::API_EXPIRED_TOKEN_CODE) {
            $bRefreshToken = $this->refreshExpiredToken();
            if ($bRefreshToken === false) {
                throw new CStoreException(ConstantsLibrary::API_ERROR_MESSAGE);
            }

            return $bRefreshToken;
        }

        throw new CStoreException(ConstantsLibrary::API_ERROR_MESSAGE);
    }

    /**
     * @param $iShopNo
     * @param $oResponse
     * @return array
     * @throws CStoreException
     */
    protected function saveRefreshedToken($iShopNo, $oResponse)
    {
        try {
            $aToken = $this->checkExpiredToken($oResponse)->jsonSerialize();
            $aToken['iShopNo'] = $iShopNo;
            return $this->oTokenRepository->saveAccessToken($aToken);
        } catch (CStoreException $oException) {
            throw new CStoreException($oException->getMessage());
        }
    }

    /**
     * Use to check if the api returns temporary error that will be resolve once api is called again
     * @param $oResponse
     * @return bool
     */
    protected function retryApiCall($oResponse)
    {
        return in_array($oResponse['error']['code'], ConstantsLibrary::RETRY_API_ERROR_CODE);
    }

    /**
     * Use to refresh expired token
     * @return bool|\League\OAuth2\Client\Token\AccessTokenInterface
     */
    private function refreshExpiredToken()
    {
        $aToken = $this->getAccessToken();
        if ($aToken === false) {
            $this->destroySession();
            return false;
        }

        $bResult = $this->refreshToken($aToken['refresh_token'], $this->oRedisParams[ConstantsLibrary::MALL_ID]);
        if ($bResult === false) {
            $this->destroySession();
            return false;
        }

        return $bResult;
    }

    /**
     * Use to make an http request to refresh token
     * @param $sRefreshToken
     * @param $sMallId
     * @return bool|\League\OAuth2\Client\Token\AccessTokenInterface
     */
    public function refreshToken($sRefreshToken, $sMallId)
    {
        $aOptions = [
            'clientId'                => config('auth.client_id'),
            'clientSecret'            => config('auth.client_secret'),
            'redirectUri'             => config('auth.app_url') . '/token',
            'mall_id'                 => $sMallId
        ];

        try {
            $oCafe24AuthProvider = new Cafe24($aOptions);
            $aRefreshToken = $oCafe24AuthProvider->getAccessToken(
                'refresh_token',
                [
                    'refresh_token' => $sRefreshToken
                ]
            );

            return $aRefreshToken;
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $oException) {
            return false;
        }
    }

    /**
     * Remove all session
     */
    public function destroySession()
    {
        session()->flush();
    }

    /**
     * Use to get access token
     * @return mixed
     */
    private function getAccessToken()
    {
        if ($this->oTokenRepository === null) {
            return null;
        }

        return $this->oTokenRepository->getAccessToken($this->oRedisParams);
    }

    /**
     * Use to check if paramter meets requirement for api
     * @param $mValidationResult
     * @throws CStoreException
     */
    protected function checkApiParameterValidation($mValidationResult)
    {
        if ($mValidationResult->fails() === true) {
            throw new CStoreException($mValidationResult->errors()->all()[0]);
        }
    }

    /**
     * Use to get only the product number of a product array
     * @param $aProduct
     * @return array
     */
    protected function getOnlyProductNo($aProduct)
    {
        if (isset($aProduct['data']) === false) {
            return [];
        }

        $aProductNo = [];
        foreach ($aProduct['data'] as $oProduct) {
            $aProductNo[] = isset($oProduct['product_no']) === true ? $oProduct['product_no'] : null;
        }

        return $aProductNo;
    }

    /**
     * Get page
     * @param $aParams
     * @return int
     */
    protected function getPage($aParams)
    {
        if (array_key_exists(ConstantsLibrary::PAGE, $aParams) === false) {
            return 1;
        }

        $iPage = $aParams[ConstantsLibrary::PAGE];
        if ($iPage === null || is_numeric($iPage) === false) {
            $iPage = 1;
        }

        return $iPage;
    }

}
