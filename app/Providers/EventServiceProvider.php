<?php
/**
 * App，供应商，事件服务提供商
 */

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
	 * 应用程序的事件侦听器映射
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
	 * 为您的应用程序注册任何事件
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
