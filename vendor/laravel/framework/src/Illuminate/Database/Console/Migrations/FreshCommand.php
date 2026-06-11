<?php
/**
 * Illuminate，数据库，控制台，迁移，刷新命令
 */

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;

class FreshCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The console command name.
	 * 控制台命令名称
     *
     * @var string
     */
    protected $name = 'migrate:fresh';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations';

    /**
     * Execute the console command.
	 * 执行控制台命令
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $database = $this->input->getOption('database');

        if ($this->option('drop-views')) {
            $this->dropAllViews($database);

            $this->info('Dropped all views successfully.');
        }

        $this->dropAllTables($database);

        $this->info('Dropped all tables successfully.');

        $this->call('migrate', array_filter([
            '--database' => $database,
            '--path' => $this->input->getOption('path'),
            '--realpath' => $this->input->getOption('realpath'),
            '--force' => true,
            '--step' => $this->option('step'),
        ]));

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }
    }

    /**
     * Drop all of the database tables.
	 * 删除所有数据库表
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllTables($database)
    {
        $this->laravel['db']->connection($database)
                    ->getSchemaBuilder()
                    ->dropAllTables();
    }

    /**
     * Drop all of the database views.
	 * 删除所有数据库视图
     *
     * @param  string  $database
     * @return void
     */
    protected function dropAllViews($database)
    {
        $this->laravel['db']->connection($database)
                    ->getSchemaBuilder()
                    ->dropAllViews();
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

            ['drop-views', null, InputOption::VALUE_NONE, 'Drop all tables and views'],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],

            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the migrations files to be executed'],

            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],

            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],

            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],

            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually'],
        ];
    }
}
