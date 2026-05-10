<?php
/**
 * Illuminate，支持，高阶排序代理
 */

namespace Illuminate\Support;

class HigherOrderTapProxy
{
    /**
     * The target being tapped.
	 * 目标被发掘
     *
     * @var mixed
     */
    public $target;

    /**
     * Create a new tap proxy instance.
	 * 创建一个新的tap代理实例
     *
     * @param  mixed  $target
     * @return void
     */
    public function __construct($target)
    {
        $this->target = $target;
    }

    /**
     * Dynamically pass method calls to the target.
	 * 动态通过方法调用目标
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->target->{$method}(...$parameters);

        return $this->target;
    }
}
