<?php
/**
 * Illuminate，路由，重定向控制器
 */

namespace Illuminate\Routing;

use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    /**
     * Invoke the controller method.
	 * 调用控制器方法
     *
     * @param  array  $args
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(...$args)
    {
        list($destination, $status) = array_slice($args, -2);

        return new RedirectResponse($destination, $status);
    }
}
