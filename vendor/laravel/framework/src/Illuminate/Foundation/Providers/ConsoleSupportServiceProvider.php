<?php
/**
 * Illuminate，基础，供应商，控制台支持服务提供商
 */

namespace Illuminate\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Database\MigrationServiceProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
	 * 指示是否延迟加载提供程序
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The provider class names.
	 * 提供程序类名
     *
     * @var array
     */
    protected $providers = [
        ArtisanServiceProvider::class,
        MigrationServiceProvider::class,
        ComposerServiceProvider::class,
    ];
}
