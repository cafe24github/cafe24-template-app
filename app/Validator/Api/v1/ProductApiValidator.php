<?php

namespace App\Validator\Api\v1;

use App\Library\ConstantsLibrary;
use App\Rules\Common\ValidParameters;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class ProductApiValidator
 * @package App\Validator\Api\v1
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class ProductApiValidator
{
    /**
     * Use to validate getting product
     * Shares with counting product
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateGetAllProduct($aParams)
    {
        return validator($aParams, [
                'limit'          => ['sometimes', 'numeric', 'min:10', 'max:100'],
                'offset'         => ['sometimes', 'numeric', 'min:0'],
                'product_name'   => ['sometimes', 'string'],
                'product_code'   => ['sometimes', 'string'],
                'category'       => ['sometimes', 'numeric'],
                'selling'        => ['sometimes', 'string', 'in:T,F'],
                'display'        => ['sometimes', 'string', 'in:T,F'],
                'mall_id'        => ['required', 'string'],
                'shop_no'        => ['required', 'numeric', 'min:0'],
            ]
        );
    }

    /**
     * Use to validate getting bundle product
     * Shares with counting product
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateGetBundleProduct($aParams)
    {
        return validator($aParams, [
                'limit'          => ['sometimes', 'numeric', 'min:10', 'max:100'],
                'offset'         => ['sometimes', 'numeric', 'min:0'],
                'product_name'   => ['sometimes', 'string'],
                'product_code'   => ['sometimes', 'string'],
                'category'       => ['sometimes', 'numeric'],
                'selling'        => ['sometimes', 'string', 'in:T,F'],
                'display'        => ['sometimes', 'string', 'in:T,F'],
                'mall_id'        => ['required', 'string'],
                'shop_no'        => ['required', 'numeric', 'min:0'],
            ]
        );
    }
    
    /**
     * Use to validate save and delete inside app database
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateToggle($aParams)
    {
        return validator($aParams, [
                'product_no'     => ['required', 'numeric', 'min:1'],
                'mall_id'        => ['required', 'string'],
                'shop_no'        => ['required', 'numeric', 'min:0'],
            ]
        );
    }

    /**
     * Use to validate get saved product
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateGetSavedProduct($aParams)
    {
        return validator($aParams, ConstantsLibrary::DEFAULT_VALIDATOR);
    }

    /**
     * Use to validate counting of saved product
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateCountSavedProduct($aParams)
    {
        return validator($aParams, ConstantsLibrary::DEFAULT_VALIDATOR);
    }
}
