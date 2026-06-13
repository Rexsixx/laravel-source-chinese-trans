<?php
/**
 * Illuminate，路由，路由绑定
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
			// 如果绑定具有“@”符号，我们将假定它用于将类名与绑定方法名分隔开。
			// 这使得可以在单个类中方便地运行多个绑定方法。
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
			// 对于模型绑定器，我们将尝试使用模型实例上的第一个方法来获取模型。
			// 如果无法获取模型，我们将抛出“未找到”异常；否则，我们将返回该实例。
            $instance = $container->make($class);

            if ($model = $instance->resolveRouteBinding($value)) {
                return $model;
            }

            // If a callback was supplied to the method we will call that to determine
            // what we should do when the model is not found. This just gives these
            // developer a little greater flexibility to decide what will happen.
			// 如果为该方法提供了回调函数，我们将调用该回调函数以确定在模型未找到的情况下我们应采取何种操作。
			// 这给了这些开发人员更大的灵活性来决定会发生什么。
            if ($callback instanceof Closure) {
                return call_user_func($callback, $value);
            }

            throw (new ModelNotFoundException)->setModel($class);
        };
    }
}
