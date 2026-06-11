<?php
/**
 * Illuminate，数据库，数据库服务提供商
 */

namespace Illuminate\Database;

use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Queue\EntityResolver;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Eloquent\QueueEntityResolver;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
	 * 引导应用程序事件
     *
     * @return void
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);

        Model::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        Model::clearBootedModels();

        $this->registerConnectionServices();

        $this->registerEloquentFactory();

        $this->registerQueueableEntityResolver();
    }

    /**
     * Register the primary database bindings.
	 * 注册主数据库绑定
     *
     * @return void
     */
    protected function registerConnectionServices()
    {
        // The connection factory is used to create the actual connection instances on
        // the database. We will inject the factory into the manager so that it may
        // make the connections while they are actually needed and not of before.
		// 连接工厂用于在数据库上创建实际的连接实例。
		// 我们将把工厂注入经理,以便在实际需要的时候建立连接,而不是以前。
        $this->app->singleton('db.factory', function ($app) {
            return new ConnectionFactory($app);
        });

        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
		// 数据库管理器用于解决各种连接,因为可能会管理多个连接。
		// 它还实现了连接解析器接口,它可能被需要连接的其他组件使用。
        $this->app->singleton('db', function ($app) {
            return new DatabaseManager($app, $app['db.factory']);
        });

        $this->app->bind('db.connection', function ($app) {
            return $app['db']->connection();
        });
    }

    /**
     * Register the Eloquent factory instance in the container.
	 * 在容器中注册Eloquent工厂实例
     *
     * @return void
     */
    protected function registerEloquentFactory()
    {
        $this->app->singleton(FakerGenerator::class, function ($app) {
            return FakerFactory::create($app['config']->get('app.faker_locale', 'en_US'));
        });

        $this->app->singleton(EloquentFactory::class, function ($app) {
            return EloquentFactory::construct(
                $app->make(FakerGenerator::class), $this->app->databasePath('factories')
            );
        });
    }

    /**
     * Register the queueable entity resolver implementation.
	 * 注册可排队实体解析器实现
     *
     * @return void
     */
    protected function registerQueueableEntityResolver()
    {
        $this->app->singleton(EntityResolver::class, function () {
            return new QueueEntityResolver;
        });
    }
}
