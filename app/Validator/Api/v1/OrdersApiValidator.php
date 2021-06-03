<?php

namespace App\Validator\Api\v1;

use App\Rules\Common\ValidParameters;

/**
 * Class OrdersApiValidator
 * @package App\Rules\Common
 *
 * @author kenneth03 <kenneth03@cafe24corp.com>
 * @version 1.0
 * @date 12/11/2020 1:52 PM
 */
class OrdersApiValidator
{
    /**
     * Validates Get All Orders Parameters
     * @param $aParams
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
     */
    public function getAllOrdersValidator($aParams)
    {

        return validator($aParams, [
                'mall_id'        => ['required', 'string'],
                'shop_no'        => ['required', 'numeric', 'min:1'],
                'start_date'     => ['required', 'before_or_equal:end_date'],
                'end_date'       => ['required', 'after_or_equal:start_date'],
                'limit'          => ['required', 'numeric', 'min:10', 'max:40'],
                'offset'         => ['required', 'numeric', 'min:0'],
                'embed'          => ['sometimes'],
            ]
        );
    }

    /**
     * Validates Get Order By Parameter Order Parameters
     * @param $iOrderId
     * @param $aParams
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
     */
    public function getOrderByParamsValidator($iOrderId, $aParams)
    {
        $aValidParameters = ['order_id', 'mall_id', 'shop_no', 'embed'];
        $aValidEmbedParameters = ['items', 'receivers', 'buyer', 'benefits', 'coupons', 'return', 'cancellation', 'exchange', 'refunds'];

        $aParams['order_id'] = $iOrderId;
        $aParams['paramKeys'] = [
            'paramKeys' => array_keys($aParams),
            'validKeys' => $aValidParameters
        ];

        $aParams['embedParamKeys'] = [
            'paramKeys' => $aParams['embed'],
            'validKeys' => $aValidEmbedParameters
        ];

        return validator($aParams, [
                'order_id'       => 'required|string',
                'embedParamKeys' => [new ValidParameters()]
            ]
        );
    }

    /**
     * Validates Update Order Parameters
     * @param $iOrderId
     * @param $aParams
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
     */
    public function updateOrderValidator($iOrderId, $aParams)
    {
        $aValidParameters = ['order_id', 'shop_no', 'mall_id', 'request'];
        $aParams['order_id'] = $iOrderId;
        $aParams['paramKeys'] = [
            'paramKeys' => array_keys($aParams),
            'validKeys' => $aValidParameters
        ];

        return validator($aParams, [
                'order_id'                => 'required|string',
                'request'                 => 'required|array',
                'request.process_status'  => 'required|string',
                'request.order_item_code' => 'sometimes|string',
                'paramKeys'               => new ValidParameters()
            ]
        );
    }

    /**
     * Validates Get All Orders Parameters
     * @param $aParams
     * @return \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator
     */
    public function countOrdersValidator($aParams)
    {
        return validator($aParams, [
                'mall_id'       => ['required'],
                'shop_no'       => ['required', 'numeric', 'min:1'],
                'start_date'    => ['required', 'before_or_equal:end_date'],
                'end_date'      => ['required', 'after_or_equal:start_date']
            ]
        );
    }
}
