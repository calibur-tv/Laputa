<?php
/**
 * Created by PhpStorm.
 * User: yuistack
 * Date: 2018/2/4
 * Time: 上午9:24
 */

namespace App\Http\Middleware;


use Tymon\JWTAuth\Middleware\GetUserFromToken;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Auth extends GetUserFromToken
{
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken())
        {
            return response([
                'code' => 40105,
                'message' => config('error.40105'),
                'data' => ''
            ], 401);
        }

        try
        {
            $user = $this->auth->authenticate($token);
        }
        catch (TokenExpiredException $e)
        {
            return response([
                'code' => 40102,
                'message' => config('error.40102'),
                'data' => ''
            ], 401);
        }
        catch (JWTException $e)
        {
            return response([
                'code' => 40103,
                'message' => config('error.40103'),
                'data' => ''
            ], 401);
        }

        if (! $user)
        {
            return response([
                'code' => 40104,
                'message' => config('error.40104'),
                'data' => ''
            ], 401);
        }

        if ($this->auth->getPayload($token)['remember'] !== $user->remember_token)
        {
            return response([
                'code' => 40106,
                'message' => config('error.40106'),
                'data' => ''
            ], 401);
        }

        return $next($request);
    }
}