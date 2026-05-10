<?php
/**
 * Illuminate，基础，供应商，Composer 服务提供商
 */

namespace Illuminate\Foundation\Providers;

use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
	 * 指示是否延迟加载提供程序
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('composer', function ($app) {
            return new Composer($app['files'], $app->basePath());
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
        return ['composer'];
    }
}
