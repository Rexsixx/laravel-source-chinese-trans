<?php
/**
 * Illuminate，支持，门面，Bus
 */

namespace Illuminate\Support\Facades;

use Illuminate\Support\Testing\Fakes\BusFake;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcherContract;

/**
 * @method static mixed dispatch($command)
 * @method static mixed dispatchNow($command, $handler = null)
 * @method static bool hasCommandHandler($command)
 * @method static bool|mixed getCommandHandler($command)
 * @method static \Illuminate\Contracts\Bus\Dispatcher pipeThrough(array $pipes)
 * @method static \Illuminate\Contracts\Bus\Dispatcher map(array $map)
 *
 * @see \Illuminate\Contracts\Bus\Dispatcher
 */
class Bus extends Facade
{
    /**
     * Replace the bound instance with a fake.
	 * 将绑定实例替换为伪实例
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new BusFake);
    }

    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BusDispatcherContract::class;
    }
}
