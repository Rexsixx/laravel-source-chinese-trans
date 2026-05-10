<?php
/**
 * Illuminate，支持，高级订单收集代理
 */

namespace Illuminate\Support;

/**
 * @mixin \Illuminate\Support\Collection
 */
class HigherOrderCollectionProxy
{
    /**
     * The collection being operated on.
	 * 收集的集合正在进行
     *
     * @var \Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * The method being proxied.
	 * 这种方法正在被证实
     *
     * @var string
     */
    protected $method;

    /**
     * Create a new proxy instance.
	 * 创建一个新的代理实例
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  string  $method
     * @return void
     */
    public function __construct(Collection $collection, $method)
    {
        $this->method = $method;
        $this->collection = $collection;
    }

    /**
     * Proxy accessing an attribute onto the collection items.
	 * 代理访问集合项上的属性
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->collection->{$this->method}(function ($value) use ($key) {
            return is_array($value) ? $value[$key] : $value->{$key};
        });
    }

    /**
     * Proxy a method call onto the collection items.
	 * 代理一个方法调用集合项
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->collection->{$this->method}(function ($value) use ($method, $parameters) {
            return $value->{$method}(...$parameters);
        });
    }
}
