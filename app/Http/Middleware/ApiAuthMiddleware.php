<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$this->basic_validate($request->header('PHP_AUTH_USER'), $request->header('PHP_AUTH_PW'))) {
            $response['AUTH_FAIL_CODE'] = config('constants.AUTH_FAIL_CODE');
            $response['INVALID_AUTHENTICATION'] = config('constants.INVALID_AUTHENTICATION');
            return response()->json($response);
        }
        return $next($request);
    }

    private function basic_validate($user, $password)
    {
        if ($user == env("API_USERNAME") && $password == env("API_PASSWORD")) {

            return true;
        }

        return false;
    }
}
