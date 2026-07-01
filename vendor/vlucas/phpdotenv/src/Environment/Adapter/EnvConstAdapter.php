<?php
/**
 * Dotenv，环境，适配器，Env 常量适配器
 */

namespace Dotenv\Environment\Adapter;

use PhpOption\None;
use PhpOption\Some;

class EnvConstAdapter implements AdapterInterface
{
    /**
     * Determines if the adapter is supported.
	 * 确定适配器是否被支持
     *
     * @return bool
     */
    public function isSupported()
    {
        return true;
    }

    /**
     * Get an environment variable, if it exists.
	 * 获取环境变量，如果存在。
     *
     * @param string $name
     *
     * @return \PhpOption\Option
     */
    public function get($name)
    {
        if (array_key_exists($name, $_ENV)) {
            return Some::create($_ENV[$name]);
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
        $_ENV[$name] = $value;
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
        unset($_ENV[$name]);
    }
}
