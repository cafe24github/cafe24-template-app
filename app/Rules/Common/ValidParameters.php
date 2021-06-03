<?php

namespace App\Rules\Common;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class ValidParameters
 * @package App\Rules\Common
 *
 * @author kenneth03 <kenneth03@cafe24corp.com>
 * @version 1.0
 * @date 12/11/2020 1:52 PM
 */
class ValidParameters implements Rule
{
    private $bIsEmpty;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $bValid = is_array($value['paramKeys']) === false ? $this->validateSingleKey($value, $value['validKeys']) : $this->validateMultipleKeys($value['paramKeys'], $value['validKeys']);

        return $bValid;
    }

    private function validateSingleKey($aParamKey, $aValidKeys)
    {
        if (in_array($aParamKey['paramKeys'], $aValidKeys) === false) {
            return false;
        }

        return true;
    }

    private function validateMultipleKeys($aParamKeys, $aValidKeys)
    {
        if ($aParamKeys === []) {
            $this->bIsEmpty = true;
            return false;
        }

        foreach ($aParamKeys as $aParamKey) {
            if (in_array($aParamKey, $aValidKeys) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if ($this->bIsEmpty === true) {
            return 'Missing parameters';
        }

        return 'Please check parameters.';
    }
}
