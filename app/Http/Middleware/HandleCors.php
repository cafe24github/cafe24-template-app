<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class HandleCors
 * @package App\Http\Middleware
 * @author eric eric@cafe24corp.com
 * @version 1.0
 * @date 2/26/2021 11:37 AM
 */
class HandleCors
{
    /**
     * Handle an incoming request.
     * @param         $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        return $next($request);
    }
}
