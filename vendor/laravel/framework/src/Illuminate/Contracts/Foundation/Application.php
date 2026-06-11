<?php
/**
 * Illuminate，契约，基础，应用
 */

namespace Illuminate\Contracts\Foundation;

use Illuminate\Contracts\Container\Container;

interface Application extends Container
{
    /**
     * Get the version number of the application.
	 * 获取应用程序的版本号
     *
     * @return string
     */
    public function version();

    /**
     * Get the base path of the Laravel installation.
	 * 获取Laravel安装的基本路径
     *
     * @return string
     */
    public function basePath();

    /**
     * Get or check the current application environment.
	 * 获取或检查当前应用程序环境
     *
     * @return string
     */
    public function environment();

    /**
     * Determine if the application is running in the console.
	 * 确定应用程序是否在控制台中运行
     *
     * @return bool
     */
    public function runningInConsole();

    /**
     * Determine if the application is running unit tests.
	 * 确定应用程序是否正在运行单元测试
     *
     * @return bool
     */
    public function runningUnitTests();

    /**
     * Determine if the application is currently down for maintenance.
	 * 确定应用程序当前是否关闭以进行维护
     *
     * @return bool
     */
    public function isDownForMaintenance();

    /**
     * Register all of the configured providers.
	 * 注册所有已配置的提供程序
     *
     * @return void
     */
    public function registerConfiguredProviders();

    /**
     * Register a service provider with the application.
	 * 向应用程序注册一个服务提供者
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  bool   $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $force = false);

    /**
     * Register a deferred provider and service.
	 * 注册一个延迟的提供者和服务
     *
     * @param  string  $provider
     * @param  string|null  $service
     * @return void
     */
    public function registerDeferredProvider($provider, $service = null);

    /**
     * Boot the application's service providers.
	 * 引导应用程序的服务提供者
     *
     * @return void
     */
    public function boot();

    /**
     * Register a new boot listener.
	 * 注册一个新的引导侦听器
     *
     * @param  callable  $callback
     * @return void
     */
    public function booting($callback);

    /**
     * Register a new "booted" listener.
	 * 注册一个新的“已启动”侦听器
     *
     * @param  callable  $callback
     * @return void
     */
    public function booted($callback);

    /**
     * Get the path to the cached services.php file.
	 * 获取缓存的services.php文件的路径
     *
     * @return string
     */
    public function getCachedServicesPath();

    /**
     * Get the path to the cached packages.php file.
	 * 获取缓存的packages.php文件的路径
     *
     * @return string
     */
    public function getCachedPackagesPath();
}
