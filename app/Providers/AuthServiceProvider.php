<?php
/**
 * App，供应商，认证服务提供商
 */

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
	 * 应用程序的策略映射
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
	 * 注册任何身份验证/授权服务
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
