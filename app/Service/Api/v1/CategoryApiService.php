<?php

namespace App\Service\Api\v1;

use App\Library\ConstantsLibrary;
use App\Library\CStoreLibrary;
use App\Repository\Token\AccessTokenRepository;
use App\Service\Auth\AuthenticateUserService;
use App\Service\BaseService;
use App\Service\Exception\CStoreException;
use App\Validator\Api\v1\CategoryApiValidator;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;

/**
 * Class CategoryApiService
 * @package App\Service\Api\v1
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class CategoryApiService extends BaseService {

    /**
     * CategoryApiService constructor.
     * @param CStoreLibrary         $oCStoreLibrary
     * @param SessionManager        $oSession
     * @param AccessTokenRepository $oRepository
     * @param CategoryApiValidator  $oCategoryValidator
     */
    public function __construct(CStoreLibrary $oCStoreLibrary, SessionManager $oSession, AccessTokenRepository $oRepository, CategoryApiValidator $oCategoryValidator)
    {
        $this->oCStoreLibrary = $oCStoreLibrary;
        $this->oSession = $oSession;
        $this->oTokenRepository = $oRepository;
        $this->oValidator = $oCategoryValidator;
    }

    /**
     * Returns the list of categories
     * @param $aParam
     * @return mixed
     */
    public function getCategories($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateGetCategory($aParam);
            $this->checkApiParameterValidation($mValidationResult);
            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $oResponse = $this->oCStoreLibrary->getProductCategories();
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $this->setJsonResponse(null, $oResponse);
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->getCategories($aParam);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->getCategories($aParam);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

}
