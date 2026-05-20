<?php
/**
 * Illuminate，支持，测试，Fake，事件 Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Contracts\Events\Dispatcher;

class EventFake implements Dispatcher
{
    /**
     * The original event dispatcher.
	 * 原始事件调度员
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * The event types that should be intercepted instead of dispatched.
	 * 应该拦截的事件类型而不是发送
     *
     * @var array
     */
    protected $eventsToFake;

    /**
     * All of the events that have been intercepted keyed by type.
	 * 所有被拦截的事件都被键入了
     *
     * @var array
     */
    protected $events = [];

    /**
     * Create a new event fake instance.
	 * 创建一个新的事件假实例
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     * @param  array|string  $eventsToFake
     * @return void
     */
    public function __construct(Dispatcher $dispatcher, $eventsToFake = [])
    {
        $this->dispatcher = $dispatcher;

        $this->eventsToFake = Arr::wrap($eventsToFake);
    }

    /**
     * Assert if an event was dispatched based on a truth-test callback.
	 * 断言如果一个事件是基于trutest callback发送的
     *
     * @param  string  $event
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertDispatched($event, $callback = null)
    {
        if (is_int($callback)) {
            return $this->assertDispatchedTimes($event, $callback);
        }

        PHPUnit::assertTrue(
            $this->dispatched($event, $callback)->count() > 0,
            "The expected [{$event}] event was not dispatched."
        );
    }

    /**
     * Assert if a event was dispatched a number of times.
	 * 断言是否事件被发送了很多次
     *
     * @param  string  $event
     * @param  int  $times
     * @return void
     */
    public function assertDispatchedTimes($event, $times = 1)
    {
        PHPUnit::assertTrue(
            ($count = $this->dispatched($event)->count()) === $times,
            "The expected [{$event}] event was dispatched {$count} times instead of {$times} times."
        );
    }

    /**
     * Determine if an event was dispatched based on a truth-test callback.
	 * 确定是否根据trutest callback发送一个事件
     *
     * @param  string  $event
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotDispatched($event, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->dispatched($event, $callback)->count() === 0,
            "The unexpected [{$event}] event was dispatched."
        );
    }

    /**
     * Get all of the events matching a truth-test callback.
	 * 获取匹配一个trutest callback的所有事件
     *
     * @param  string  $event
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function dispatched($event, $callback = null)
    {
        if (! $this->hasDispatched($event)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->events[$event])->filter(function ($arguments) use ($callback) {
            return $callback(...$arguments);
        });
    }

    /**
     * Determine if the given event has been dispatched.
	 * 确定是否已发送给定事件
     *
     * @param  string  $event
     * @return bool
     */
    public function hasDispatched($event)
    {
        return isset($this->events[$event]) && ! empty($this->events[$event]);
    }

    /**
     * Register an event listener with the dispatcher.
	 * 用dispatcher注册一个事件侦听器
     *
     * @param  string|array  $events
     * @param  mixed  $listener
     * @return void
     */
    public function listen($events, $listener)
    {
        $this->dispatcher->listen($events, $listener);
    }

    /**
     * Determine if a given event has listeners.
	 * 确定给定事件是否有侦听器
     *
     * @param  string  $eventName
     * @return bool
     */
    public function hasListeners($eventName)
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    /**
     * Register an event and payload to be dispatched later.
	 * 稍后注册一个事件和有效负载
     *
     * @param  string  $event
     * @param  array  $payload
     * @return void
     */
    public function push($event, $payload = [])
    {
        //
    }

    /**
     * Register an event subscriber with the dispatcher.
	 * 向调度员注册一个事件订阅者
     *
     * @param  object|string  $subscriber
     * @return void
     */
    public function subscribe($subscriber)
    {
        $this->dispatcher->subscribe($subscriber);
    }

    /**
     * Flush a set of pushed events.
	 * 刷新一组推送事件
     *
     * @param  string  $event
     * @return void
     */
    public function flush($event)
    {
        //
    }

    /**
     * Fire an event and call the listeners.
	 * 启动一个事件并调用侦听器
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function fire($event, $payload = [], $halt = false)
    {
        return $this->dispatch($event, $payload, $halt);
    }

    /**
     * Fire an event and call the listeners.
	 * 启动一个事件并调用侦听器
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function dispatch($event, $payload = [], $halt = false)
    {
        $name = is_object($event) ? get_class($event) : (string) $event;

        if ($this->shouldFakeEvent($name, $payload)) {
            $this->events[$name][] = func_get_args();
        } else {
            $this->dispatcher->dispatch($event, $payload, $halt);
        }
    }

    /**
     * Determine if an event should be faked or actually dispatched.
	 * 确定事件是否应该被伪造或被发送
     *
     * @param  string  $eventName
     * @param  mixed  $payload
     * @return bool
     */
    protected function shouldFakeEvent($eventName, $payload)
    {
        if (empty($this->eventsToFake)) {
            return true;
        }

        return collect($this->eventsToFake)
            ->filter(function ($event) use ($eventName, $payload) {
                return $event instanceof Closure
                            ? $event($eventName, $payload)
                            : $event === $eventName;
            })
            ->isNotEmpty();
    }

    /**
     * Remove a set of listeners from the dispatcher.
	 * 从dispatcher中删除一组侦听器
     *
     * @param  string  $event
     * @return void
     */
    public function forget($event)
    {
        //
    }

    /**
     * Forget all of the queued listeners.
	 * 忘记所有排队的侦听器
     *
     * @return void
     */
    public function forgetPushed()
    {
        //
    }

    /**
     * Dispatch an event and call the listeners.
	 * 发送一个事件并调用侦听器
     *
     * @param  string|object $event
     * @param  mixed $payload
     * @return void
     */
    public function until($event, $payload = [])
    {
        return $this->dispatch($event, $payload, true);
    }
}
