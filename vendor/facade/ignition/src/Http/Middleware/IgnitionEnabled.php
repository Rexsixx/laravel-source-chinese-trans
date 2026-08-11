<?php
/**
 * 门面，Ignition，Http，中间件，Ignition 启用
 */

namespace Facade\Ignition\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IgnitionEnabled
{
    /**
     * Handle an incoming request.
	 * 处理传入请求
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->ignitionEnabled()) {
            abort(404);
        }

        return $next($request);
    }

    protected function ignitionEnabled(): bool
    {
        return config('app.debug');
    }
}
