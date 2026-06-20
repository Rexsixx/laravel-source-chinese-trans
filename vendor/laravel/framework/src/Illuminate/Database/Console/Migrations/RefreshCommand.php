<?php
/**
 * Illuminate，数据库，控制台，迁移，刷新命令
 */

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class RefreshCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $name = 'migrate:refresh';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Reset and re-run all migrations';

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

        // Next we'll gather some of the options so that we can have the right options
        // to pass to the commands. This includes options such as which database to
        // use and the path to use for the migration. Then we'll run the command.
		// 接下来,我们将收集一些选项,这样我们就可以有正确的选项传递到命令。
		// 这包括选择数据库和使用迁移路径等选项。然后我们运行命令。
        $database = $this->input->getOption('database');

        $path = $this->input->getOption('path');

        // If the "step" option is specified it means we only want to rollback a small
        // number of migrations before migrating again. For example, the user might
        // only rollback and remigrate the latest four migrations instead of all.
		// 如果“step”选项被指定,这意味着我们只希望在迁移之前返回少量的迁移。
		// 例如,用户可能只回滚回并重新迁移最近的四个迁移,而不是全部。
        $step = $this->input->getOption('step') ?: 0;

        if ($step > 0) {
            $this->runRollback($database, $path, $step);
        } else {
            $this->runReset($database, $path);
        }

        // The refresh command is essentially just a brief aggregate of a few other of
        // the migration commands and just provides a convenient wrapper to execute
        // them in succession. We'll also see if we need to re-seed the database.
		// 刷新命令本质上只是迁移命令的一小部分,只是提供了一个方便的包装器来继承它们。
		// 我们也会看到我们是否需要重新种子数据库。
        $this->call('migrate', array_filter([
            '--database' => $database,
            '--path' => $path,
            '--realpath' => $this->input->getOption('realpath'),
            '--force' => true,
        ]));

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }
    }

    /**
     * Run the rollback command.
	 * 执行回滚命令
     *
     * @param  string  $database
     * @param  string  $path
     * @param  int  $step
     * @return void
     */
    protected function runRollback($database, $path, $step)
    {
        $this->call('migrate:rollback', array_filter([
            '--database' => $database,
            '--path' => $path,
            '--realpath' => $this->input->getOption('realpath'),
            '--step' => $step,
            '--force' => true,
        ]));
    }

    /**
     * Run the reset command.
	 * 执行reset命令
     *
     * @param  string  $database
     * @param  string  $path
     * @return void
     */
    protected function runReset($database, $path)
    {
        $this->call('migrate:reset', array_filter([
            '--database' => $database,
            '--path' => $path,
            '--realpath' => $this->input->getOption('realpath'),
            '--force' => true,
        ]));
    }

    /**
     * Determine if the developer has requested database seeding.
	 * 确定开发人员是否请求了数据库播种
     *
     * @return bool
     */
    protected function needsSeeding()
    {
        return $this->option('seed') || $this->option('seeder');
    }

    /**
     * Run the database seeder command.
	 * 执行database seeder命令
     *
     * @param  string  $database
     * @return void
     */
    protected function runSeeder($database)
    {
        $this->call('db:seed', array_filter([
            '--database' => $database,
            '--class' => $this->option('seeder') ?: 'DatabaseSeeder',
            '--force' => true,
        ]));
    }

    /**
     * Get the console command options.
	 * 获取控制台命令选项
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations files to be executed'],

            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],

            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],

            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],

            ['step', null, InputOption::VALUE_OPTIONAL, 'The number of migrations to be reverted & re-run'],
        ];
    }
}
