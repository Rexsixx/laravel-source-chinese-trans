<?php
/**
 * Illuminate，支持，Carbon
 */

namespace Illuminate\Support;

use JsonSerializable;
use Carbon\Carbon as BaseCarbon;
use Illuminate\Support\Traits\Macroable;

class Carbon extends BaseCarbon implements JsonSerializable
{
    use Macroable;

    /**
     * The custom Carbon JSON serializer.
	 * 自定义Carbon JSON序列化器
     *
     * @var callable|null
     */
    protected static $serializer;

    /**
     * Prepare the object for JSON serialization.
	 * 为JSON序列化准备对象
     *
     * @return array|string
     */
    public function jsonSerialize()
    {
        if (static::$serializer) {
            return call_user_func(static::$serializer, $this);
        }

        $carbon = $this;

        return call_user_func(function () use ($carbon) {
            return get_object_vars($carbon);
        });
    }

    /**
     * JSON serialize all Carbon instances using the given callback.
	 * JSON使用给定的回调序列化所有Carbon实例
     *
     * @param  callable  $callback
     * @return void
     */
    public static function serializeUsing($callback)
    {
        static::$serializer = $callback;
    }
}
