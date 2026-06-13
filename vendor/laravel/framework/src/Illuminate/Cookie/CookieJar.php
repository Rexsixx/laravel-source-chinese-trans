<?php
/**
 * Illuminate，Cookie，Cookie 压缩
 */

namespace Illuminate\Cookie;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\InteractsWithTime;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Contracts\Cookie\QueueingFactory as JarContract;

class CookieJar implements JarContract
{
    use InteractsWithTime, Macroable;

    /**
     * The default path (if specified).
	 * 默认路径（如果指定）
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The default domain (if specified).
	 * 默认域（如果指定）
     *
     * @var string
     */
    protected $domain;

    /**
     * The default secure setting (defaults to false).
	 * 默认的安全设置（默认为false）
     *
     * @var bool
     */
    protected $secure = false;

    /**
     * The default SameSite option (if specified).
	 * 默认的SameSite选项（如果指定）
     *
     * @var string
     */
    protected $sameSite;

    /**
     * All of the cookies queued for sending.
	 * 所有排队等待发送的cookie
     *
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    protected $queued = [];

    /**
     * Create a new cookie instance.
	 * 创建一个新的cookie实例
     *
     * @param  string       $name
     * @param  string       $value
     * @param  int          $minutes
     * @param  string       $path
     * @param  string       $domain
     * @param  bool|null    $secure
     * @param  bool         $httpOnly
     * @param  bool         $raw
     * @param  string|null  $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        [$path, $domain, $secure, $sameSite] = $this->getPathAndDomain($path, $domain, $secure, $sameSite);

        $time = ($minutes == 0) ? 0 : $this->availableAt($minutes * 60);

        return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * Create a cookie that lasts "forever" (five years).
	 * 做一块“永远”（5年）的cookie
     *
     * @param  string       $name
     * @param  string       $value
     * @param  string       $path
     * @param  string       $domain
     * @param  bool|null    $secure
     * @param  bool         $httpOnly
     * @param  bool         $raw
     * @param  string|null  $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forever($name, $value, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {
        return $this->make($name, $value, 2628000, $path, $domain, $secure, $httpOnly, $raw, $sameSite);
    }

    /**
     * Expire the given cookie.
	 * 使给定的cookie过期
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string  $domain
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forget($name, $path = null, $domain = null)
    {
        return $this->make($name, null, -2628000, $path, $domain);
    }

    /**
     * Determine if a cookie has been queued.
	 * 确定cookie是否已排队
     *
     * @param  string  $key
     * @return bool
     */
    public function hasQueued($key)
    {
        return ! is_null($this->queued($key));
    }

    /**
     * Get a queued cookie instance.
	 * 获取一个排队的cookie实例
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function queued($key, $default = null)
    {
        return Arr::get($this->queued, $key, $default);
    }

    /**
     * Queue a cookie to send with the next response.
	 * 将cookie与下一个响应一起排队发送
     *
     * @param  array  $parameters
     * @return void
     */
    public function queue(...$parameters)
    {
        if (head($parameters) instanceof Cookie) {
            $cookie = head($parameters);
        } else {
            $cookie = call_user_func_array([$this, 'make'], $parameters);
        }

        $this->queued[$cookie->getName()] = $cookie;
    }

    /**
     * Remove a cookie from the queue.
	 * 从队列中删除一个cookie
     *
     * @param  string  $name
     * @return void
     */
    public function unqueue($name)
    {
        unset($this->queued[$name]);
    }

    /**
     * Get the path and domain, or the default values.
	 * 获取路径和域，或默认值。
     *
     * @param  string    $path
     * @param  string    $domain
     * @param  bool|null $secure
     * @param  string    $sameSite
     * @return array
     */
    protected function getPathAndDomain($path, $domain, $secure = null, $sameSite = null)
    {
        return [$path ?: $this->path, $domain ?: $this->domain, is_bool($secure) ? $secure : $this->secure, $sameSite ?: $this->sameSite];
    }

    /**
     * Set the default path and domain for the jar.
	 * 为jar设置默认路径和域
     *
     * @param  string  $path
     * @param  string  $domain
     * @param  bool    $secure
     * @param  string  $sameSite
     * @return $this
     */
    public function setDefaultPathAndDomain($path, $domain, $secure = false, $sameSite = null)
    {
        [$this->path, $this->domain, $this->secure, $this->sameSite] = [$path, $domain, $secure, $sameSite];

        return $this;
    }

    /**
     * Get the cookies which have been queued for the next request.
	 * 获取已为下一个请求排队的cookie
     *
     * @return \Symfony\Component\HttpFoundation\Cookie[]
     */
    public function getQueuedCookies()
    {
        return $this->queued;
    }
}
