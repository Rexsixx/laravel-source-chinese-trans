<?php
/**
 * Illuminate，缓存，数组存储
 */

namespace Illuminate\Cache;

use Illuminate\Contracts\Cache\Store;

class ArrayStore extends TaggableStore implements Store
{
    use RetrievesMultipleKeys;

    /**
     * The array of stored values.
	 * 存储值的数组
     *
     * @var array
     */
    protected $storage = [];

    /**
     * Retrieve an item from the cache by key.
	 * 按键从缓存中检索项
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->storage[$key] ?? null;
    }

    /**
     * Store an item in the cache for a given number of minutes.
	 * 将项存储在缓存中给定的分钟数
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  float|int  $minutes
     * @return void
     */
    public function put($key, $value, $minutes)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Increment the value of an item in the cache.
	 * 增加缓存中项的值
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int
     */
    public function increment($key, $value = 1)
    {
        $this->storage[$key] = ! isset($this->storage[$key])
                ? $value : ((int) $this->storage[$key]) + $value;

        return $this->storage[$key];
    }

    /**
     * Decrement the value of an item in the cache.
	 * 递减缓存中项的值
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int
     */
    public function decrement($key, $value = 1)
    {
        return $this->increment($key, $value * -1);
    }

    /**
     * Store an item in the cache indefinitely.
	 * 将项无限期地存储在缓存中
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function forever($key, $value)
    {
        $this->put($key, $value, 0);
    }

    /**
     * Remove an item from the cache.
	 * 从缓存中删除项
     *
     * @param  string  $key
     * @return bool
     */
    public function forget($key)
    {
        unset($this->storage[$key]);

        return true;
    }

    /**
     * Remove all items from the cache.
	 * 从缓存中删除所有项
     *
     * @return bool
     */
    public function flush()
    {
        $this->storage = [];

        return true;
    }

    /**
     * Get the cache key prefix.
	 * 获取缓存键前缀
     *
     * @return string
     */
    public function getPrefix()
    {
        return '';
    }
}
