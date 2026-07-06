<?php
/**
 * Illuminate，缓存，数据库存储
 */

namespace Illuminate\Cache;

use Closure;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\ConnectionInterface;

class DatabaseStore implements Store
{
    use InteractsWithTime, RetrievesMultipleKeys;

    /**
     * The database connection instance.
	 * 数据库连接实例
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The name of the cache table.
	 * 缓存表的名称
     *
     * @var string
     */
    protected $table;

    /**
     * A string that should be prepended to keys.
	 * 应该加在键前的字符串
     *
     * @var string
     */
    protected $prefix;

    /**
     * Create a new database store.
	 * 创建一个新的数据库存储
     *
     * @param  \Illuminate\Database\ConnectionInterface  $connection
     * @param  string  $table
     * @param  string  $prefix
     * @return void
     */
    public function __construct(ConnectionInterface $connection, $table, $prefix = '')
    {
        $this->table = $table;
        $this->prefix = $prefix;
        $this->connection = $connection;
    }

    /**
     * Retrieve an item from the cache by key.
	 * 按键从缓存中检索项
     *
     * @param  string|array  $key
     * @return mixed
     */
    public function get($key)
    {
        $prefixed = $this->prefix.$key;

        $cache = $this->table()->where('key', '=', $prefixed)->first();

        // If we have a cache record we will check the expiration time against current
        // time on the system and see if the record has expired. If it has, we will
        // remove the records from the database table so it isn't returned again.
		// 如果我们有缓存记录，就会将该记录的过期时间与系统当前时间进行比较，以确定该记录是否已过期。
		// 如果有,我们将从数据库表中删除记录,因此不会再次返回。
        if (is_null($cache)) {
            return;
        }

        $cache = is_array($cache) ? (object) $cache : $cache;

        // If this cache expiration date is past the current time, we will remove this
        // item from the cache. Then we will return a null value since the cache is
        // expired. We will use "Carbon" to make this comparison with the column.
		// 如果此缓存过期日期已超过当前时间，我们将从缓存中删除此项目。
		// 然后我们将返回空值,因为缓存已经过期。我们将使用“碳”与列进行比较。
        if ($this->currentTime() >= $cache->expiration) {
            $this->forget($key);

            return;
        }

        return $this->unserialize($cache->value);
    }

    /**
     * Store an item in the cache for a given number of seconds.
	 * 将项存储在缓存中给定的秒数
     *
     * @param  string  $key
     * @param  mixed   $value
     * @param  int  $seconds
     * @return bool
     */
    public function put($key, $value, $seconds)
    {
        $key = $this->prefix.$key;

        $value = $this->serialize($value);

        $expiration = $this->getTime() + $seconds;

        try {
            return $this->table()->insert(compact('key', 'value', 'expiration'));
        } catch (Exception $e) {
            $result = $this->table()->where('key', $key)->update(compact('value', 'expiration'));

            return $result > 0;
        }
    }

    /**
     * Increment the value of an item in the cache.
	 * 增加缓存中项的值
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current + $value;
        });
    }

    /**
     * Decrement the value of an item in the cache.
	 * 递减缓存中项的值
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        return $this->incrementOrDecrement($key, $value, function ($current, $value) {
            return $current - $value;
        });
    }

    /**
     * Increment or decrement an item in the cache.
	 * 增加或减少缓存中的项
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  \Closure  $callback
     * @return int|bool
     */
    protected function incrementOrDecrement($key, $value, Closure $callback)
    {
        return $this->connection->transaction(function () use ($key, $value, $callback) {
            $prefixed = $this->prefix.$key;

            $cache = $this->table()->where('key', $prefixed)
                        ->lockForUpdate()->first();

            // If there is no value in the cache, we will return false here. Otherwise the
            // value will be decrypted and we will proceed with this function to either
            // increment or decrement this value based on the given action callbacks.
			// 如果缓存中没有数据，则在此处返回“false”。
			// 否则，该值将会被解密，然后我们将根据给定的操作回调函数来执行此功能，以实现对该值的增加或减少操作。
            if (is_null($cache)) {
                return false;
            }

            $cache = is_array($cache) ? (object) $cache : $cache;

            $current = $this->unserialize($cache->value);

            // Here we'll call this callback function that was given to the function which
            // is used to either increment or decrement the function. We use a callback
            // so we do not have to recreate all this logic in each of the functions.
			// 在这里，我们将把这个回调函数称为那个被传递给该函数的函数，这个函数用于实现函数的递增或递减操作。
			// 我们使用回调,所以我们不必在每个函数中重新创建所有这个逻辑。
            $new = $callback((int) $current, $value);

            if (! is_numeric($current)) {
                return false;
            }

            // Here we will update the values in the table. We will also encrypt the value
            // since database cache values are encrypted by default with secure storage
            // that can't be easily read. We will return the new value after storing.
			// 在这里，我们将更新表格中的数值。同时，我们还会对这些数值进行加密处理，
			// 因为数据库缓存中的数值默认是经过加密存储的，且这种存储方式不易被读取。
            $this->table()->where('key', $prefixed)->update([
                'value' => $this->serialize($new),
            ]);

            return $new;
        });
    }

    /**
     * Get the current system time.
	 * 获取当前系统时间
     *
     * @return int
     */
    protected function getTime()
    {
        return $this->currentTime();
    }

    /**
     * Store an item in the cache indefinitely.
	 * 将项无限期地存储在缓存中
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return bool
     */
    public function forever($key, $value)
    {
        return $this->put($key, $value, 315360000);
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
        $this->table()->where('key', '=', $this->prefix.$key)->delete();

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
        $this->table()->delete();

        return true;
    }

    /**
     * Get a query builder for the cache table.
	 * 获取缓存表的查询生成器
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->connection->table($this->table);
    }

    /**
     * Get the underlying database connection.
	 * 获取底层数据库连接
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Get the cache key prefix.
	 * 获取缓存键前缀
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Serialize the given value.
	 * 序列化给定的值
     *
     * @param  mixed  $value
     * @return string
     */
    protected function serialize($value)
    {
        $result = serialize($value);

        if ($this->connection instanceof PostgresConnection && Str::contains($result, "\0")) {
            $result = base64_encode($result);
        }

        return $result;
    }

    /**
     * Unserialize the given value.
	 * 反序列化给定的值
     *
     * @param  string  $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        if ($this->connection instanceof PostgresConnection && ! Str::contains($value, [':', ';'])) {
            $value = base64_decode($value);
        }

        return unserialize($value);
    }
}
