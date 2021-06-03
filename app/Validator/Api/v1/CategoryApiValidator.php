<?php

namespace App\Validator\Api\v1;

use App\Library\ConstantsLibrary;
use App\Rules\Common\ValidParameters;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Contracts\Validation\Validator;

/**
 * Class CategoryApiValidator
 * @package App\Validator\Api\v1
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class CategoryApiValidator
{
    /**
     * Use to validate getting product
     * Shares with counting product
     * @param $aParams
     * @return Factory|Validator
     */
    public function validateGetCategory($aParams)
    {
        return validator($aParams, ConstantsLibrary::DEFAULT_VALIDATOR);
    }
}
