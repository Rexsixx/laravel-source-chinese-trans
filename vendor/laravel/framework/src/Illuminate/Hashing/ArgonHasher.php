<?php
/**
 * Illuminate，哈希，Argon 哈希
 */

namespace Illuminate\Hashing;

use RuntimeException;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class ArgonHasher extends AbstractHasher implements HasherContract
{
    /**
     * The default memory cost factor.
	 * 默认内存成本因子
     *
     * @var int
     */
    protected $memory = 1024;

    /**
     * The default time cost factor.
	 * 默认的时间成本因子
     *
     * @var int
     */
    protected $time = 2;

    /**
     * The default threads factor.
	 * 默认的线程因子
     *
     * @var int
     */
    protected $threads = 2;

    /**
     * Create a new hasher instance.
	 * 创建一个新的散列实例
     *
     * @param  array  $options
     * @return void
     */
    public function __construct(array $options = [])
    {
        $this->time = $options['time'] ?? $this->time;
        $this->memory = $options['memory'] ?? $this->memory;
        $this->threads = $options['threads'] ?? $this->threads;
    }

    /**
     * Hash the given value.
	 * 对给定值进行散列
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     *
     * @throws \RuntimeException
     */
    public function make($value, array $options = [])
    {
        $hash = password_hash($value, PASSWORD_ARGON2I, [
            'memory_cost' => $this->memory($options),
            'time_cost' => $this->time($options),
            'threads' => $this->threads($options),
        ]);

        if ($hash === false) {
            throw new RuntimeException('Argon2 hashing not supported.');
        }

        return $hash;
    }

    /**
     * Check if the given hash has been hashed using the given options.
	 * 检查给定的散列是否已经使用给定的选项进行了散列
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return password_needs_rehash($hashedValue, PASSWORD_ARGON2I, [
            'memory_cost' => $this->memory($options),
            'time_cost' => $this->time($options),
            'threads' => $this->threads($options),
        ]);
    }

    /**
     * Set the default password memory factor.
	 * 设置默认密码内存系数
     *
     * @param  int  $memory
     * @return $this
     */
    public function setMemory(int $memory)
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * Set the default password timing factor.
	 * 设置默认密码定时因子
     *
     * @param  int  $time
     * @return $this
     */
    public function setTime(int $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Set the default password threads factor.
	 * 设置默认密码线程因子
     *
     * @param  int  $threads
     * @return $this
     */
    public function setThreads(int $threads)
    {
        $this->threads = $threads;

        return $this;
    }

    /**
     * Extract the memory cost value from the options array.
	 * 从选项数组中提取内存成本值
     *
     * @param  array  $options
     * @return int
     */
    protected function memory(array $options)
    {
        return $options['memory'] ?? $this->memory;
    }

    /**
     * Extract the time cost value from the options array.
	 * 从选项数组中提取时间成本值
     *
     * @param  array  $options
     * @return int
     */
    protected function time(array $options)
    {
        return $options['time'] ?? $this->time;
    }

    /**
     * Extract the threads value from the options array.
	 * 从选项数组中提取线程值
     *
     * @param  array  $options
     * @return int
     */
    protected function threads(array $options)
    {
        return $options['threads'] ?? $this->threads;
    }
}
