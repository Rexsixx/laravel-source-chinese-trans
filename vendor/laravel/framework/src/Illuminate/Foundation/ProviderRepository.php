<?php
/**
 * Illuminate，基础，供应商资源库
 */

namespace Illuminate\Foundation;

use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class ProviderRepository
{
    /**
     * The application implementation.
	 * 应用实施
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The filesystem instance.
	 * 文件系统实例
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The path to the manifest file.
	 * 清单文件的路径
     *
     * @var string
     */
    protected $manifestPath;

    /**
     * Create a new service repository instance.
	 * 创建一个新的服务存储库实例
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $manifestPath
     * @return void
     */
    public function __construct(ApplicationContract $app, Filesystem $files, $manifestPath)
    {
        $this->app = $app;
        $this->files = $files;
        $this->manifestPath = $manifestPath;
    }

    /**
     * Register the application service providers.
	 * 注册应用程序服务提供者
     *
     * @param  array  $providers
     * @return void
     */
    public function load(array $providers)
    {
        $manifest = $this->loadManifest();

        // First we will load the service manifest, which contains information on all
        // service providers registered with the application and which services it
        // provides. This is used to know which services are "deferred" loaders.
		// 首先，我们将加载服务清单，该清单包含了所有已注册于该应用程序中的服务提供者的信息，
		// 以及该应用程序所提供的各项服务内容。
        if ($this->shouldRecompile($manifest, $providers)) {
            $manifest = $this->compileManifest($providers);
        }

        // Next, we will register events to load the providers for each of the events
        // that it has requested. This allows the service provider to defer itself
        // while still getting automatically loaded when a certain event occurs.
		// 接下来，我们将注册相关事件，以加载它所请求的每个事件的提供者。
        foreach ($manifest['when'] as $provider => $events) {
            $this->registerLoadEvents($provider, $events);
        }

        // We will go ahead and register all of the eagerly loaded providers with the
        // application so their services can be registered with the application as
        // a provided service. Then we will set the deferred service list on it.
		// 我们将继续注册所有已加载的提供者，以便将其服务作为提供的服务注册到应用程序中。
        foreach ($manifest['eager'] as $provider) {
            $this->app->register($provider);
        }

        $this->app->addDeferredServices($manifest['deferred']);
    }

    /**
     * Load the service provider manifest JSON file.
	 * 加载服务提供者清单JSON文件
     *
     * @return array|null
     */
    public function loadManifest()
    {
        // The service manifest is a file containing a JSON representation of every
        // service provided by the application and whether its provider is using
        // deferred loading or should be eagerly loaded on each request to us.
		// 服务清单是一个文件，其中包含了应用程序所提供每项服务的 JSON 格式表示，
		// 以及其提供者是采用延迟加载方式还是在每次向我们发出请求时都需进行主动加载。
        if ($this->files->exists($this->manifestPath)) {
            $manifest = $this->files->getRequire($this->manifestPath);

            if ($manifest) {
                return array_merge(['when' => []], $manifest);
            }
        }
    }

    /**
     * Determine if the manifest should be compiled.
	 * 确定是否应该编译清单
     *
     * @param  array  $manifest
     * @param  array  $providers
     * @return bool
     */
    public function shouldRecompile($manifest, $providers)
    {
        return is_null($manifest) || $manifest['providers'] != $providers;
    }

    /**
     * Register the load events for the given provider.
	 * 注册给定提供程序的加载事件
     *
     * @param  string  $provider
     * @param  array  $events
     * @return void
     */
    protected function registerLoadEvents($provider, array $events)
    {
        if (count($events) < 1) {
            return;
        }

        $this->app->make('events')->listen($events, function () use ($provider) {
            $this->app->register($provider);
        });
    }

    /**
     * Compile the application service manifest file.
	 * 编译应用程序服务清单文件
     *
     * @param  array  $providers
     * @return array
     */
    protected function compileManifest($providers)
    {
        // The service manifest should contain a list of all of the providers for
        // the application so we can compare it on each request to the service
        // and determine if the manifest should be recompiled or is current.
		// 服务清单应包含该应用程序的所有提供者的列表，
		// 这样我们就能在每次向服务发出请求时对其进行比较，并确定该清单是否需要重新编译或者是否是最新的。
        $manifest = $this->freshManifest($providers);

        foreach ($providers as $provider) {
            $instance = $this->createProvider($provider);

            // When recompiling the service manifest, we will spin through each of the
            // providers and check if it's a deferred provider or not. If so we'll
            // add it's provided services to the manifest and note the provider.
			// 在重新编译服务清单时，我们会逐一检查每个提供者，以确定其是否为延迟型提供者。
            if ($instance->isDeferred()) {
                foreach ($instance->provides() as $service) {
                    $manifest['deferred'][$service] = $provider;
                }

                $manifest['when'][$provider] = $instance->when();
            }

            // If the service providers are not deferred, we will simply add it to an
            // array of eagerly loaded providers that will get registered on every
            // request to this application instead of "lazy" loading every time.
            else {
                $manifest['eager'][] = $provider;
            }
        }

        return $this->writeManifest($manifest);
    }

    /**
     * Create a fresh service manifest data structure.
	 * 创建一个新的服务清单数据结构
     *
     * @param  array  $providers
     * @return array
     */
    protected function freshManifest(array $providers)
    {
        return ['providers' => $providers, 'eager' => [], 'deferred' => []];
    }

    /**
     * Write the service manifest file to disk.
	 * 将服务清单文件写入磁盘
     *
     * @param  array  $manifest
     * @return array
     *
     * @throws \Exception
     */
    public function writeManifest($manifest)
    {
        if (! is_writable(dirname($this->manifestPath))) {
            throw new Exception('The bootstrap/cache directory must be present and writable.');
        }

        $this->files->put(
            $this->manifestPath, '<?php return '.var_export($manifest, true).';'
        );

        return array_merge(['when' => []], $manifest);
    }

    /**
     * Create a new provider instance.
	 * 创建一个新的提供者实例
     *
     * @param  string  $provider
     * @return \Illuminate\Support\ServiceProvider
     */
    public function createProvider($provider)
    {
        return new $provider($this->app);
    }
}
