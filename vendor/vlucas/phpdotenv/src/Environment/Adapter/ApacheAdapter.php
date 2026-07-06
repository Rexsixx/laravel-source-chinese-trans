<?php
/**
 * Dotenv，环境，适配器，Apache 适配器
 */

namespace Dotenv\Environment\Adapter;

use PhpOption\None;

class ApacheAdapter implements AdapterInterface
{
    /**
     * Determines if the adapter is supported.
	 * 确定适配器是否被支持。
     *
     * This happens if PHP is running as an Apache module.
	 * 如果PHP运行为Apache模块,则会发生这种情况。
     *
     * @return bool
     */
    public function isSupported()
    {
        return function_exists('apache_getenv') && function_exists('apache_setenv');
    }

    /**
     * Get an environment variable, if it exists.
	 * 获取环境变量，如果存在。
     *
     * This is intentionally not implemented, since this adapter exists only as
     * a means to overwrite existing apache environment variables.
     *
     * @param string $name
     *
     * @return \PhpOption\Option
     */
    public function get($name)
    {
        return None::create();
    }

    /**
     * Set an environment variable.
	 * 设置环境变量。
     *
     * Only if an existing apache variable exists do we overwrite it.
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function set($name, $value = null)
    {
        if (apache_getenv($name) !== false) {
            apache_setenv($name, (string) $value);
        }
    }

    /**
     * Clear an environment variable.
	 * 清除一个环境变量
     *
     * @param string $name
     *
     * @return void
     */
    public function clear($name)
    {
        // Nothing to do here.
    }
}
