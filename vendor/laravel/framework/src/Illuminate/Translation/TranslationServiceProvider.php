<?php
/**
 * Illuminate，翻译，翻译服务提供商
 */

namespace Illuminate\Translation;

use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
	 * 指示是否延迟加载提供程序
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
			// 在注册转换器组件时，我们需要设置本地及回退区域默认值。
            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    /**
     * Register the translation line loader.
	 * 注册翻译行装载机
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new FileLoader($app['files'], $app['path.lang']);
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
        return ['translator', 'translation.loader'];
    }
}
