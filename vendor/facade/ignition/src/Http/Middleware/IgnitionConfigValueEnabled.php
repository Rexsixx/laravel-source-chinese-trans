<?php
/**
 * 门面，Ignition，Http，中间件，Ignition 配置值使能
 */

namespace Facade\Ignition\Http\Middleware;

use Closure;
use Facade\Ignition\IgnitionConfig;
use Illuminate\Http\Request;

class IgnitionConfigValueEnabled
{
    /** @var \Facade\Ignition\IgnitionConfig */
    protected $ignitionConfig;

    public function __construct(IgnitionConfig $ignitionConfig)
    {
        $this->ignitionConfig = $ignitionConfig;
    }

    public function handle(Request $request, Closure $next, string $value)
    {
        if (! $this->ignitionConfig->toArray()[$value]) {
            abort(404);
        }

        return $next($request);
    }
}
