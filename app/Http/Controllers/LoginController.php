<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(Request $request)
    {
        if ($user = User::where('user_name', $request->user_name)->first()) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                return redirect()->to('/');
            }
        }
        return view('auth.login', ['message'=>'错误：用户名或密码错误！']);

    }
    public function getLogout()
    {
        Auth::logout();
        return redirect()->to('/login');
    }

    public function getRegister()
    {
        return view('auth.register');
    }

    public function postRegister(Request $request)
    {
        if ($request->password !== $request->password_confirmation) {
            return view('auth.register', ['message'=>'错误：两次密码不一致！']);
        }
        $username = $request->user_name;
        if (User::where('user_name', $username)->count() > 0) {
            return view('auth.register', ['message'=>'错误：用户名已存在！']);
        }
        $user = new User();
        $user->user_name = $request->user_name;
        $user->password = Hash::make($request->password);
        $user->save();
        /*        User::create([
                    'user_name'=>$username,
                    'password'=>bcrypt($request->password)
                ]);*/
        return view('auth.register', ['message'=>'注册成功！']);
    }
}
