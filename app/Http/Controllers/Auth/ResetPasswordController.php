<?php
/**
 * App，Http，控制台，授权，重置密码控制器
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller	密码重置控制器
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
	| 此控制器负责处理密码重置请求，并通过一个简单的特性来实现这一功能。
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
	 * 重置密码后重定向用户的位置
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
	 * 创建一个新的控制器实例
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}
