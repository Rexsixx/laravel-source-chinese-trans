<?php
/**
 * Illuminate，支持，门面，Broadcast
 */

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static void connection($name = null);
 * @method static \Illuminate\Broadcasting\Broadcasters\Broadcaster channel(string $channel, callable|string  $callback)
 * @method static mixed auth(\Illuminate\Http\Request $request)
 *
 * @see \Illuminate\Contracts\Broadcasting\Factory
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
