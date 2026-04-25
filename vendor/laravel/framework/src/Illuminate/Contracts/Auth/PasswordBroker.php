<?php
/**
 * Illuminate，契约，认证，密码代理
 */

namespace Illuminate\Contracts\Auth;

use Closure;

interface PasswordBroker
{
    /**
     * Constant representing a successfully sent reminder.
	 * 表示成功发送提醒的常量
     *
     * @var string
     */
    const RESET_LINK_SENT = 'passwords.sent';

    /**
     * Constant representing a successfully reset password.
	 * 表示成功重置密码的常量
     *
     * @var string
     */
    const PASSWORD_RESET = 'passwords.reset';

    /**
     * Constant representing the user not found response.
	 * 表示用户未找到响应的常量
     *
     * @var string
     */
    const INVALID_USER = 'passwords.user';

    /**
     * Constant representing an invalid password.
	 * 表示无效密码的常量
     *
     * @var string
     */
    const INVALID_PASSWORD = 'passwords.password';

    /**
     * Constant representing an invalid token.
	 * 表示无效令牌的常量
     *
     * @var string
     */
    const INVALID_TOKEN = 'passwords.token';

    /**
     * Send a password reset link to a user.
	 * 向用户发送密码重置链接
     *
     * @param  array  $credentials
     * @return string
     */
    public function sendResetLink(array $credentials);

    /**
     * Reset the password for the given token.
	 * 重置给定令牌的密码
     *
     * @param  array     $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback);

    /**
     * Set a custom password validator.
	 * 设置自定义密码验证器
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function validator(Closure $callback);

    /**
     * Determine if the passwords match for the request.
	 * 确定密码是否与请求匹配
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validateNewPassword(array $credentials);
}
