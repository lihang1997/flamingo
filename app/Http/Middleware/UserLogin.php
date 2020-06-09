<?php

namespace App\Http\Middleware;

use App\Http\Flamingo\Token;
use Closure;

class UserLogin
{
    const LOGIN_URI = '/api/user/login';

    const NO_LOGIN = [
        self::LOGIN_URI,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $uri = $request->getRequestUri();
        if ( ! in_array($uri, self::NO_LOGIN)) {
            /**
             * 检查一下 token 是否是可用的
             */
            $requestToken = $request->header('logintoken');
            $isEffective = Token::isEffective($requestToken, $request);
            if ( ! $isEffective) {
                return response('403 Forbidden, user token is unavalidate.', 403);
            }
        }
        return $next($request);
    }
}
