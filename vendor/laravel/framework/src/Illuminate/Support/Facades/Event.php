<?php
/**
 * Illuminate，支持，门面，Event
 */

namespace Illuminate\Support\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Testing\Fakes\EventFake;

/**
 * @method static void listen(string|array $events, $listener)
 * @method static bool hasListeners(string $eventName)
 * @method static void subscribe(object|string $subscriber)
 * @method static array|null until(string|object $event, $payload = [])
 * @method static array|null dispatch(string|object $event, $payload = [], bool $halt = false)
 * @method static void push(string $event, array $payload = [])
 * @method static void flush(string $event)
 * @method static void forget(string $event)
 * @method static void forgetPushed()
 *
 * @see \Illuminate\Events\Dispatcher
 */
class Event extends Facade
{
    /**
     * Replace the bound instance with a fake.
	 * 将绑定实例替换为伪实例
     *
     * @param  array|string  $eventsToFake
     * @return void
     */
    public static function fake($eventsToFake = [])
    {
        static::swap($fake = new EventFake(static::getFacadeRoot(), $eventsToFake));

        Model::setEventDispatcher($fake);
    }

    /**
     * Replace the bound instance with a fake during the given callable's execution.
	 * 在给定的可调用对象执行期间，将绑定实例替换为伪实例。
     *
     * @param  callable  $callable
     * @param  array  $eventsToFake
     * @return callable
     */
    public static function fakeFor(callable $callable, array $eventsToFake = [])
    {
        $originalDispatcher = static::getFacadeRoot();

        static::fake($eventsToFake);

        return tap($callable(), function () use ($originalDispatcher) {
            static::swap($originalDispatcher);

            Model::setEventDispatcher($originalDispatcher);
        });
    }

    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'events';
    }
}
