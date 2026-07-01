<?php
/**
 * Illuminate，基础，供应商，控制台支持服务提供商
 */

namespace Illuminate\Foundation\Providers;

use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Database\MigrationServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class ConsoleSupportServiceProvider extends AggregateServiceProvider implements DeferrableProvider
{
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
