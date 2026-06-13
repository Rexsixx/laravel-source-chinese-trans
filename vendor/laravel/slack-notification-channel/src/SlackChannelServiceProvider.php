<?php
/**
 * Illuminate，通知，Slack 通道服务提供商
 */

namespace Illuminate\Notifications;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class SlackChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        Notification::extend('slack', function ($app) {
            return new Channels\SlackWebhookChannel(new HttpClient);
        });
    }
}
