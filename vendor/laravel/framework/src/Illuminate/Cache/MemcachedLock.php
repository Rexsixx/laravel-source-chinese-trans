<?php
/**
 * Illuminate，缓存，Memcached 锁
 */

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Lock as LockContract;

class MemcachedLock extends Lock implements LockContract
{
    /**
     * The Memcached instance.
	 * Memcached实例
     *
     * @var \Memcached
     */
    protected $memcached;

    /**
     * Create a new lock instance.
	 * 创建一个新的锁实例
     *
     * @param  \Memcached  $memcached
     * @param  string  $name
     * @param  int  $seconds
     * @return void
     */
    public function __construct($memcached, $name, $seconds)
    {
        parent::__construct($name, $seconds);

        $this->memcached = $memcached;
    }

    /**
     * Attempt to acquire the lock.
	 * 尝试获取锁
     *
     * @return bool
     */
    public function acquire()
    {
        return $this->memcached->add(
            $this->name, 1, $this->seconds
        );
    }

    /**
     * Release the lock.
	 * 释放锁
     *
     * @return void
     */
    public function release()
    {
        $this->memcached->delete($this->name);
    }
}
