<?php
/**
 * Illuminate，认证，密码，令牌库接口
 */

namespace Illuminate\Auth\Passwords;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

interface TokenRepositoryInterface
{
    /**
     * Create a new token.
	 * 创建一个新令牌
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function create(CanResetPasswordContract $user);

    /**
     * Determine if a token record exists and is valid.
	 * 确定令牌记录是否存在并且有效
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token);

    /**
     * Delete a token record.
	 * 删除令牌记录
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function delete(CanResetPasswordContract $user);

    /**
     * Delete expired tokens.
	 * 删除过期令牌
     *
     * @return void
     */
    public function deleteExpired();
}
