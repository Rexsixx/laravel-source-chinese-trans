<?php
/**
 * Illuminate，基础，支持，供应商，事件服务提供商
 */

namespace Illuminate\Foundation\Support\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
	 * 应用程序的事件处理程序映射
     *
     * @var array
     */
    protected $listen = [];

    /**
     * The subscriber classes to register.
	 * 要注册的订阅者类
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the application's event listeners.
	 * 注册应用程序的事件侦听器
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->listens() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
	 * 获取事件和处理程序
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
