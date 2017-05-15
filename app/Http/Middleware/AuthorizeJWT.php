<?php

namespace App\Http\Middleware;

use Closure;
use  \JWTAuth;

class AuthorizeJWT
{

    public function handle($request, Closure $next)
    {

        $user = $this->authorizeToken();

        $request->setUserResolver(function () use($user){
            return $user;
        });

        return $next($request);
    }


    protected function authorizeToken()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                throw new \Exception('user_not_found', 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            throw new \Exception('token_expired', $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            throw new \Exception('token_invalid', $e->getStatusCode());

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            throw new \Exception('token_absent', $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

}
