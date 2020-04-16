<?php

namespace App\Admin\Controllers;

use App\Models\Worker;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Facades\Agent;

class AuthController extends BaseAuthController
{
    protected function username()
    {
        return 'identity';
    }

    protected $loginView = 'admin.login';

    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }
        $identity = $this->username();
        return view($this->loginView,compact('identity'));
    }

    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);
        $username = $credentials[$this->username()];
        // 优先工号登陆
        $worker = Worker::where('no',$username)->first();
        if ($worker && $worker->admin->username){
            $username = $worker->admin->username;
        }
        if ($this->guard()->attempt(['username'=>$username, 'password'=>$credentials['password'] ], $remember)) {
            $admin = $this->guard()->user();
            if ($admin->id != 1) {
                $sso_token = gen_sso_token();
                session([config('sso.token_key')=> $sso_token]);

                $admin->worker->login_token = $sso_token;
                $admin->worker->save();
            }
            return $this->sendLoginResponse($request);
        }
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    public function getLogout(Request $request)
    {
        $error_msg = $request->session()->get(config('sso.error_key'));
        $this->guard()->logout();
        $request->session()->invalidate();
        if ($error_msg){
            return redirect(config('admin.route.prefix'))->withErrors([
                    $this->username() => $error_msg
                ]);
        }
        return redirect(config('admin.route.prefix'));
    }
}
