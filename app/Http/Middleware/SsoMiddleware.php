<?php

namespace App\Http\Middleware;

use App\Admin\Controllers\AuthController;
use Closure;
use Illuminate\Support\Facades\Auth;

class SsoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,$guard=null)
    {
        $admin = Auth::guard($guard)->user();
        if (!$admin){
            return $next($request);
        }
        $sso_token = gen_sso_token();
        $login_token = $admin->worker->login_token;
        if (empty($login_token)){
            return app(AuthController::class)->getLogout($request);
        }
        if (strtolower(getenv('APP_ENV') == 'production')){
            $sso_session = session(config('sso.token_key'));
            if (!$sso_session){
                return app(AuthController::class)->getLogout($request);
            }
            if($sso_token != $sso_session || $sso_session != $login_token){
                return app(AuthController::class)->getLogout($request);
            }
        }else if($sso_token  != $login_token){
            $request->session()->flash(config('sso.error_key'),'异常：已在其他地方登陆，请重新登陆');
            return app(AuthController::class)->getLogout($request);
        }
        return $next($request);
    }
}
