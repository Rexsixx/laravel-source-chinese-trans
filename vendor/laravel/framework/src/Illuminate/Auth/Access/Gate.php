<?php
/**
 * Illuminate，Auth，访问，大门
 */

namespace Illuminate\Auth\Access;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class Gate implements GateContract
{
    use HandlesAuthorization;

    /**
     * The container instance.
	 * 容器实例
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The user resolver callable.
	 * 可调用的用户解析器
     *
     * @var callable
     */
    protected $userResolver;

    /**
     * All of the defined abilities.
	 * 所有已定义的能力
     *
     * @var array
     */
    protected $abilities = [];

    /**
     * All of the defined policies.
	 * 所有已定义的策略
     *
     * @var array
     */
    protected $policies = [];

    /**
     * All of the registered before callbacks.
	 * 所有在回调之前注册的
     *
     * @var array
     */
    protected $beforeCallbacks = [];

    /**
     * All of the registered after callbacks.
	 * 所有在回调后注册的
     *
     * @var array
     */
    protected $afterCallbacks = [];

    /**
     * Create a new gate instance.
	 * 创建一个新的门实例
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  callable  $userResolver
     * @param  array  $abilities
     * @param  array  $policies
     * @param  array  $beforeCallbacks
     * @param  array  $afterCallbacks
     * @return void
     */
    public function __construct(Container $container, callable $userResolver, array $abilities = [],
                                array $policies = [], array $beforeCallbacks = [], array $afterCallbacks = [])
    {
        $this->policies = $policies;
        $this->container = $container;
        $this->abilities = $abilities;
        $this->userResolver = $userResolver;
        $this->afterCallbacks = $afterCallbacks;
        $this->beforeCallbacks = $beforeCallbacks;
    }

    /**
     * Determine if a given ability has been defined.
	 * 确定是否已经定义了给定的能力
     *
     * @param  string|array  $ability
     * @return bool
     */
    public function has($ability)
    {
        $abilities = is_array($ability) ? $ability : func_get_args();

        foreach ($abilities as $ability) {
            if (! isset($this->abilities[$ability])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Define a new ability.
	 * 定义一个新能力
     *
     * @param  string  $ability
     * @param  callable|string  $callback
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function define($ability, $callback)
    {
        if (is_callable($callback)) {
            $this->abilities[$ability] = $callback;
        } elseif (is_string($callback) && Str::contains($callback, '@')) {
            $this->abilities[$ability] = $this->buildAbilityCallback($ability, $callback);
        } else {
            throw new InvalidArgumentException("Callback must be a callable or a 'Class@method' string.");
        }

        return $this;
    }

    /**
     * Define abilities for a resource.
	 * 定义资源的能力
     *
     * @param  string  $name
     * @param  string  $class
     * @param  array   $abilities
     * @return $this
     */
    public function resource($name, $class, array $abilities = null)
    {
        $abilities = $abilities ?: [
            'view'   => 'view',
            'create' => 'create',
            'update' => 'update',
            'delete' => 'delete',
        ];

        foreach ($abilities as $ability => $method) {
            $this->define($name.'.'.$ability, $class.'@'.$method);
        }

        return $this;
    }

    /**
     * Create the ability callback for a callback string.
	 * 为回调字符串创建能力回调
     *
     * @param  string  $ability
     * @param  string  $callback
     * @return \Closure
     */
    protected function buildAbilityCallback($ability, $callback)
    {
        return function () use ($ability, $callback) {
            list($class, $method) = Str::parseCallback($callback);

            $policy = $this->resolvePolicy($class);

            $arguments = func_get_args();

            $user = array_shift($arguments);

            $result = $this->callPolicyBefore(
                $policy, $user, $ability, $arguments
            );

            if (! is_null($result)) {
                return $result;
            }

            return $policy->{$method}(...func_get_args());
        };
    }

    /**
     * Define a policy class for a given class type.
	 * 为给定的类类型定义策略类
     *
     * @param  string  $class
     * @param  string  $policy
     * @return $this
     */
    public function policy($class, $policy)
    {
        $this->policies[$class] = $policy;

        return $this;
    }

    /**
     * Register a callback to run before all Gate checks.
	 * 注册一个回调，以便在所有Gate检查之前运行。
     *
     * @param  callable  $callback
     * @return $this
     */
    public function before(callable $callback)
    {
        $this->beforeCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to run after all Gate checks.
	 * 注册一个回调，在所有Gate检查之后运行。
     *
     * @param  callable  $callback
     * @return $this
     */
    public function after(callable $callback)
    {
        $this->afterCallbacks[] = $callback;

        return $this;
    }

    /**
     * Determine if the given ability should be granted for the current user.
	 * 确定是否应该为当前用户授予给定的能力
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function allows($ability, $arguments = [])
    {
        return $this->check($ability, $arguments);
    }

    /**
     * Determine if the given ability should be denied for the current user.
	 * 确定当前用户是否应该拒绝给定的能力
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function denies($ability, $arguments = [])
    {
        return ! $this->allows($ability, $arguments);
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
	 * 确定是否应该为当前用户授予所有给定的能力
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function check($abilities, $arguments = [])
    {
        return collect($abilities)->every(function ($ability) use ($arguments) {
            try {
                return (bool) $this->raw($ability, $arguments);
            } catch (AuthorizationException $e) {
                return false;
            }
        });
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
	 * 确定是否应该为当前用户授予给定的任何一种能力
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function any($abilities, $arguments = [])
    {
        return collect($abilities)->contains(function ($ability) use ($arguments) {
            return $this->check($ability, $arguments);
        });
    }

    /**
     * Determine if the given ability should be granted for the current user.
	 * 确定是否应该为当前用户授予给定的能力
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        $result = $this->raw($ability, $arguments);

        if ($result instanceof Response) {
            return $result;
        }

        return $result ? $this->allow() : $this->deny();
    }

    /**
     * Get the raw result from the authorization callback.
	 * 从授权回调获取原始结果
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return mixed
     */
    protected function raw($ability, $arguments = [])
    {
        if (! $user = $this->resolveUser()) {
            return false;
        }

        $arguments = Arr::wrap($arguments);

        // First we will call the "before" callbacks for the Gate. If any of these give
        // back a non-null response, we will immediately return that result in order
        // to let the developers override all checks for some authorization cases.
        $result = $this->callBeforeCallbacks(
            $user, $ability, $arguments
        );

        if (is_null($result)) {
            $result = $this->callAuthCallback($user, $ability, $arguments);
        }

        // After calling the authorization callback, we will call the "after" callbacks
        // that are registered with the Gate, which allows a developer to do logging
        // if that is required for this application. Then we'll return the result.
        $this->callAfterCallbacks(
            $user, $ability, $arguments, $result
        );

        return $result;
    }

    /**
     * Resolve and call the appropriate authorization callback.
	 * 解析并调用适当的授权回调
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    protected function callAuthCallback($user, $ability, array $arguments)
    {
        $callback = $this->resolveAuthCallback($user, $ability, $arguments);

        return $callback($user, ...$arguments);
    }

    /**
     * Call all of the before callbacks and return if a result is given.
	 * 调用所有的before回调函数，并在给出结果时返回。
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool|null
     */
    protected function callBeforeCallbacks($user, $ability, array $arguments)
    {
        $arguments = array_merge([$user, $ability], [$arguments]);

        foreach ($this->beforeCallbacks as $before) {
            if (! is_null($result = $before(...$arguments))) {
                return $result;
            }
        }
    }

    /**
     * Call all of the after callbacks with check result.
	 * 调用所有带有检查结果的after回调函数
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @param  bool  $result
     * @return void
     */
    protected function callAfterCallbacks($user, $ability, array $arguments, $result)
    {
        $arguments = array_merge([$user, $ability, $result], [$arguments]);

        foreach ($this->afterCallbacks as $after) {
            $after(...$arguments);
        }
    }

    /**
     * Resolve the callable for the given ability and arguments.
	 * 解析给定能力和参数的可调用对象
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return callable
     */
    protected function resolveAuthCallback($user, $ability, array $arguments)
    {
        if (isset($arguments[0]) &&
            ! is_null($policy = $this->getPolicyFor($arguments[0])) &&
            $callback = $this->resolvePolicyCallback($user, $ability, $arguments, $policy)) {
            return $callback;
        }

        if (isset($this->abilities[$ability])) {
            return $this->abilities[$ability];
        }

        return function () {
            return false;
        };
    }

    /**
     * Get a policy instance for a given class.
	 * 获取给定类的策略实例
     *
     * @param  object|string  $class
     * @return mixed
     */
    public function getPolicyFor($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (! is_string($class)) {
            return;
        }

        if (isset($this->policies[$class])) {
            return $this->resolvePolicy($this->policies[$class]);
        }

        foreach ($this->policies as $expected => $policy) {
            if (is_subclass_of($class, $expected)) {
                return $this->resolvePolicy($policy);
            }
        }
    }

    /**
     * Build a policy class instance of the given type.
	 * 构建给定类型的策略类实例
     *
     * @param  object|string  $class
     * @return mixed
     */
    public function resolvePolicy($class)
    {
        return $this->container->make($class);
    }

    /**
     * Resolve the callback for a policy check.
	 * 解析策略检查的回调
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @param  mixed  $policy
     * @return bool|callable
     */
    protected function resolvePolicyCallback($user, $ability, array $arguments, $policy)
    {
        if (! is_callable([$policy, $this->formatAbilityToMethod($ability)])) {
            return false;
        }

        return function () use ($user, $ability, $arguments, $policy) {
            // This callback will be responsible for calling the policy's before method and
            // running this policy method if necessary. This is used to when objects are
            // mapped to policy objects in the user's configurations or on this class.
            $result = $this->callPolicyBefore(
                $policy, $user, $ability, $arguments
            );

            // When we receive a non-null result from this before method, we will return it
            // as the "final" results. This will allow developers to override the checks
            // in this policy to return the result for all rules defined in the class.
            if (! is_null($result)) {
                return $result;
            }

            $ability = $this->formatAbilityToMethod($ability);

            // If this first argument is a string, that means they are passing a class name
            // to the policy. We will remove the first argument from this argument array
            // because this policy already knows what type of models it can authorize.
            if (isset($arguments[0]) && is_string($arguments[0])) {
                array_shift($arguments);
            }

            return is_callable([$policy, $ability])
                        ? $policy->{$ability}($user, ...$arguments)
                        : false;
        };
    }

    /**
     * Call the "before" method on the given policy, if applicable.
	 * 在给定策略上调用“before”方法（如果适用）
     *
     * @param  mixed  $policy
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return mixed
     */
    protected function callPolicyBefore($policy, $user, $ability, $arguments)
    {
        if (method_exists($policy, 'before')) {
            return $policy->before($user, $ability, ...$arguments);
        }
    }

    /**
     * Format the policy ability into a method name.
	 * 将策略功能格式化为方法名称
     *
     * @param  string  $ability
     * @return string
     */
    protected function formatAbilityToMethod($ability)
    {
        return strpos($ability, '-') !== false ? Str::camel($ability) : $ability;
    }

    /**
     * Get a gate instance for the given user.
	 * 获取给定用户的gate实例
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @return static
     */
    public function forUser($user)
    {
        $callback = function () use ($user) {
            return $user;
        };

        return new static(
            $this->container, $callback, $this->abilities,
            $this->policies, $this->beforeCallbacks, $this->afterCallbacks
        );
    }

    /**
     * Resolve the user from the user resolver.
	 * 从用户解析器中解析用户
     *
     * @return mixed
     */
    protected function resolveUser()
    {
        return call_user_func($this->userResolver);
    }

    /**
     * Get all of the defined abilities.
	 * 获得所有已定义的能力
     *
     * @return array
     */
    public function abilities()
    {
        return $this->abilities;
    }

    /**
     * Get all of the defined policies.
	 * 获取所有已定义的策略
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }
}
