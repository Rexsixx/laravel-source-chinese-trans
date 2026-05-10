<?php
/**
 * Illuminate，Redis，连接，Php Redis 连接
 */

namespace Illuminate\Redis\Connections;

use Redis;
use Closure;
use Illuminate\Contracts\Redis\Connection as ConnectionContract;

/**
 * @mixin \Redis
 */
class PhpRedisConnection extends Connection implements ConnectionContract
{
    /**
     * Create a new PhpRedis connection.
	 * 创建一个新的PhpRedis连接
     *
     * @param  \Redis  $client
     * @return void
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Returns the value of the given key.
	 * 返回给定键的值
     *
     * @param  string  $key
     * @return string|null
     */
    public function get($key)
    {
        $result = $this->client->get($key);

        return $result !== false ? $result : null;
    }

    /**
     * Get the values of all the given keys.
	 * 获取所有给定键的值
     *
     * @param  array  $keys
     * @return array
     */
    public function mget(array $keys)
    {
        return array_map(function ($value) {
            return $value !== false ? $value : null;
        }, $this->client->mget($keys));
    }

    /**
     * Determine if the given keys exist.
	 * 确定给定的键是否存在
     *
     * @param  dynamic  $keys
     * @return int
     */
    public function exists(...$keys)
    {
        $keys = collect($keys)->map(function ($key) {
            return $this->applyPrefix($key);
        })->all();

        return $this->executeRaw(array_merge(['exists'], $keys));
    }

    /**
     * Set the string value in argument as value of the key.
	 * 将字符串值设置为参数作为键的值
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $expireResolution
     * @param  int|null  $expireTTL
     * @param  string|null  $flag
     * @return bool
     */
    public function set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
    {
        return $this->command('set', [
            $key,
            $value,
            $expireResolution ? [$flag, $expireResolution => $expireTTL] : null,
        ]);
    }

    /**
     * Set the given key if it doesn't exist.
	 * 如果它不存在,设置给定的键。
     *
     * @param  string  $key
     * @param  string  $value
     * @return int
     */
    public function setnx($key, $value)
    {
        return (int) $this->client->setnx($key, $value);
    }

    /**
     * Get the value of the given hash fields.
	 * 获取给定哈希字段的值
     *
     * @param  string  $key
     * @param  dynamic  $dictionary
     * @return int
     */
    public function hmget($key, ...$dictionary)
    {
        if (count($dictionary) == 1) {
            $dictionary = $dictionary[0];
        }

        return array_values($this->command('hmget', [$key, $dictionary]));
    }

    /**
     * Set the given hash fields to their respective values.
	 * 将给定的哈希字段设置为各自的值
     *
     * @param  string  $key
     * @param  dynamic  $dictionary
     * @return int
     */
    public function hmset($key, ...$dictionary)
    {
        if (count($dictionary) == 1) {
            $dictionary = $dictionary[0];
        } else {
            $input = collect($dictionary);

            $dictionary = $input->nth(2)->combine($input->nth(2, 1))->toArray();
        }

        return $this->command('hmset', [$key, $dictionary]);
    }

    /**
     * Set the given hash field if it doesn't exist.
	 * 如果不存在,设置给定的哈希字段。
     *
     * @param  string  $hash
     * @param  string  $key
     * @param  string  $value
     * @return int
     */
    public function hsetnx($hash, $key, $value)
    {
        return (int) $this->client->hSetNx($hash, $key, $value);
    }

    /**
     * Removes the first count occurrences of the value element from the list.
	 * 从列表中删除第一个值元素的发生
     *
     * @param  string  $key
     * @param  int  $count
     * @param  $value  $value
     * @return int|false
     */
    public function lrem($key, $count, $value)
    {
        return $this->command('lrem', [$key, $value, $count]);
    }

    /**
     * Removes and returns a random element from the set value at key.
	 * 从设置值中删除并返回一个随机元素
     *
     * @param  string  $key
     * @param  int|null  $count
     * @return mixed|false
     */
    public function spop($key, $count = null)
    {
        return $this->command('spop', [$key]);
    }

    /**
     * Add one or more members to a sorted set or update its score if it already exists.
	 * 如果已经存在,将一个或多个成员添加到一个排序集或更新它的分数。
     *
     * @param  string  $key
     * @param  dynamic  $dictionary
     * @return int
     */
    public function zadd($key, ...$dictionary)
    {
        if (is_array(end($dictionary))) {
            foreach (array_pop($dictionary) as $member => $score) {
                $dictionary[] = $score;
                $dictionary[] = $member;
            }
        }

        $key = $this->applyPrefix($key);

        return $this->executeRaw(array_merge(['zadd', $key], $dictionary));
    }

    /**
     * Return elements with score between $min and $max.
	 * 返回元素值在$ min到$ max之间
     *
     * @param  string  $key
     * @param  mixed  $min
     * @param  mixed  $max
     * @param  array  $options
     * @return int
     */
    public function zrangebyscore($key, $min, $max, $options = [])
    {
        if (isset($options['limit'])) {
            $options['limit'] = [
                $options['limit']['offset'],
                $options['limit']['count'],
            ];
        }

        return $this->command('zRangeByScore', [$key, $min, $max, $options]);
    }

    /**
     * Return elements with score between $min and $max.
	 * 返回元素值在$ min到$ max之间
     *
     * @param  string  $key
     * @param  mixed  $min
     * @param  mixed  $max
     * @param  array  $options
     * @return int
     */
    public function zrevrangebyscore($key, $min, $max, $options = [])
    {
        if (isset($options['limit'])) {
            $options['limit'] = [
                $options['limit']['offset'],
                $options['limit']['count'],
            ];
        }

        return $this->command('zRevRangeByScore', [$key, $min, $max, $options]);
    }

    /**
     * Find the intersection between sets and store in a new set.
	 * 在新设置中找到设置和存储之间的交集
     *
     * @param  string  $output
     * @param  array  $keys
     * @param  array  $options
     * @return int
     */
    public function zinterstore($output, $keys, $options = [])
    {
        return $this->zInter($output, $keys,
            $options['weights'] ?? null,
            $options['aggregate'] ?? 'sum'
        );
    }

    /**
     * Find the union between sets and store in a new set.
	 * 在新设置中找到设置和存储之间的结合
     *
     * @param  string  $output
     * @param  array  $keys
     * @param  array  $options
     * @return int
     */
    public function zunionstore($output, $keys, $options = [])
    {
        return $this->zUnion($output, $keys,
            $options['weights'] ?? null,
            $options['aggregate'] ?? 'sum'
        );
    }

    /**
     * Execute commands in a pipeline.
	 * 在管道中执行命令
     *
     * @param  callable  $callback
     * @return \Redis|array
     */
    public function pipeline(callable $callback = null)
    {
        $pipeline = $this->client()->pipeline();

        return is_null($callback)
            ? $pipeline
            : tap($pipeline, $callback)->exec();
    }

    /**
     * Execute commands in a transaction.
	 * 在事务中执行命令
     *
     * @param  callable  $callback
     * @return \Redis|array
     */
    public function transaction(callable $callback = null)
    {
        $transaction = $this->client()->multi();

        return is_null($callback)
            ? $transaction
            : tap($transaction, $callback)->exec();
    }

    /**
     * Evaluate a LUA script serverside, from the SHA1 hash of the script instead of the script itself.
	 * 通过脚本的SHA1哈希来评估脚本serverside,而不是脚本本身。
     *
     * @param  string  $script
     * @param  int  $numkeys
     * @param  mixed  $arguments
     * @return mixed
     */
    public function evalsha($script, $numkeys, ...$arguments)
    {
        return $this->command('evalsha', [
            $this->script('load', $script), $arguments, $numkeys,
        ]);
    }

    /**
     * Evaluate a script and return its result.
	 * 评估一个脚本并返回它的结果
     *
     * @param  string  $script
     * @param  int  $numberOfKeys
     * @param  dynamic  $arguments
     * @return mixed
     */
    public function eval($script, $numberOfKeys, ...$arguments)
    {
        return $this->client->eval($script, $arguments, $numberOfKeys);
    }

    /**
     * Subscribe to a set of given channels for messages.
	 * 订阅一组给定的消息通道
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @return void
     */
    public function subscribe($channels, Closure $callback)
    {
        $this->client->subscribe((array) $channels, function ($redis, $channel, $message) use ($callback) {
            $callback($message, $channel);
        });
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
        $this->client->psubscribe((array) $channels, function ($redis, $pattern, $channel, $message) use ($callback) {
            $callback($message, $channel);
        });
    }

    /**
     * Subscribe to a set of given channels for messages.
	 * 订阅一组给定的消息通道
     *
     * @param  array|string  $channels
     * @param  \Closure  $callback
     * @param  string  $method
     * @return void
     */
    public function createSubscription($channels, Closure $callback, $method = 'subscribe')
    {
        //
    }

    /**
     * Execute a raw command.
	 * 执行原始命令
     *
     * @param  array  $parameters
     * @return mixed
     */
    public function executeRaw(array $parameters)
    {
        return $this->command('rawCommand', $parameters);
    }

    /**
     * Disconnects from the Redis instance.
	 * 从Redis实例中断开连接
     *
     * @return void
     */
    public function disconnect()
    {
        $this->client->close();
    }

    /**
     * Apply prefix to the given key if necessary.
	 * 如有必要,将前缀应用于给定键
     *
     * @param  string  $key
     * @return string
     */
    private function applyPrefix($key)
    {
        $prefix = (string) $this->client->getOption(Redis::OPT_PREFIX);

        return $prefix.$key;
    }

    /**
     * Pass other method calls down to the underlying client.
	 * 通过其他方法调用底层客户端
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method = strtolower($method);

        return parent::__call($method, $parameters);
    }
}
