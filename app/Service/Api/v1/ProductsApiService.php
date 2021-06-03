<?php

namespace App\Service\Api\v1;

use App\Library\ConstantsLibrary;
use App\Library\CStoreLibrary;
use App\Repository\App\ProductRepository;
use App\Repository\Token\AccessTokenRepository;
use App\Service\BaseService;
use App\Service\Exception\CStoreException;
use App\Service\Exception\ProductException;
use App\Validator\Api\v1\ProductApiValidator;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use App\Rules\Common\ValidParameters;

/**
 * Class ProductsApiService
 * @package App\Service\Product
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 12/2/2020 9:35 AM
 */
class ProductsApiService extends BaseService {

    /**
     * Set the needed fields
     */
    const PRODUCT_FIELDS = '&fields=product_code,product_no,product_name,price,additionalimages,summary_description,tiny_image,options,variants';

    /**
     * ProductsService constructor.
     * @param CStoreLibrary         $oCStoreLibrary
     * @param SessionManager        $oSession
     * @param AccessTokenRepository $oRepository
     * @param ProductRepository     $oProdRepository
     * @param ProductApiValidator   $oProductValidator
     */
    public function __construct(CStoreLibrary $oCStoreLibrary, SessionManager $oSession, AccessTokenRepository $oRepository, ProductRepository $oProdRepository, ProductApiValidator $oProductValidator)
    {
        $this->oCStoreLibrary = $oCStoreLibrary;
        $this->oSession = $oSession;
        $this->oTokenRepository = $oRepository;
        $this->oProdRepository = $oProdRepository;
        $this->oValidator = $oProductValidator;
    }

    /**
     * Use to get the products in the api with given parameters
     * @param $aParam
     * @return array|mixed
     */
    public function getProducts($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateGetAllProduct($aParam);
            $this->checkApiParameterValidation($mValidationResult);
            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $sUrlParameters = $this->getSearchUrlParameters($aParam);
            $this->oCStoreLibrary->bCache = true;
            $oResponse = $this->oCStoreLibrary->getProducts($sUrlParameters);
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $oResponse;
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->getProducts($aParam);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->getProducts($aParam);

        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to get url parameters in getting products
     * @param $aParam
     * @return string
     */
    private function getSearchUrlParameters($aParam)
    {
        $sUrlParameters = http_build_query($aParam) . self::PRODUCT_FIELDS . '&embed=options';
        if (array_key_exists('custom_product_code', $aParam) === true) {
            $sUrlParameters .= ',variants';
        }

        if (array_key_exists('embed', $aParam) === true) {
            $sUrlParameters .= ',variants&custom_product_code';
        }

        return $sUrlParameters;
    }

    /**
     * Returns the list of products
     * @param $aParam
     * @return mixed
     */
    public function countProduct($aParam) {
        try {
            $mValidationResult = $this->oValidator->validateGetAllProduct($aParam);
            $this->checkApiParameterValidation($mValidationResult);
            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $sUrlParameters = http_build_query($aParam);
            $this->oCStoreLibrary->bCache = true;
            $oResponse = $this->oCStoreLibrary->getProductCount($sUrlParameters);
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $this->setJsonResponse(null, $oResponse);
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->countProduct($aParam);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->countProduct($aParam);

        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to save product
     * @param $aParam
     * @return array
     */
    public function saveProduct($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateToggle($aParam);
            $this->checkProductParameterValidation($mValidationResult);
            $aParam['product'] = $this->validateProductNo($aParam);
            $bExist = $this->oProdRepository->getProductByNo($aParam);
            if ($bExist !== false) {
                throw new ProductException(ConstantsLibrary::EXISTING_PRODUCT_MESSAGE);
            }

            $iCount = $this->oProdRepository->countSavedProduct($aParam);
            if ($iCount >= config('app.banner_limit')) {
                return $this->setErrorResponse(ConstantsLibrary::MAX_PRODUCT_MESSAGE, $iCount);
            }

            $this->oProdRepository->saveProduct($aParam);
            return $this->setJsonResponse(ConstantsLibrary::PRODUCT_ADDED_MESSAGE, $iCount + 1);
        } catch (ProductException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to get the data for the current page
     * @param $aParam
     * @return array
     */
    public function getProductPageData($aParam)
    {
        $oCount = $this->countProduct($aParam);
        $iCount = $oCount[ConstantsLibrary::DATA][ConstantsLibrary::COUNT];
        $aProductList = $this->getProducts($aParam);
        $oCountSavedProduct = $this->countSavedProduct($aParam);
        $aProduct = $iCount === 0 ? ConstantsLibrary::EMPTY_PRODUCT : $aProductList;
        $oSavedProduct = $this->getSavedProducts($aParam);
        $aSavedProduct = $this->getOnlyProductNo($oSavedProduct);

        return [
            'aProduct'           => $aProduct,
            'iCount'             => $iCount,
            'iCountSavedProduct' => $oCountSavedProduct[ConstantsLibrary::DATA],
            'aSavedProduct'      => $aSavedProduct
        ];
    }

    /**
     * Use to validate product number
     * @param $aParam
     * @return mixed
     * @throws ProductException
     */
    private function validateProductNo($aParam)
    {
        $mProductNo = $aParam['product_no'];
        if (is_numeric($mProductNo) === false) {
            throw new ProductException(ConstantsLibrary::INV_PROD_NUM_MESSAGE);
        }

        $mProduct = $this->getProductByNo($mProductNo, $aParam);
        if ($mProduct === false) {
            throw new ProductException(ConstantsLibrary::API_ERROR_MESSAGE);
        }

        $oProduct = $mProduct['products'];
        if (count($oProduct) === 0) {
            throw new ProductException(ConstantsLibrary::PROD_NO_NOT_EXIST_MESSAGE);
        }

        return $oProduct[0];
    }

    /**
     * Use to delete product
     * @param $aParam
     * @return array
     */
    public function deleteProduct($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateToggle($aParam);
            $this->checkProductParameterValidation($mValidationResult);
            $this->validateProductNo($aParam);
            $bExist = $this->oProdRepository->getProductByNo($aParam);
            if ($bExist === false) {
                throw new ProductException(ConstantsLibrary::PRODUCT_NOT_SAVED_MESSAGE);
            }

            $this->oProdRepository->deleteProduct($aParam['product_no'], $aParam);
            $iCount = $this->oProdRepository->countSavedProduct($aParam);
            return $this->setJsonResponse(ConstantsLibrary::PRODUCT_REMOVED_MESSAGE, $iCount);
        } catch (ProductException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to get saved product
     * @param $aParam
     * @return array
     */
    public function getSavedProducts($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateGetSavedProduct($aParam);
            $this->checkProductParameterValidation($mValidationResult);
            $oData = $this->oProdRepository->getSavedProduct($aParam);
            return $this->setJsonResponse(null, $oData);
        } catch (ProductException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to count the saved product in db
     * @param $aParam
     * @return array
     */
    public function countSavedProduct($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateCountSavedProduct($aParam);
            $this->checkProductParameterValidation($mValidationResult);
            $oData = $this->oProdRepository->countSavedProduct($aParam);
            return $this->setJsonResponse(null, $oData);
        } catch (ProductException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Use to get a product by product number
     * @param $iProductNo
     * @param $aParam
     * @return array|mixed
     */
    public function getProductByNo($iProductNo, $aParam)
    {
        try {
            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $this->oCStoreLibrary->bCache = true;
            $oResponse = $this->oCStoreLibrary->getProductByProductNumber($iProductNo . self::PRODUCT_FIELDS);
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $oResponse;
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->getProductByNo($iProductNo, $aParam);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->getProductByNo($iProductNo, $aParam);
        } catch (CStoreException $oException) {
            return false;
        }
    }

    /**
     * Use to throw product exception when invalid parameter is passed
     * @param $mValidationResult
     * @throws ProductException
     */
    private function checkProductParameterValidation($mValidationResult)
    {
        if ($mValidationResult->fails() === true) {
            throw new ProductException($mValidationResult->errors()->all()[0]);
        }
    }

    /**
     * Use to toggle script tag in redis
     * @param $aParam
     * @return array
     */
    public function toggleScriptTag($aParam)
    {
        try {
            $mInstall = $this->checkInstallable($aParam);
            if ($mInstall === true) {
               $this->installScriptTag($aParam);
            }

            $bFlag = $this->getCurrentScriptTag($aParam);
            $aParam['scripttag'] = ['is_enabled' => !$bFlag];
            $mData = $this->updateScriptTag($aParam);
            if ($mData === false) {
                throw new CStoreException(ConstantsLibrary::API_ERROR_MESSAGE);
            }

            return $this->setJsonResponse(null);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

    /**
     * Install script tag in mall
     * @param $aParam
     * @return int
     * @throws CStoreException
     */
    private function installScriptTag($aParam)
    {
        try {
            $aDisplayLocation = explode(',', config('app.display_location'));
            $aScriptTagParams = [
                'src' => env('APP_URL') . '/scripttag/template-app.js',
                'display_location' => $aDisplayLocation,
                'integrity' => $this->generateSRI()

            ];

            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $oResponse = $this->oCStoreLibrary->installScriptTag($aScriptTagParams);
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $oResponse;
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->installScriptTag($aParam);
            }

            if ($oResponse['error']['code'] === ConstantsLibrary::API_UNPROCESSABLE_CODE && $oResponse['error']['message'] === ConstantsLibrary::CORS_ERROR_MESSAGE) {
                return $this->installScriptTag($aParam);
            }

            if ($oResponse['error']['code'] === ConstantsLibrary::API_UNPROCESSABLE_CODE) {
                throw new CStoreException(ConstantsLibrary::API_ERROR_MESSAGE);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->installScriptTag($aParam);

        } catch (CStoreException $oException) {
            throw new CStoreException($oException->getMessage());
        }
    }

    /**
     * Generates subresource integrity for src file.
     *
     * @return string
     */
    private function generateSRI()
    {
        $sFileSrc = public_path('js/template-app.js');
        $oFileData = file_get_contents($sFileSrc);
        $sEncodedData = base64_encode(hash('sha384', $oFileData, true));
        return 'sha384-' . $sEncodedData;
    }


    /**
     * Update the status of script tag in redis
     * @param $aParam
     * @return int
     * @throws CStoreException
     */
    private function updateScriptTag($aParam)
    {
        $mData = $this->oProdRepository->saveScriptTag($aParam);
        if ($mData === false) {
            throw new CStoreException(ConstantsLibrary::API_ERROR_MESSAGE);
        }

        return $mData;
    }

    /**
     * Check if script tag is not yet install in the mall
     * @param $aParam
     * @return bool
     * @throws CStoreException
     */
    private function checkInstallable($aParam)
    {
        $mData = $this->oProdRepository->checkScriptInstallable($aParam);
        if (count($mData) !== 0) {
            return false;
        }

        $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
        $oResponse = $this->oCStoreLibrary->countScriptTag();
        $bValid = $this->validateApiReturn($oResponse);
        if ($bValid === true) {
            if ($oResponse['count'] !== 0) {
                return false;
            }

            return true;
        }

        if ($this->retryApiCall($oResponse) === true) {
            return $this->checkInstallable($aParam);
        }

        if ($oResponse['error']['code'] === ConstantsLibrary::API_UNPROCESSABLE_CODE) {
            return false;
        }

        $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
        return $this->checkInstallable($aParam);
    }

    /**
     * Get the current script tag status
     * @param $aParam
     * @return bool
     */
    private function getCurrentScriptTag($aParam)
    {
        $mData = $this->oProdRepository->checkScriptInstallable($aParam);
        return isset($mData[0]['is_enabled']) === false ? false : $mData[0]['is_enabled'];

    }

    /**
     * Return script tag status in success response
     * @param $aParam
     * @return array
     * @throws CStoreException
     */
    public function getScriptTagStatus($aParam)
    {
        $bInstalled = $this->checkInstallable($aParam);
        if ($bInstalled === true) {
            return $this->setJsonResponse(null, false);
        }

        $bCurrentStatus = $this->getCurrentScriptTag($aParam);
        return $this->setJsonResponse(null, $bCurrentStatus);
    }

    /**
     * Get bundle products list
     * @param $aParam
     * @return array|mixed
     */
    public function getBundleProducts($aParam)
    {
        try {
            $mValidationResult = $this->oValidator->validateGetBundleProduct($aParam);
            $this->checkApiParameterValidation($mValidationResult);
            $this->setRedisParams($aParam)->setCStoreToken($this->oRedisParams);
            $sUrlParameters = http_build_query($aParam);
            $this->oCStoreLibrary->bCache = true;
            $oResponse = $this->oCStoreLibrary->getBundleProducts($sUrlParameters);
            $bValid = $this->validateApiReturn($oResponse);
            if ($bValid === true) {
                return $oResponse;
            }

            if ($this->retryApiCall($oResponse) === true) {
                return $this->getBundleProducts($aParam);
            }

            $this->saveRefreshedToken($aParam[ConstantsLibrary::SHOP_NO], $oResponse);
            return $this->getBundleProducts($aParam);
        } catch (CStoreException $oException) {
            return $this->setErrorResponse($oException->getMessage());
        }
    }

}
