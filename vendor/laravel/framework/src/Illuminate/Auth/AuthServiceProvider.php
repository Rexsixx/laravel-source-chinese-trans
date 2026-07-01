<?php
/**
 * Illuminate，认证，认证服务提供商
 */

namespace Illuminate\Auth;

use Illuminate\Auth\Access\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->registerAuthenticator();
        $this->registerUserResolver();
        $this->registerAccessGate();
        $this->registerRequestRebindHandler();
        $this->registerEventRebindHandler();
    }

    /**
     * Register the authenticator services.
	 * 注册验证器服务
     *
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('auth', function ($app) {
            // Once the authentication service has actually been requested by the developer
            // we will set a variable in the application indicating such. This helps us
            // know that we need to set any queued cookies in the after event later.
			// 一旦开发人员实际提出了身份验证服务的请求，我们就会在应用程序中设置一个变量来表明这一情况。
			// 这帮助我们知道,我们需要在以后的事件中设置任何排队的cookie。
            $app['auth.loaded'] = true;

            return new AuthManager($app);
        });

        $this->app->singleton('auth.driver', function ($app) {
            return $app['auth']->guard();
        });
    }

    /**
     * Register a resolver for the authenticated user.
	 * 为经过身份验证的用户注册一个解析器
     *
     * @return void
     */
    protected function registerUserResolver()
    {
        $this->app->bind(
            AuthenticatableContract::class, function ($app) {
                return call_user_func($app['auth']->userResolver());
            }
        );
    }

    /**
     * Register the access gate service.
	 * 注册门禁服务
     *
     * @return void
     */
    protected function registerAccessGate()
    {
        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () use ($app) {
                return call_user_func($app['auth']->userResolver());
            });
        });
    }

    /**
     * Handle the re-binding of the request binding.
	 * 处理请求绑定的重新绑定
     *
     * @return void
     */
    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function ($guard = null) use ($app) {
                return call_user_func($app['auth']->userResolver(), $guard);
            });
        });
    }

    /**
     * Handle the re-binding of the event dispatcher binding.
	 * 处理事件调度程序绑定的重新绑定
     *
     * @return void
     */
    protected function registerEventRebindHandler()
    {
        $this->app->rebinding('events', function ($app, $dispatcher) {
            if (! $app->resolved('auth')) {
                return;
            }

            if (method_exists($guard = $app['auth']->guard(), 'setDispatcher')) {
                $guard->setDispatcher($dispatcher);
            }
        });
    }
}
