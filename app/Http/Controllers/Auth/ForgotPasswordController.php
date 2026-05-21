<?php
/**
 * App，Http，控制台，授权，忘记密码控制器
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller		密码重置控制器
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
	| 此控制器负责处理密码重置邮件，并包含一个特性，可帮助您从应用程序向用户发送此类通知。
    |
    */

    use SendsPasswordResetEmails;

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
