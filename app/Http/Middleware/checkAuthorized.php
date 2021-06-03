<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;

/**
 * Class checkAuthorized
 * @package App\Http\Middleware
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/1/2021 9:35 AM
 */
class checkAuthorized
{
    /**
     * Required keys to proceed in app
     */
    const REQUIRED_KEY = [
        'mall_id',
        'user_id',
        'user_type'
    ];

    /**
     * Handle an incoming request. Checks if user is Authorized
     * @param Request  $oRequest
     * @param callable $cNext
     * @return mixed
     */
    public function handle(Request $oRequest, callable $cNext)
    {
        foreach (self::REQUIRED_KEY as $sKey => $sValue) {
            if (session()->has($sValue) === false) {
                return redirect()->route('forbidden');
            }
        }

        return $cNext($oRequest);
    }
}
