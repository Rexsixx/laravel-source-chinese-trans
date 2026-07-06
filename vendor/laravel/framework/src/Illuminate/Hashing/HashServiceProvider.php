<?php
/**
 * Illuminate，哈希算法，哈希服务提供商
 */

namespace Illuminate\Hashing;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class HashServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('hash', function ($app) {
            return new HashManager($app);
        });

        $this->app->singleton('hash.driver', function ($app) {
            return $app['hash']->driver();
        });
    }

    /**
     * Get the services provided by the provider.
	 * 获取提供者提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return ['hash', 'hash.driver'];
    }
}
