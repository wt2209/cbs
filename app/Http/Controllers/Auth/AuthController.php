<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Http\Request;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function getMyLogin()
    {
        return view('auth.login');
    }

    public function postMyLogin(Request $request)
    {
        if ($user = User::where('user_name', $request->user_name)->first()) {
            if (Hash::check($request->password, $user->password)) {
                Auth::login($user);
                return redirect()->to('/');
            }
        }
        return view('auth.login', ['message'=>'错误：用户名或密码错误！']);

    }
    public function getMyLogout()
    {
        Auth::logout();
        return redirect()->to('/login');
    }

    public function getMyRegister()
    {
        return view('auth.register');
    }

    public function postMyRegister(Request $request)
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
