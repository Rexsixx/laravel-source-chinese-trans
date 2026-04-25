<?php
/**
 * Illuminate，契约，缓存，锁供应商
 */

namespace Illuminate\Contracts\Cache;

interface LockProvider
{
    /**
     * Get a lock instance.
	 * 获取一个锁实例
     *
     * @param  string  $name
     * @param  int  $seconds
     * @return \Illuminate\Contracts\Cache\Lock
     */
    public function lock($name, $seconds = 0);
}
