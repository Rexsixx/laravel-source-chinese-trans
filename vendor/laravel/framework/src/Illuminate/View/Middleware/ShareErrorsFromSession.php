<?php
/**
 * Illuminate，视图，中间件，来自会话的共享错误
 */

namespace Illuminate\View\Middleware;

use Closure;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Contracts\View\Factory as ViewFactory;

class ShareErrorsFromSession
{
    /**
     * The view factory implementation.
	 * 视图工厂实现
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new error binder instance.
	 * 创建一个新的错误绑定实例
     *
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @return void
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Handle an incoming request.
	 * 处理传入请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If the current session has an "errors" variable bound to it, we will share
        // its value with all view instances so the views can easily access errors
        // without having to bind. An empty bag is set when there aren't errors.
		// 如果当前会话中存在一个名为“errors”的变量，并且该变量已被绑定，
		// 那么我们将将其值共享给所有视图实例，这样视图就能轻松获取错误信息而无需进行绑定操作。如果没有错误发生，则会设置一个空的集合。
        $this->view->share(
            'errors', $request->session()->get('errors') ?: new ViewErrorBag
        );

        // Putting the errors in the view for every view allows the developer to just
        // assume that some errors are always available, which is convenient since
        // they don't have to continually run checks for the presence of errors.
		// 将错误信息以每个视图的形式呈现出来，使得开发人员能够假定某些错误总是存在的，
		// 这样就很方便了，因为他们无需持续进行检查以确认错误是否存在。

        return $next($request);
    }
}
