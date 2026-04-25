<?php
/**
 * Illuminate，Redis，限值器，并发限制器
 */

namespace Illuminate\Redis\Limiters;

use Illuminate\Contracts\Redis\LimiterTimeoutException;

class ConcurrencyLimiter
{
    /**
     * The Redis factory implementation.
	 * Redis工厂实现
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * The name of the limiter.
	 * 限制器的名称
     *
     * @var string
     */
    protected $name;

    /**
     * The allowed number of concurrent tasks.
	 * 允许的并发任务数
     *
     * @var int
     */
    protected $maxLocks;

    /**
     * The number of seconds a slot should be maintained.
	 * 应该维护一个槽位的秒数
     *
     * @var int
     */
    protected $releaseAfter;

    /**
     * Create a new concurrency limiter instance.
	 * 创建一个新的并发限制器实例
     *
     * @param  \Illuminate\Redis\Connections\Connection  $redis
     * @param  string  $name
     * @param  int  $maxLocks
     * @param  int  $releaseAfter
     * @return void
     */
    public function __construct($redis, $name, $maxLocks, $releaseAfter)
    {
        $this->name = $name;
        $this->redis = $redis;
        $this->maxLocks = $maxLocks;
        $this->releaseAfter = $releaseAfter;
    }

    /**
     * Attempt to acquire the lock for the given number of seconds.
	 * 尝试在给定的秒数内获取锁
     *
     * @param  int  $timeout
     * @param  callable|null  $callback
     * @return bool
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function block($timeout, $callback = null)
    {
        $starting = time();

        while (! $slot = $this->acquire()) {
            if (time() - $timeout >= $starting) {
                throw new LimiterTimeoutException;
            }

            usleep(250 * 1000);
        }

        if (is_callable($callback)) {
            return tap($callback(), function () use ($slot) {
                $this->release($slot);
            });
        }

        return true;
    }

    /**
     * Attempt to acquire the lock.
	 * 尝试获取锁
     *
     * @return mixed
     */
    protected function acquire()
    {
        $slots = array_map(function ($i) {
            return $this->name.$i;
        }, range(1, $this->maxLocks));

        return $this->redis->eval($this->luaScript(), count($slots),
            ...array_merge($slots, [$this->name, $this->releaseAfter])
        );
    }

    /**
     * Get the Lua script for acquiring a lock.
	 * 获取用于获取锁的Lua脚本
     *
     * KEYS    - The keys that represent available slots
     * ARGV[1] - The limiter name
     * ARGV[2] - The number of seconds the slot should be reserved
     *
     * @return string
     */
    protected function luaScript()
    {
        return <<<'LUA'
for index, value in pairs(redis.call('mget', unpack(KEYS))) do
    if not value then
        redis.call('set', ARGV[1]..index, "1", "EX", ARGV[2])
        return ARGV[1]..index
    end
end
LUA;
    }

    /**
     * Release the lock.
	 * 释放锁
     *
     * @param  string  $key
     * @return void
     */
    protected function release($key)
    {
        $this->redis->del($key);
    }
}
