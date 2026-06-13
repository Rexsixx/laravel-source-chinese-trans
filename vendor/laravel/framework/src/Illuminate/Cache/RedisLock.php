<?php
/**
 * Illuminate，缓存，Redis 锁
 */

namespace Illuminate\Cache;

class RedisLock extends Lock
{
    /**
     * The Redis factory implementation.
	 * Redis工厂实现
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * Create a new lock instance.
	 * 创建一个新的锁实例
     *
     * @param  \Illuminate\Redis\Connections\Connection  $redis
     * @param  string  $name
     * @param  int  $seconds
     * @return void
     */
    public function __construct($redis, $name, $seconds)
    {
        parent::__construct($name, $seconds);

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
	 * 尝试获取锁
     *
     * @return bool
     */
    public function acquire()
    {
        $result = $this->redis->setnx($this->name, 1);

        if ($result === 1 && $this->seconds > 0) {
            $this->redis->expire($this->name, $this->seconds);
        }

        return $result === 1;
    }

    /**
     * Release the lock.
	 * 释放锁
     *
     * @return void
     */
    public function release()
    {
        $this->redis->del($this->name);
    }
}
