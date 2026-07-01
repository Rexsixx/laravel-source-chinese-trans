<?php
/**
 * App，Http，控制器，认证，验证控制器
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller		电子邮件验证控制器
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
	| 该控制器负责处理最近在应用程序注册的任何用户的电子邮件验证。
	| 如果用户没有收到原始的电子邮件信息,电子邮件也可能被重新发送。
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
	 * 在验证后重定向用户
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
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
