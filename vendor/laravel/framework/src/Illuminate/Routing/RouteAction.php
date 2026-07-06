<?php
/**
 * Illuminate，路由选择，路由动作
 */

namespace Illuminate\Routing;

use LogicException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use UnexpectedValueException;

class RouteAction
{
    /**
     * Parse the given action into an array.
	 * 将给定的动作解析为数组
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return array
     */
    public static function parse($uri, $action)
    {
        // If no action is passed in right away, we assume the user will make use of
        // fluent routing. In that case, we set a default closure, to be executed
        // if the user never explicitly sets an action to handle the given uri.
		// 如果立即没有传递任何操作信息，我们就假定用户会使用流畅的路由功能。
		// 在这种情况下，我们设置了默认的闭包，如果用户从未明确指定处理给定 URI 的操作，该闭包就会被执行。
        if (is_null($action)) {
            return static::missingAction($uri);
        }

        // If the action is already a Closure instance, we will just set that instance
        // as the "uses" property, because there is nothing else we need to do when
        // it is available. Otherwise we will need to find it in the action list.
		// 如果该操作已经是 Closure 类型的实例，我们就会将该实例设置为“使用”属性的值，因为一旦该实例可用，我们就无需再做其他处理了。
		// 否则我们需要在动作列表中找到它。
        if (is_callable($action, true)) {
            return ! is_array($action) ? ['uses' => $action] : [
                'uses' => $action[0].'@'.$action[1],
                'controller' => $action[0].'@'.$action[1],
            ];
        }

        // If no "uses" property has been set, we will dig through the array to find a
        // Closure instance within this list. We will set the first Closure we come
        // across into the "uses" property that will get fired off by this route.
		// 如果没有设置“uses”属性，我们将遍历该数组，查找其中的Closure实例。
		// 我们将把首次遇到的“闭包”对象设置到“使用”属性中，这样该属性就会由此路由触发执行。
        elseif (! isset($action['uses'])) {
            $action['uses'] = static::findCallable($action);
        }

        if (is_string($action['uses']) && ! Str::contains($action['uses'], '@')) {
            $action['uses'] = static::makeInvokable($action['uses']);
        }

        return $action;
    }

    /**
     * Get an action for a route that has no action.
	 * 为没有动作的路由获取一个动作
     *
     * @param  string  $uri
     * @return array
     */
    protected static function missingAction($uri)
    {
        return ['uses' => function () use ($uri) {
            throw new LogicException("Route for [{$uri}] has no action.");
        }];
    }

    /**
     * Find the callable in an action array.
	 * 在动作数组中查找可调用对象
     *
     * @param  array  $action
     * @return callable
     */
    protected static function findCallable(array $action)
    {
        return Arr::first($action, function ($value, $key) {
            return is_callable($value) && is_numeric($key);
        });
    }

    /**
     * Make an action for an invokable controller.
	 * 为可调用控制器创建一个操作
     *
     * @param  string $action
     * @return string
     *
     * @throws \UnexpectedValueException
     */
    protected static function makeInvokable($action)
    {
        if (! method_exists($action, '__invoke')) {
            throw new UnexpectedValueException("Invalid route action: [{$action}].");
        }

        return $action.'@__invoke';
    }
}
