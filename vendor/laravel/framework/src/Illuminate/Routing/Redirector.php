<?php
/**
 * Illuminate，路由，Redirector
 */

namespace Illuminate\Routing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Session\Store as SessionStore;

class Redirector
{
    use Macroable;

    /**
     * The URL generator instance.
	 * URL生成器实例
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $generator;

    /**
     * The session store instance.
	 * 会话存储实例
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Create a new Redirector instance.
	 * 创建一个新的Redirector实例
     *
     * @param  \Illuminate\Routing\UrlGenerator  $generator
     * @return void
     */
    public function __construct(UrlGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Create a new redirect response to the "home" route.
	 * 创建新的重定向响应“home”路由
     *
     * @param  int  $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function home($status = 302)
    {
        return $this->to($this->generator->route('home'), $status);
    }

    /**
     * Create a new redirect response to the previous location.
	 * 创建对前面位置的新重定向响应
     *
     * @param  int    $status
     * @param  array  $headers
     * @param  mixed  $fallback
     * @return \Illuminate\Http\RedirectResponse
     */
    public function back($status = 302, $headers = [], $fallback = false)
    {
        return $this->createRedirect($this->generator->previous($fallback), $status, $headers);
    }

    /**
     * Create a new redirect response to the current URI.
	 * 创建对当前URI的新重定向响应
     *
     * @param  int    $status
     * @param  array  $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh($status = 302, $headers = [])
    {
        return $this->to($this->generator->getRequest()->path(), $status, $headers);
    }

    /**
     * Create a new redirect response, while putting the current URL in the session.
	 * 创建一个新的重定向响应,同时在会话中放置当前URL。
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guest($path, $status = 302, $headers = [], $secure = null)
    {
        $this->session->put('url.intended', $this->generator->full());

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the previously intended location.
	 * 创建一个新的重定向响应,以响应先前预定的位置。
     *
     * @param  string  $default
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function intended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        $path = $this->session->pull('url.intended', $default);

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the given path.
	 * 创建对给定路径的新重定向响应
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Illuminate\Http\RedirectResponse
     */
    public function to($path, $status = 302, $headers = [], $secure = null)
    {
        return $this->createRedirect($this->generator->to($path, [], $secure), $status, $headers);
    }

    /**
     * Create a new redirect response to an external URL (no validation).
	 * 创建一个新的重定向响应的外部URL(没有验证)
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function away($path, $status = 302, $headers = [])
    {
        return $this->createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to the given HTTPS path.
	 * 为给定的HTTPS路径创建一个新的重定向响应
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function secure($path, $status = 302, $headers = [])
    {
        return $this->to($path, $status, $headers, true);
    }

    /**
     * Create a new redirect response to a named route.
	 * 创建一个新的重定向响应来响应指定的路由
     *
     * @param  string  $route
     * @param  mixed   $parameters
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function route($route, $parameters = [], $status = 302, $headers = [])
    {
        return $this->to($this->generator->route($route, $parameters), $status, $headers);
    }

    /**
     * Create a new redirect response to a controller action.
	 * 创建一个新的重定向响应来响应控制器的操作
     *
     * @param  string  $action
     * @param  mixed   $parameters
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action($action, $parameters = [], $status = 302, $headers = [])
    {
        return $this->to($this->generator->action($action, $parameters), $status, $headers);
    }

    /**
     * Create a new redirect response.
	 * 创建新的重定向响应
     *
     * @param  string  $path
     * @param  int     $status
     * @param  array   $headers
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function createRedirect($path, $status, $headers)
    {
        return tap(new RedirectResponse($path, $status, $headers), function ($redirect) {
            if (isset($this->session)) {
                $redirect->setSession($this->session);
            }

            $redirect->setRequest($this->generator->getRequest());
        });
    }

    /**
     * Get the URL generator instance.
	 * 获取URL生成器实例
     *
     * @return \Illuminate\Routing\UrlGenerator
     */
    public function getUrlGenerator()
    {
        return $this->generator;
    }

    /**
     * Set the active session store.
	 * 设置活动会话存储
     *
     * @param  \Illuminate\Session\Store  $session
     * @return void
     */
    public function setSession(SessionStore $session)
    {
        $this->session = $session;
    }
}
