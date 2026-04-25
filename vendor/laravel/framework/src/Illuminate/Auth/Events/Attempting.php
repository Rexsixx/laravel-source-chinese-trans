<?php
/**
 * Illuminate，Auth，事件，尝试
 */

namespace Illuminate\Auth\Events;

class Attempting
{
    /**
     * The credentials for the user.
	 * 用户的凭据
     *
     * @var array
     */
    public $credentials;

    /**
     * Indicates if the user should be "remembered".
	 * 指示是否需要“记住”用户
     *
     * @var bool
     */
    public $remember;

    /**
     * Create a new event instance.
	 * 创建一个新的事件实例
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return void
     */
    public function __construct($credentials, $remember)
    {
        $this->remember = $remember;
        $this->credentials = $credentials;
    }
}
