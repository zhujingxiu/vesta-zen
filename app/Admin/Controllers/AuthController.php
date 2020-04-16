<?php

namespace App\Admin\Controllers;

use App\Models\Worker;
use Encore\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;

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
            return $this->sendLoginResponse($request);
        }
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }
}
