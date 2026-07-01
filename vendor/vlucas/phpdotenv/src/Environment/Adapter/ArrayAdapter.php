<?php
/**
 * Dotenv，环境，适配器，数组适配器
 */

namespace Dotenv\Environment\Adapter;

use PhpOption\None;
use PhpOption\Some;

class ArrayAdapter implements AdapterInterface
{
    /**
     * The variables and their values.
	 * 变量及其值
     *
     * @return array<string|null>
     */
    private $variables = [];

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
	 * 获取环境变量（如果存在）
     *
     * @param string $name
     *
     * @return \PhpOption\Option
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->variables)) {
            return Some::create($this->variables[$name]);
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
        $this->variables[$name] = $value;
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
        unset($this->variables[$name]);
    }
}
