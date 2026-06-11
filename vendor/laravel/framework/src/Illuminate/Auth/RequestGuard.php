<?php
/**
 * Illuminate，认证，请求警卫
 */

namespace Illuminate\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Auth\UserProvider;

class RequestGuard implements Guard
{
    use GuardHelpers, Macroable;

    /**
     * The guard callback.
	 * 请求警卫
     *
     * @var callable
     */
    protected $callback;

    /**
     * The request instance.
	 * 请求实例
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
	 * 创建一个新的身份验证保护
     *
     * @param  callable  $callback
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\UserProvider|null $provider
     * @return void
     */
    public function __construct(callable $callback, Request $request, UserProvider $provider = null)
    {
        $this->request = $request;
        $this->callback = $callback;
        $this->provider = $provider;
    }

    /**
     * Get the currently authenticated user.
	 * 获取当前经过身份验证的用户
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
		// 如果我们已经检索了当前请求的用户,我们可以立即返回返回。
		// 我们不希望在每次调用这个方法的每一个调用中获取用户数据,因为这将非常缓慢。
        if (! is_null($this->user)) {
            return $this->user;
        }

        return $this->user = call_user_func(
            $this->callback, $this->request, $this->getProvider()
        );
    }

    /**
     * Validate a user's credentials.
	 * 验证用户的凭据
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return ! is_null((new static(
            $this->callback, $credentials['request'], $this->getProvider()
        ))->user());
    }

    /**
     * Set the current request instance.
	 * 设置当前请求实例
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
