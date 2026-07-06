<?php
/**
 * Illuminate，支持，门面，Config
 */

namespace Illuminate\Support\Facades;

/**
 * @method static bool has($key)
 * @method static mixed get($key, $default = null)
 * @method static array all()
 * @method static void set($key, $value = null)
 * @method static void prepend($key, $value)
 * @method static void push($key, $value)
 *
 * @see \Illuminate\Config\Repository
 */
class Config extends Facade
{
    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'config';
    }
}
