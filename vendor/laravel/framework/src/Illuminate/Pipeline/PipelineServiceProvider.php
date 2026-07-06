<?php
/**
 * Illuminate，管道，管道服务提供商
 */

namespace Illuminate\Pipeline;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Contracts\Pipeline\Hub as PipelineHubContract;

class PipelineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            PipelineHubContract::class, Hub::class
        );
    }

    /**
     * Get the services provided by the provider.
	 * 获取提供者提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return [
            PipelineHubContract::class,
        ];
    }
}
