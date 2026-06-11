<?php
/**
 * Illuminate，支持，服务提供商
 */

namespace Illuminate\Support;

use Illuminate\Console\Application as Artisan;

abstract class ServiceProvider
{
    /**
     * The application instance.
	 * 应用实例
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Indicates if loading of the provider is deferred.
	 * 指示是否延迟加载提供程序
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The paths that should be published.
	 * 应该发布的路径
     *
     * @var array
     */
    public static $publishes = [];

    /**
     * The paths that should be published by group.
	 * 应按组发布的路径
     *
     * @var array
     */
    public static $publishGroups = [];

    /**
     * Create a new service provider instance.
	 * 创建一个新的服务提供者实例
     *
     * @param  \Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Merge the given configuration with the existing configuration.
	 * 将给定的配置与现有配置合并
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, array_merge(require $path, $config));
    }

    /**
     * Load the given routes file if routes are not already cached.
	 * 如果路由尚未缓存，则加载给定的路由文件。
     *
     * @param  string  $path
     * @return void
     */
    protected function loadRoutesFrom($path)
    {
        if (! $this->app->routesAreCached()) {
            require $path;
        }
    }

    /**
     * Register a view file namespace.
	 * 注册一个视图文件命名空间
     *
     * @param  string|array  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadViewsFrom($path, $namespace)
    {
        if (is_array($this->app->config['view']['paths'])) {
            foreach ($this->app->config['view']['paths'] as $viewPath) {
                if (is_dir($appPath = $viewPath.'/vendor/'.$namespace)) {
                    $this->app['view']->addNamespace($namespace, $appPath);
                }
            }
        }

        $this->app['view']->addNamespace($namespace, $path);
    }

    /**
     * Register a translation file namespace.
	 * 注册一个翻译文件命名空间
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    protected function loadTranslationsFrom($path, $namespace)
    {
        $this->app['translator']->addNamespace($namespace, $path);
    }

    /**
     * Register a JSON translation file path.
	 * 注册一个JSON翻译文件路径
     *
     * @param  string  $path
     * @return void
     */
    protected function loadJsonTranslationsFrom($path)
    {
        $this->app['translator']->addJsonPath($path);
    }

    /**
     * Register a database migration path.
	 * 注册数据库迁移路径
     *
     * @param  array|string  $paths
     * @return void
     */
    protected function loadMigrationsFrom($paths)
    {
        $this->app->afterResolving('migrator', function ($migrator) use ($paths) {
            foreach ((array) $paths as $path) {
                $migrator->path($path);
            }
        });
    }

    /**
     * Register paths to be published by the publish command.
	 * 使用publish命令注册要发布的路径
     *
     * @param  array  $paths
     * @param  string  $group
     * @return void
     */
    protected function publishes(array $paths, $group = null)
    {
        $this->ensurePublishArrayInitialized($class = static::class);

        static::$publishes[$class] = array_merge(static::$publishes[$class], $paths);

        if ($group) {
            $this->addPublishGroup($group, $paths);
        }
    }

    /**
     * Ensure the publish array for the service provider is initialized.
	 * 确保初始化了服务提供者的发布数组
     *
     * @param  string  $class
     * @return void
     */
    protected function ensurePublishArrayInitialized($class)
    {
        if (! array_key_exists($class, static::$publishes)) {
            static::$publishes[$class] = [];
        }
    }

    /**
     * Add a publish group / tag to the service provider.
	 * 向服务提供者添加发布组/标记
     *
     * @param  string  $group
     * @param  array  $paths
     * @return void
     */
    protected function addPublishGroup($group, $paths)
    {
        if (! array_key_exists($group, static::$publishGroups)) {
            static::$publishGroups[$group] = [];
        }

        static::$publishGroups[$group] = array_merge(
            static::$publishGroups[$group], $paths
        );
    }

    /**
     * Get the paths to publish.
	 * 获取发布路径
     *
     * @param  string  $provider
     * @param  string  $group
     * @return array
     */
    public static function pathsToPublish($provider = null, $group = null)
    {
        if (! is_null($paths = static::pathsForProviderOrGroup($provider, $group))) {
            return $paths;
        }

        return collect(static::$publishes)->reduce(function ($paths, $p) {
            return array_merge($paths, $p);
        }, []);
    }

    /**
     * Get the paths for the provider or group (or both).
	 * 获取提供程序或组（或两者）的路径
     *
     * @param  string|null  $provider
     * @param  string|null  $group
     * @return array
     */
    protected static function pathsForProviderOrGroup($provider, $group)
    {
        if ($provider && $group) {
            return static::pathsForProviderAndGroup($provider, $group);
        } elseif ($group && array_key_exists($group, static::$publishGroups)) {
            return static::$publishGroups[$group];
        } elseif ($provider && array_key_exists($provider, static::$publishes)) {
            return static::$publishes[$provider];
        } elseif ($group || $provider) {
            return [];
        }
    }

    /**
     * Get the paths for the provider and group.
	 * 获取提供程序和组的路径
     *
     * @param  string  $provider
     * @param  string  $group
     * @return array
     */
    protected static function pathsForProviderAndGroup($provider, $group)
    {
        if (! empty(static::$publishes[$provider]) && ! empty(static::$publishGroups[$group])) {
            return array_intersect_key(static::$publishes[$provider], static::$publishGroups[$group]);
        }

        return [];
    }

    /**
     * Get the service providers available for publishing.
	 * 获取可用于发布的服务提供者
     *
     * @return array
     */
    public static function publishableProviders()
    {
        return array_keys(static::$publishes);
    }

    /**
     * Get the groups available for publishing.
	 * 获取可用于发布的组
     *
     * @return array
     */
    public static function publishableGroups()
    {
        return array_keys(static::$publishGroups);
    }

    /**
     * Register the package's custom Artisan commands.
	 * 注册包的自定义Artisan命令
     *
     * @param  array|mixed  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        Artisan::starting(function ($artisan) use ($commands) {
            $artisan->resolveCommands($commands);
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
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
	 * 获取触发此服务提供者注册的事件
     *
     * @return array
     */
    public function when()
    {
        return [];
    }

    /**
     * Determine if the provider is deferred.
	 * 确定是否延迟提供程序
     *
     * @return bool
     */
    public function isDeferred()
    {
        return $this->defer;
    }
}
