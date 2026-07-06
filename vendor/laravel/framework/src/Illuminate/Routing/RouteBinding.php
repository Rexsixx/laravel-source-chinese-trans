<?php
/**
 * Illuminate，路由选择，路由绑定
 */

namespace Illuminate\Routing;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RouteBinding
{
    /**
     * Create a Route model binding for a given callback.
	 * 为给定的回调创建一个Route模型绑定
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  \Closure|string  $binder
     * @return \Closure
     */
    public static function forCallback($container, $binder)
    {
        if (is_string($binder)) {
            return static::createClassBinding($container, $binder);
        }

        return $binder;
    }

    /**
     * Create a class based binding using the IoC container.
	 * 使用IoC容器创建基于类的绑定
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  string  $binding
     * @return \Closure
     */
    protected static function createClassBinding($container, $binding)
    {
        return function ($value, $route) use ($container, $binding) {
            // If the binding has an @ sign, we will assume it's being used to delimit
            // the class name from the bind method name. This allows for bindings
            // to run multiple bind methods in a single class for convenience.
			// 如果绑定中带有“@”符号，我们就认为它是用于将类名与绑定方法名分隔开的。
			// 这样可以方便地在一个类中调用多个绑定方法。
            [$class, $method] = Str::parseCallback($binding, 'bind');

            $callable = [$container->make($class), $method];

            return call_user_func($callable, $value, $route);
        };
    }

    /**
     * Create a Route model binding for a model.
	 * 为模型创建一个Route模型绑定
     *
     * @param  \Illuminate\Container\Container  $container
     * @param  string  $class
     * @param  \Closure|null  $callback
     * @return \Closure
     */
    public static function forModel($container, $class, $callback = null)
    {
        return function ($value) use ($container, $class, $callback) {
            if (is_null($value)) {
                return;
            }

            // For model binders, we will attempt to retrieve the models using the first
            // method on the model instance. If we cannot retrieve the models we'll
            // throw a not found exception otherwise we will return the instance.
			// 对于模型绑定器而言，我们将尝试使用模型实例上的第一种方法来获取模型。
			// 如果我们无法获取这些模型，就会抛出“未找到”的异常；否则，我们将返回该实例。
            $instance = $container->make($class);

            if ($model = $instance->resolveRouteBinding($value)) {
                return $model;
            }

            // If a callback was supplied to the method we will call that to determine
            // what we should do when the model is not found. This just gives these
            // developer a little greater flexibility to decide what will happen.
			// 如果为该方法提供了回调函数，我们将调用该回调函数来确定当模型未找到时我们应采取何种行动。
			// 这只是为这些开发者提供了一定程度的灵活性，以便他们能够自行决定会发生何种情况。
            if ($callback instanceof Closure) {
                return call_user_func($callback, $value);
            }

            throw (new ModelNotFoundException)->setModel($class);
        };
    }
}
