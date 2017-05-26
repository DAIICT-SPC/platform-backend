<?php

namespace App\Http\Middleware;

use App\Helper;
use Closure;

class checkRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {

        if( request()->user()->role != $role )
        {
            return Helper::apiError("Unauthorized Access!",null,401);
        }

        return $next($request);
    }
}
