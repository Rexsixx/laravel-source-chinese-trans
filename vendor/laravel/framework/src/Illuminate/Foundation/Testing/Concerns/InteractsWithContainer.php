<?php
/**
 * Illuminate，基础，测试，问题，与容器交互
 */

namespace Illuminate\Foundation\Testing\Concerns;

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
}
