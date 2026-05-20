<?php
/**
 * Illuminate，支持，门面，Password
 */

namespace Illuminate\Support\Facades;

/**
 * @method static string sendResetLink(array $credentials)
 * @method static mixed reset(array $credentials, \Closure $callback)
 * @method static void validator(\Closure $callback)
 * @method static bool validateNewPassword(array $credentials)
 *
 * @see \Illuminate\Auth\Passwords\PasswordBroker
 */
class Password extends Facade
{
    /**
     * Constant representing a successfully sent reminder.
	 * 代表一个成功发送提醒的常量
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
	 * 代表用户未找到响应的常量
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
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'auth.password';
    }
}
