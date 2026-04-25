<?php
/**
 * Illuminate，缓存，事件，主键写入
 */

namespace Illuminate\Cache\Events;

class KeyWritten extends CacheEvent
{
    /**
     * The value that was written.
	 * 写入的值
     *
     * @var mixed
     */
    public $value;

    /**
     * The number of minutes the key should be valid.
	 * 密钥应该有效的分钟数
     *
     * @var int
     */
    public $minutes;

    /**
     * Create a new event instance.
	 * 创建一个新的事件实例
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $minutes
     * @param  array  $tags
     * @return void
     */
    public function __construct($key, $value, $minutes, $tags = [])
    {
        parent::__construct($key, $tags);

        $this->value = $value;
        $this->minutes = $minutes;
    }
}
