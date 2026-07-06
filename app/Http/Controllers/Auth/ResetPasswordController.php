<?php
/**
 * App，Http，控制器，认证，重置密码控制器
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller	密码复位控制器
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
	| 这个控制器负责处理密码重置请求,并使用一个简单的特性来包括这种行为。
	| 你可以自由地探索这个特质,并覆盖任何你想要调整的方法。
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
	 * 在重新设置密码后重定向用户
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
