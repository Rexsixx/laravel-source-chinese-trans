<?php
/**
 * Illuminate，数据库，迁移，迁移器
 */

namespace Illuminate\Database\Migrations;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\ConnectionResolverInterface as Resolver;

class Migrator
{
    /**
     * The migration repository implementation.
	 * 迁移存储库实现
     *
     * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    /**
     * The filesystem instance.
	 * 文件系统实例
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The connection resolver instance.
	 * 连接解析器实例
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the default connection.
	 * 默认连接的名称
     *
     * @var string
     */
    protected $connection;

    /**
     * The paths to all of the migration files.
	 * 所有迁移文件的路径
     *
     * @var array
     */
    protected $paths = [];

    /**
     * The output interface implementation.
	 * 输出接口实现
     *
     * @var \Illuminate\Console\OutputStyle
     */
    protected $output;

    /**
     * Create a new migrator instance.
	 * 创建一个新的迁移器实例
     *
     * @param  \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(MigrationRepositoryInterface $repository,
                                Resolver $resolver,
                                Filesystem $files)
    {
        $this->files = $files;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }

    /**
     * Run the pending migrations at a given path.
	 * 在给定路径上运行挂起的迁移
     *
     * @param  array|string  $paths
     * @param  array  $options
     * @return array
     */
    public function run($paths = [], array $options = [])
    {
        $this->notes = [];

        // Once we grab all of the migration files for the path, we will compare them
        // against the migrations that have already been run for this package then
        // run each of the outstanding migrations against a database connection.
		// 一旦我们抓取了路径的所有迁移文件,我们将将它们与已经运行在这个包中的迁移进行比较,
		// 然后运行每一个对数据库连接的优秀迁移。
        $files = $this->getMigrationFiles($paths);

        $this->requireFiles($migrations = $this->pendingMigrations(
            $files, $this->repository->getRan()
        ));

        // Once we have all these migrations that are outstanding we are ready to run
        // we will go ahead and run them "up". This will execute each migration as
        // an operation against a database. Then we'll return this list of them.
		// 一旦我们有了所有的迁移,我们已经准备好了,我们就会继续说“up”。
		// 这将执行每个迁移作为对数据库的操作。然后我们将返回它们的列表。
        $this->runPending($migrations, $options);

        return $migrations;
    }

    /**
     * Get the migration files that have not yet run.
	 * 获取尚未运行的迁移文件
     *
     * @param  array  $files
     * @param  array  $ran
     * @return array
     */
    protected function pendingMigrations($files, $ran)
    {
        return Collection::make($files)
                ->reject(function ($file) use ($ran) {
                    return in_array($this->getMigrationName($file), $ran);
                })->values()->all();
    }

    /**
     * Run an array of migrations.
	 * 运行一系列迁移
     *
     * @param  array  $migrations
     * @param  array  $options
     * @return void
     */
    public function runPending(array $migrations, array $options = [])
    {
        // First we will just make sure that there are any migrations to run. If there
        // aren't, we will just make a note of it to the developer so they're aware
        // that all of the migrations have been run against this database system.
		// 首先,我们要确保有任何迁移运行。
		// 如果没有,我们将把它给开发人员,所以他们知道所有的迁移都是针对这个数据库系统的。
        if (count($migrations) === 0) {
            $this->note('<info>Nothing to migrate.</info>');

            return;
        }

        // Next, we will get the next batch number for the migrations so we can insert
        // correct batch number in the database migrations repository when we store
        // each migration's execution. We will also extract a few of the options.
		// 接下来,我们将得到下一批的迁移,以便我们可以在数据库迁移存储库中插入正确的批号,
		// 当我们存储每个迁移的执行时。我们还将提取一些选项。
        $batch = $this->repository->getNextBatchNumber();

        $pretend = $options['pretend'] ?? false;

        $step = $options['step'] ?? false;

        // Once we have the array of migrations, we will spin through them and run the
        // migrations "up" so the changes are made to the databases. We'll then log
        // that the migration was run so we don't repeat it next time we execute.
		// 一旦我们有了迁移的数组,我们就会旋转它们并运行迁移“up”,这样就会对数据库进行更改。
		// 然后我们会记录迁移是运行的,这样我们就不会在下次执行时重复它。
        foreach ($migrations as $file) {
            $this->runUp($file, $batch, $pretend);

            if ($step) {
                $batch++;
            }
        }
    }

    /**
     * Run "up" a migration instance.
	 * 运行“up”迁移实例
     *
     * @param  string  $file
     * @param  int     $batch
     * @param  bool    $pretend
     * @return void
     */
    protected function runUp($file, $batch, $pretend)
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
		// 首先,我们将从这个迁移文件名解决迁移类的“真实”实例。
		// 一旦我们有了实例,我们就可以运行实际的命令,如“up”或“down”,或者我们可以模拟操作。
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            return $this->pretendToRun($migration, 'up');
        }

        $this->note("<comment>Migrating:</comment> {$name}");

        $this->runMigration($migration, 'up');

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
		// 一旦我们运行了一个迁移类,我们将登录它在这个存储库中运行,
		// 这样我们下次在应用程序中进行迁移时就不会尝试运行它。迁移存储库保持迁移顺序。
        $this->repository->log($name, $batch);

        $this->note("<info>Migrated:</info>  {$name}");
    }

    /**
     * Rollback the last migration operation.
	 * 回滚上一次迁移操作
     *
     * @param  array|string $paths
     * @param  array  $options
     * @return array
     */
    public function rollback($paths = [], array $options = [])
    {
        $this->notes = [];

        // We want to pull in the last batch of migrations that ran on the previous
        // migration operation. We'll then reverse those migrations and run each
        // of them "down" to reverse the last migration "operation" which ran.
		// 我们想要在上次迁移操作的最后一批迁移中拉出来。
		// 然后我们将逆转这些迁移,并将它们“down”运行到最后一个迁移“操作”。
        $migrations = $this->getMigrationsForRollback($options);

        if (count($migrations) === 0) {
            $this->note('<info>Nothing to rollback.</info>');

            return [];
        }

        return $this->rollbackMigrations($migrations, $paths, $options);
    }

    /**
     * Get the migrations for a rollback operation.
	 * 获取回滚操作的迁移
     *
     * @param  array  $options
     * @return array
     */
    protected function getMigrationsForRollback(array $options)
    {
        if (($steps = $options['step'] ?? 0) > 0) {
            return $this->repository->getMigrations($steps);
        }

        return $this->repository->getLast();
    }

    /**
     * Rollback the given migrations.
	 * 回滚给定的迁移
     *
     * @param  array  $migrations
     * @param  array|string  $paths
     * @param  array  $options
     * @return array
     */
    protected function rollbackMigrations(array $migrations, $paths, array $options)
    {
        $rolledBack = [];

        $this->requireFiles($files = $this->getMigrationFiles($paths));

        // Next we will run through all of the migrations and call the "down" method
        // which will reverse each migration in order. This getLast method on the
        // repository already returns these migration's names in reverse order.
		// 接下来,我们将运行所有的迁移,并调用“down”方法,它将改变每个迁移的顺序。
		// 该存储库中的getLast方法已经以相反的顺序返回这些迁移的名称。
        foreach ($migrations as $migration) {
            $migration = (object) $migration;

            if (! $file = Arr::get($files, $migration->migration)) {
                $this->note("<fg=red>Migration not found:</> {$migration->migration}");

                continue;
            }

            $rolledBack[] = $file;

            $this->runDown(
                $file, $migration,
                $options['pretend'] ?? false
            );
        }

        return $rolledBack;
    }

    /**
     * Rolls all of the currently applied migrations back.
	 * 回滚所有当前应用的迁移
     *
     * @param  array|string $paths
     * @param  bool  $pretend
     * @return array
     */
    public function reset($paths = [], $pretend = false)
    {
        $this->notes = [];

        // Next, we will reverse the migration list so we can run them back in the
        // correct order for resetting this database. This will allow us to get
        // the database back into its "empty" state ready for the migrations.
		// 接下来,我们将改变迁移列表,这样我们就可以把它们返回回重新设置这个数据库的正确顺序。
		// 这将允许我们将数据库带回“空”状态,为迁移准备就绪。
        $migrations = array_reverse($this->repository->getRan());

        if (count($migrations) === 0) {
            $this->note('<info>Nothing to rollback.</info>');

            return [];
        }

        return $this->resetMigrations($migrations, $paths, $pretend);
    }

    /**
     * Reset the given migrations.
	 * 重置给定的迁移
     *
     * @param  array  $migrations
     * @param  array  $paths
     * @param  bool  $pretend
     * @return array
     */
    protected function resetMigrations(array $migrations, array $paths, $pretend = false)
    {
        // Since the getRan method that retrieves the migration name just gives us the
        // migration name, we will format the names into objects with the name as a
        // property on the objects so that we can pass it to the rollback method.
		// 通过检索迁移名称的getRan方法给我们迁移名,我们将将名称格式化为对象,
		// 将名称作为属性在对象上,这样我们就可以将其传递给回滚方法。
        $migrations = collect($migrations)->map(function ($m) {
            return (object) ['migration' => $m];
        })->all();

        return $this->rollbackMigrations(
            $migrations, $paths, compact('pretend')
        );
    }

    /**
     * Run "down" a migration instance.
	 * 运行“down”迁移实例
     *
     * @param  string  $file
     * @param  object  $migration
     * @param  bool    $pretend
     * @return void
     */
    protected function runDown($file, $migration, $pretend)
    {
        // First we will get the file name of the migration so we can resolve out an
        // instance of the migration. Once we get an instance we can either run a
        // pretend execution of the migration or we can run the real migration.
		// 首先,我们将得到迁移的文件名,这样我们就可以解决迁移的一个实例。
		// 一旦我们得到实例,我们就可以运行迁移的假装执行,或者我们可以运行真正的迁移。
        $instance = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        $this->note("<comment>Rolling back:</comment> {$name}");

        if ($pretend) {
            return $this->pretendToRun($instance, 'down');
        }

        $this->runMigration($instance, 'down');

        // Once we have successfully run the migration "down" we will remove it from
        // the migration repository so it will be considered to have not been run
        // by the application then will be able to fire by any later operation.
		// 一旦我们成功地运行了“down”,我们将从迁移存储库中删除它,
		// 这样它就会被认为没有被应用程序运行,然后将能够被任何稍后的操作触发。
        $this->repository->delete($migration);

        $this->note("<info>Rolled back:</info>  {$name}");
    }

    /**
     * Run a migration inside a transaction if the database supports it.
	 * 如果数据库支持，则在事务中运行迁移。
     *
     * @param  object  $migration
     * @param  string  $method
     * @return void
     */
    protected function runMigration($migration, $method)
    {
        $connection = $this->resolveConnection(
            $migration->getConnection()
        );

        $callback = function () use ($migration, $method) {
            if (method_exists($migration, $method)) {
                $migration->{$method}();
            }
        };

        $this->getSchemaGrammar($connection)->supportsSchemaTransactions()
            && $migration->withinTransaction
                    ? $connection->transaction($callback)
                    : $callback();
    }

    /**
     * Pretend to run the migrations.
	 * 假装运行迁移
     *
     * @param  object  $migration
     * @param  string  $method
     * @return void
     */
    protected function pretendToRun($migration, $method)
    {
        foreach ($this->getQueries($migration, $method) as $query) {
            $name = get_class($migration);

            $this->note("<info>{$name}:</info> {$query['query']}");
        }
    }

    /**
     * Get all of the queries that would be run for a migration.
	 * 获取将为迁移运行的所有查询
     *
     * @param  object  $migration
     * @param  string  $method
     * @return array
     */
    protected function getQueries($migration, $method)
    {
        // Now that we have the connections we can resolve it and pretend to run the
        // queries against the database returning the array of raw SQL statements
        // that would get fired against the database system for this migration.
		// 现在,我们有了连接,我们可以解决它,并假装运行对数据库的查询,
		// 返回返回的原始SQL语句数组,这些SQL语句将会被针对这个迁移的数据库系统而被触发。
        $db = $this->resolveConnection(
            $migration->getConnection()
        );

        return $db->pretend(function () use ($migration, $method) {
            if (method_exists($migration, $method)) {
                $migration->{$method}();
            }
        });
    }

    /**
     * Resolve a migration instance from a file.
	 * 从文件解析迁移实例
     *
     * @param  string  $file
     * @return object
     */
    public function resolve($file)
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $file), 4)));

        return new $class;
    }

    /**
     * Get all of the migration files in a given path.
	 * 获取给定路径中的所有迁移文件
     *
     * @param  string|array  $paths
     * @return array
     */
    public function getMigrationFiles($paths)
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return Str::endsWith($path, '.php') ? [$path] : $this->files->glob($path.'/*_*.php');
        })->filter()->sortBy(function ($file) {
            return $this->getMigrationName($file);
        })->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        })->all();
    }

    /**
     * Require in all the migration files in a given path.
	 * 在给定路径中的所有迁移文件中要求
     *
     * @param  array   $files
     * @return void
     */
    public function requireFiles(array $files)
    {
        foreach ($files as $file) {
            $this->files->requireOnce($file);
        }
    }

    /**
     * Get the name of the migration.
	 * 获取迁移的名称
     *
     * @param  string  $path
     * @return string
     */
    public function getMigrationName($path)
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * Register a custom migration path.
	 * 注册自定义迁移路径
     *
     * @param  string  $path
     * @return void
     */
    public function path($path)
    {
        $this->paths = array_unique(array_merge($this->paths, [$path]));
    }

    /**
     * Get all of the custom migration paths.
	 * 获取所有自定义迁移路径
     *
     * @return array
     */
    public function paths()
    {
        return $this->paths;
    }

    /**
     * Get the default connection name.
	 * 获取默认连接名称
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the default connection name.
	 * 设置默认连接名称
     *
     * @param  string  $name
     * @return void
     */
    public function setConnection($name)
    {
        if (! is_null($name)) {
            $this->resolver->setDefaultConnection($name);
        }

        $this->repository->setSource($name);

        $this->connection = $name;
    }

    /**
     * Resolve the database connection instance.
	 * 解析数据库连接实例
     *
     * @param  string  $connection
     * @return \Illuminate\Database\Connection
     */
    public function resolveConnection($connection)
    {
        return $this->resolver->connection($connection ?: $this->connection);
    }

    /**
     * Get the schema grammar out of a migration connection.
	 * 从迁移连接中获取模式语法
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected function getSchemaGrammar($connection)
    {
        if (is_null($grammar = $connection->getSchemaGrammar())) {
            $connection->useDefaultSchemaGrammar();

            $grammar = $connection->getSchemaGrammar();
        }

        return $grammar;
    }

    /**
     * Get the migration repository instance.
	 * 获取迁移存储库实例
     *
     * @return \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Determine if the migration repository exists.
	 * 确定迁移存储库是否存在
     *
     * @return bool
     */
    public function repositoryExists()
    {
        return $this->repository->repositoryExists();
    }

    /**
     * Get the file system instance.
	 * 获取文件系统实例
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }

    /**
     * Set the output implementation that should be used by the console.
	 * 设置控制台应该使用的输出实现
     *
     * @param  \Illuminate\Console\OutputStyle  $output
     * @return $this
     */
    public function setOutput(OutputStyle $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Write a note to the conosle's output.
	 * 在控制台的输出中写入一个注释
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        if ($this->output) {
            $this->output->writeln($message);
        }
    }
}
