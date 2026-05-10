<?php
/**
 * Illuminate，Redis，连接，Connection
 */

namespace Illuminate\Redis\Connections;

use Closure;
use Illuminate\Redis\Limiters\DurationLimiterBuilder;
use Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder;

/**
 * @mixin \Predis\Client
 */
abstract class Connection
{
    /**
     * The Predis client.
	 * Predis客户端
     *
     * @var \Predis\Client
     */
    protected $client;

    /**
     * Subscribe to a set of given channels for messages.
	 * 为消息订阅一组给定的通道
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $method
     * @return void
     */
    abstract public function createSubscription($channels, Closure $callback, $method = 'subscribe');

    /**
     * Funnel a callback for a maximum number of simultaneous executions.
	 * 为同时执行的最大数量设置一个漏斗回调
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Limiters\ConcurrencyLimiterBuilder
     */
    public function funnel($name)
    {
        return new ConcurrencyLimiterBuilder($this, $name);
    }

    /**
     * Throttle a callback for a maximum number of executions over a given duration.
	 * 在给定的持续时间内限制回调的最大执行次数
     *
     * @param  string  $name
     * @return \Illuminate\Redis\Limiters\DurationLimiterBuilder
     */
    public function throttle($name)
    {
        return new DurationLimiterBuilder($this, $name);
    }

    /**
     * Get the underlying Redis client.
	 * 获取底层Redis客户端
     *
     * @return mixed
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Subscribe to a set of given channels for messages.
	 * 为消息订阅一组给定的通道
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @return void
     */
    public function subscribe($channels, Closure $callback)
    {
        return $this->createSubscription($channels, $callback, __FUNCTION__);
    }

    /**
     * Subscribe to a set of given channels with wildcards.
	 * 使用通配符订阅一组给定的通道
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @return void
     */
    public function psubscribe($channels, Closure $callback)
    {
        return $this->createSubscription($channels, $callback, __FUNCTION__);
    }

    /**
     * Run a command against the Redis database.
	 * 对Redis数据库运行命令
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function command($method, array $parameters = [])
    {
        return $this->client->{$method}(...$parameters);
    }

    /**
     * Pass other method calls down to the underlying client.
	 * 将其他方法调用传递给底层客户端
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->command($method, $parameters);
    }
}
