<?php
/**
 * Illuminate，数据库，迁移，迁移的创造者
 */

namespace Illuminate\Database\Migrations;

use Closure;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class MigrationCreator
{
    /**
     * The filesystem instance.
	 * 文件系统实例
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The registered post create hooks.
	 * 注册的post创建钩子
     *
     * @var array
     */
    protected $postCreate = [];

    /**
     * Create a new migration creator instance.
	 * 创建一个新的迁移创建器实例
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new migration at the given path.
	 * 在给定的路径上创建一个新的迁移
     *
     * @param  string  $name
     * @param  string  $path
     * @param  string|null  $table
     * @param  bool    $create
     * @return string
     *
     * @throws \Exception
     */
    public function create($name, $path, $table = null, $create = false)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the post create event.
		// 首先，我们将获取用于迁移的模板文件，该文件可作为迁移过程中的某种模板使用。
		// 一旦我们获取到这些信息，就会填充各个占位符，保存文件，并运行创建后的事件。
        $stub = $this->getStub($table, $create);

        $this->files->put(
            $path = $this->getPath($name, $path),
            $this->populateStub($name, $stub, $table)
        );

        // Next, we will fire any hooks that are supposed to fire after a migration is
        // created. Once that is done we'll be ready to return the full path to the
        // migration file so it can be used however it's needed by the developer.
		// 接下来，我们将触发所有原本应在迁移创建完成后才触发的钩子。
		// 完成这些步骤后，我们就能将迁移文件的完整路径提供给开发人员，以便他们根据需要进行使用。
        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
	 * 确保具有给定名称的迁移不存在
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($name)
    {
        if (class_exists($className = $this->getClassName($name))) {
            throw new InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Get the migration stub file.
	 * 获取迁移存根文件
     *
     * @param  string|null  $table
     * @param  bool    $create
     * @return string
     */
    protected function getStub($table, $create)
    {
        if (is_null($table)) {
            return $this->files->get($this->stubPath().'/blank.stub');
        }

        // We also have stubs for creating new tables and modifying existing tables
        // to save the developer some typing when they are creating a new tables
        // or modifying existing tables. We'll grab the appropriate stub here.
		// 我们还提供了创建新表和修改现有表的快捷方式，以便在开发人员创建新表或修改现有表时减少他们的输入工作量。
		// 我们会在这里抓住合适的存根。
        $stub = $create ? 'create.stub' : 'update.stub';

        return $this->files->get($this->stubPath()."/{$stub}");
    }

    /**
     * Populate the place-holders in the migration stub.
	 * 在迁移存根中填充占位符
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace('DummyClass', $this->getClassName($name), $stub);

        // Here we will replace the table place-holders with the table specified by
        // the developer, which is useful for quickly creating a tables creation
        // or update migration from the console instead of typing it manually.
		// 在这里，我们将用开发人员指定的表格来替换表格占位符，这便于通过控制台快速创建表格或进行更新迁移操作，而无需手动输入。
        if (! is_null($table)) {
            $stub = str_replace('DummyTable', $table, $stub);
        }

        return $stub;
    }

    /**
     * Get the class name of a migration name.
	 * 获取迁移名的类名
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the migration.
	 * 获取迁移的完整路径
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Fire the registered post create hooks.
	 * 触发注册的帖子创建钩子
     *
     * @param  string|null  $table
     * @return void
     */
    protected function firePostCreateHooks($table)
    {
        foreach ($this->postCreate as $callback) {
            call_user_func($callback, $table);
        }
    }

    /**
     * Register a post migration create hook.
	 * 注册一个迁移后创建钩子
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function afterCreate(Closure $callback)
    {
        $this->postCreate[] = $callback;
    }

    /**
     * Get the date prefix for the migration.
	 * 获取迁移的日期前缀
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
	 * 找到存根的路径
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the filesystem instance.
	 * 获取文件系统实例
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }
}
