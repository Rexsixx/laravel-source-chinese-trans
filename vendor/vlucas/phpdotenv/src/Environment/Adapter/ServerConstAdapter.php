<?php
/**
 * Dotenv，环境，适配器，服务器常量适配器
 */

namespace Dotenv\Environment\Adapter;

use PhpOption\None;
use PhpOption\Some;

class ServerConstAdapter implements AdapterInterface
{
    /**
     * Determines if the adapter is supported.
	 * 确定是否支持适配器
     *
     * @return bool
     */
    public function isSupported()
    {
        return true;
    }

    /**
     * Get an environment variable, if it exists.
	 * 如果存在,就得到一个环境变量。
     *
     * @param string $name
     *
     * @return \PhpOption\Option
     */
    public function get($name)
    {
        if (array_key_exists($name, $_SERVER)) {
            return Some::create($_SERVER[$name]);
        }

        return None::create();
    }

    /**
     * Set an environment variable.
	 * 设置环境变量
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function set($name, $value = null)
    {
        $_SERVER[$name] = $value;
    }

    /**
     * Clear an environment variable.
     *
     * @param string $name
     *
     * @return void
     */
    public function clear($name)
    {
        unset($_SERVER[$name]);
    }
}
