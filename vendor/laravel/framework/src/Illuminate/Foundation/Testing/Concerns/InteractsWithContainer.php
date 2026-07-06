<?php
/**
 * Illuminate，基础，测试，问题，与容器交互
 */

namespace Illuminate\Foundation\Testing\Concerns;

use Closure;
use Mockery;

trait InteractsWithContainer
{
    /**
     * Register an instance of an object in the container.
	 * 在容器中注册对象的实例
     *
     * @param  string  $abstract
     * @param  object  $instance
     * @return object
     */
    protected function swap($abstract, $instance)
    {
        return $this->instance($abstract, $instance);
    }

    /**
     * Register an instance of an object in the container.
	 * 在容器中注册对象的实例
     *
     * @param  string  $abstract
     * @param  object  $instance
     * @return object
     */
    protected function instance($abstract, $instance)
    {
        $this->app->instance($abstract, $instance);

        return $instance;
    }

    /**
     * Mock an instance of an object in the container.
	 * 模拟容器中对象的实例
     *
     * @param  string  $abstract
     * @param  \Closure|null  $mock
     * @return object
     */
    protected function mock($abstract, Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
    }

    /**
     * Spy an instance of an object in the container.
	 * 监视容器中对象的实例
     *
     * @param  string  $abstract
     * @param  \Closure|null  $mock
     * @return object
     */
    protected function spy($abstract, Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::spy(...array_filter(func_get_args())));
    }
}
