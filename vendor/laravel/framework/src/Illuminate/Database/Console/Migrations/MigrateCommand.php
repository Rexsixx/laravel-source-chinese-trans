<?php
/**
 * Illuminate，数据库，控制台，迁移，迁移命令
 */

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Migrations\Migrator;

class MigrateCommand extends BaseCommand
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
	 * console命令的名称和签名
     *
     * @var string
     */
    protected $signature = 'migrate {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--seed : Indicates if the seed task should be re-run}
                {--step : Force the migrations to be run so they can be rolled back individually}';

    /**
     * The console command description.
	 * console命令说明
     *
     * @var string
     */
    protected $description = 'Run the database migrations';

    /**
     * The migrator instance.
	 * 迁移器实例
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    /**
     * Create a new migration command instance.
	 * 创建一个新的迁移命令实例
     *
     * @param  \Illuminate\Database\Migrations\Migrator  $migrator
     * @return void
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
	 * 执行console命令
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
		// 接下来，我们将检查是否定义了路径选项。
		// 如果存在的话，我们将使用相对于此安装文件夹根目录的路径，以便能够对应用程序内的任何路径执行迁移操作。
        $this->migrator->setOutput($this->output)
                ->run($this->getMigrationPaths(), [
                    'pretend' => $this->option('pretend'),
                    'step' => $this->option('step'),
                ]);

        // Finally, if the "seed" option has been given, we will re-run the database
        // seed task to re-populate the database, which is convenient when adding
        // a migration and a seed at the same time, as it is only this command.
		// 最后，如果已设置了“种子”选项，我们将重新执行数据库种子任务以重新填充数据库。
		// 这种方法非常方便，当同时进行迁移和设置种子操作时尤其适用，因为只需使用此命令即可。
        if ($this->option('seed') && ! $this->option('pretend')) {
            $this->call('db:seed', ['--force' => true]);
        }
    }

    /**
     * Prepare the migration database for running.
	 * 准备运行迁移数据库
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection($this->option('database'));

        if (! $this->migrator->repositoryExists()) {
            $this->call('migrate:install', array_filter([
                '--database' => $this->option('database'),
            ]));
        }
    }
}
