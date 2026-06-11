<?php
/**
 * Illuminate，数据库，迁移服务提供商
 */

namespace Illuminate\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class MigrationServiceProvider extends ServiceProvider
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
        $this->registerRepository();

        $this->registerMigrator();

        $this->registerCreator();
    }

    /**
     * Register the migration repository service.
	 * 注册迁移存储库服务
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('migration.repository', function ($app) {
            $table = $app['config']['database.migrations'];

            return new DatabaseMigrationRepository($app['db'], $table);
        });
    }

    /**
     * Register the migrator service.
	 * 注册迁移器服务
     *
     * @return void
     */
    protected function registerMigrator()
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
		// 迁移器负责实际运行和回滚应用程序中的迁移文件。
		// 我们将通过我们的数据库连接解析器,这样迁移器就可以在需要时解决这些连接中的任何一个。
        $this->app->singleton('migrator', function ($app) {
            $repository = $app['migration.repository'];

            return new Migrator($repository, $app['db'], $app['files']);
        });
    }

    /**
     * Register the migration creator.
	 * 注册迁移创建者
     *
     * @return void
     */
    protected function registerCreator()
    {
        $this->app->singleton('migration.creator', function ($app) {
            return new MigrationCreator($app['files']);
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
        return [
            'migrator', 'migration.repository', 'migration.creator',
        ];
    }
}
