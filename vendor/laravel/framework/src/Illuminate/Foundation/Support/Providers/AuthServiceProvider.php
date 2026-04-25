<?php
/**
 * Illuminate，基础，支持，供应商，认证服务提供商
 */

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
	 * 应用程序的策略映射
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register the application's policies.
	 * 注册应用程序的策略
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }
}
