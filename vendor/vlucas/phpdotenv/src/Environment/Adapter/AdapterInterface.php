<?php
/**
 * Dotenv，环境，适配器，适配器接口
 */

namespace Dotenv\Environment\Adapter;

interface AdapterInterface
{
    /**
     * Determines if the adapter is supported.
	 * 确定适配器是否被支持
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Get an environment variable, if it exists.
	 * 获取环境变量（如果存在）
     *
     * @param string $name
     *
     * @return \PhpOption\Option
     */
    public function get($name);

    /**
     * Set an environment variable.
	 * 设置环境变量
     *
     * @param string      $name
     * @param string|null $value
     *
     * @return void
     */
    public function set($name, $value = null);

    /**
     * Clear an environment variable.
	 * 清除一个环境变量
     *
     * @param string $name
     *
     * @return void
     */
    public function clear($name);
}
