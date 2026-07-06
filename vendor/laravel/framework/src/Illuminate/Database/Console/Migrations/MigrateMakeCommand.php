<?php
/**
 * Illuminate，数据库，控制台，迁移，迁移 Make命令
 */

namespace Illuminate\Database\Console\Migrations;

use Illuminate\Support\Str;
use Illuminate\Support\Composer;
use Illuminate\Database\Migrations\MigrationCreator;

class MigrateMakeCommand extends BaseCommand
{
    /**
     * The console command signature.
	 * 控制台命令签名
     *
     * @var string
     */
    protected $signature = 'make:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * The console command description.
	 * console命令说明
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
	 * 迁移创建器实例
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
	 * Composer实例
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
	 * 创建一个新的迁移安装命令实例
     *
     * @param  \Illuminate\Database\Migrations\MigrationCreator  $creator
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
	 * 执行console命令
     *
     * @return void
     */
    public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
		// 开发人员可以在此架构操作中指定要修改的表。
		// 开发人员还可以指定是否需要重新创建此表，以便我们能够创建相应的迁移操作。
        $name = Str::snake(trim($this->input->getArgument('name')));

        $table = $this->input->getOption('table');

        $create = $this->input->getOption('create') ?: false;

        // If no table was given as an option but a create option is given then we
        // will use the "create" option as the table name. This allows the devs
        // to pass a table name into this option as a short-cut for creating.
		// 如果未给出表名选项，但提供了“创建”选项，那么我们将使用“创建”选项作为表名。
		// 这使得开发人员能够将表名作为参数传递给此选项，从而简化了创建操作。
        if (! $table && is_string($create)) {
            $table = $create;

            $create = true;
        }

        // Next, we will attempt to guess the table name if this the migration has
        // "create" in the name. This will allow us to provide a convenient way
        // of creating migrations that create new tables for the application.
		// 接下来，如果迁移操作的名称中包含“创建”一词，我们将尝试猜测其对应的表名。
		// 这将使我们能够提供一种便捷的方式来创建迁移操作，从而为应用程序创建新的表。
        if (! $table) {
            [$table, $create] = TableGuesser::guess($name);
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
		// 现在我们准备将迁移写入磁盘。
		// 一旦我们完成了迁移工作，我们就会对整个框架进行“dump-autoload”操作，
		// 以确保迁移信息能被类加载器正确识别并注册。
        $this->writeMigration($name, $table, $create);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
	 * 将迁移文件写入磁盘
     *
     * @param  string  $name
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function writeMigration($name, $table, $create)
    {
        $file = $this->creator->create(
            $name, $this->getMigrationPath(), $table, $create
        );

        if (! $this->option('fullpath')) {
            $file = pathinfo($file, PATHINFO_FILENAME);
        }

        $this->line("<info>Created Migration:</info> {$file}");
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
	 * 获取迁移路径（由‘——path’选项指定或默认位置）
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                            ? $this->laravel->basePath().'/'.$targetPath
                            : $targetPath;
        }

        return parent::getMigrationPath();
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
	 * 确定给定的路径是否是预先解析的“真实”路径
     *
     * @return bool
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }
}
