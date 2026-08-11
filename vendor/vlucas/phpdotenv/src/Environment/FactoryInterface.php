<?php
/**
 * Dotenv，环境，工厂接口
 */

namespace Dotenv\Environment;

/**
 * This environment factory interface.
 * 这个环境工厂接口。
 *
 * If you need custom implementations of the variables interface, implement
 * this interface, and use your implementation in the loader.
 * 如果需要自定义变量接口的实现，请实现此接口，并在加载器中使用你的实现。
 */
interface FactoryInterface
{
    /**
     * Creates a new mutable environment variables instance.
	 * 创建一个新的可变环境变量实例
     *
     * @return \Dotenv\Environment\VariablesInterface
     */
    public function create();

    /**
     * Creates a new immutable environment variables instance.
	 * 创建一个新的不可变环境变量实例
     *
     * @return \Dotenv\Environment\VariablesInterface
     */
    public function createImmutable();
}
