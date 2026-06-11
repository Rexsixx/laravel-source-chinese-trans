<?php
/**
 * Illuminate，基础，控制台，应用名称命令
 */

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Finder\Finder;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class AppNameCommand extends Command
{
    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $name = 'app:name';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Set the application namespace';

    /**
     * The Composer class instance.
	 * Composer类实例
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * The filesystem instance.
	 * 文件系统实例
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Current root application namespace.
	 * 当前根应用程序名称空间
     *
     * @var string
     */
    protected $currentRoot;

    /**
     * Create a new key generator command.
	 * 创建一个新的密钥生成器命令
     *
     * @param  \Illuminate\Support\Composer  $composer
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Composer $composer, Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
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
        $this->currentRoot = trim($this->laravel->getNamespace(), '\\');

        $this->setAppDirectoryNamespace();
        $this->setBootstrapNamespaces();
        $this->setConfigNamespaces();
        $this->setComposerNamespace();
        $this->setDatabaseFactoryNamespaces();

        $this->info('Application namespace set!');

        $this->composer->dumpAutoloads();

        $this->call('optimize:clear');
    }

    /**
     * Set the namespace on the files in the app directory.
	 * 设置app目录下文件的命名空间
     *
     * @return void
     */
    protected function setAppDirectoryNamespace()
    {
        $files = Finder::create()
                            ->in($this->laravel['path'])
                            ->contains($this->currentRoot)
                            ->name('*.php');

        foreach ($files as $file) {
            $this->replaceNamespace($file->getRealPath());
        }
    }

    /**
     * Replace the App namespace at the given path.
	 * 在给定的路径上替换App命名空间
     *
     * @param  string  $path
     * @return void
     */
    protected function replaceNamespace($path)
    {
        $search = [
            'namespace '.$this->currentRoot.';',
            $this->currentRoot.'\\',
        ];

        $replace = [
            'namespace '.$this->argument('name').';',
            $this->argument('name').'\\',
        ];

        $this->replaceIn($path, $search, $replace);
    }

    /**
     * Set the bootstrap namespaces.
	 * 设置引导命名空间
     *
     * @return void
     */
    protected function setBootstrapNamespaces()
    {
        $search = [
            $this->currentRoot.'\\Http',
            $this->currentRoot.'\\Console',
            $this->currentRoot.'\\Exceptions',
        ];

        $replace = [
            $this->argument('name').'\\Http',
            $this->argument('name').'\\Console',
            $this->argument('name').'\\Exceptions',
        ];

        $this->replaceIn($this->getBootstrapPath(), $search, $replace);
    }

    /**
     * Set the namespace in the appropriate configuration files.
	 * 在适当的配置文件中设置命名空间
     *
     * @return void
     */
    protected function setConfigNamespaces()
    {
        $this->setAppConfigNamespaces();
        $this->setAuthConfigNamespace();
        $this->setServicesConfigNamespace();
    }

    /**
     * Set the application provider namespaces.
	 * 设置应用程序提供程序命名空间
     *
     * @return void
     */
    protected function setAppConfigNamespaces()
    {
        $search = [
            $this->currentRoot.'\\Providers',
            $this->currentRoot.'\\Http\\Controllers\\',
        ];

        $replace = [
            $this->argument('name').'\\Providers',
            $this->argument('name').'\\Http\\Controllers\\',
        ];

        $this->replaceIn($this->getConfigPath('app'), $search, $replace);
    }

    /**
     * Set the authentication User namespace.
	 * 设置鉴权用户命名空间
     *
     * @return void
     */
    protected function setAuthConfigNamespace()
    {
        $this->replaceIn(
            $this->getConfigPath('auth'),
            $this->currentRoot.'\\User',
            $this->argument('name').'\\User'
        );
    }

    /**
     * Set the services User namespace.
	 * 设置业务用户命名空间
     *
     * @return void
     */
    protected function setServicesConfigNamespace()
    {
        $this->replaceIn(
            $this->getConfigPath('services'),
            $this->currentRoot.'\\User',
            $this->argument('name').'\\User'
        );
    }

    /**
     * Set the PSR-4 namespace in the Composer file.
	 * 在Composer文件中设置PSR-4名称空间
     *
     * @return void
     */
    protected function setComposerNamespace()
    {
        $this->replaceIn(
            $this->getComposerPath(),
            str_replace('\\', '\\\\', $this->currentRoot).'\\\\',
            str_replace('\\', '\\\\', $this->argument('name')).'\\\\'
        );
    }

    /**
     * Set the namespace in database factory files.
	 * 在数据库工厂文件中设置名称空间
     *
     * @return void
     */
    protected function setDatabaseFactoryNamespaces()
    {
        $files = Finder::create()
                            ->in(database_path('factories'))
                            ->contains($this->currentRoot)
                            ->name('*.php');

        foreach ($files as $file) {
            $this->replaceIn(
                $file->getRealPath(),
                $this->currentRoot, $this->argument('name')
            );
        }
    }

    /**
     * Replace the given string in the given file.
	 * 替换给定文件中的给定字符串
     *
     * @param  string  $path
     * @param  string|array  $search
     * @param  string|array  $replace
     * @return void
     */
    protected function replaceIn($path, $search, $replace)
    {
        if ($this->files->exists($path)) {
            $this->files->put($path, str_replace($search, $replace, $this->files->get($path)));
        }
    }

    /**
     * Get the path to the bootstrap/app.php file.
	 * 获取bootstrap/app.php文件的路径
     *
     * @return string
     */
    protected function getBootstrapPath()
    {
        return $this->laravel->bootstrapPath().'/app.php';
    }

    /**
     * Get the path to the Composer.json file.
	 * 获取到Composer文件的路径
     *
     * @return string
     */
    protected function getComposerPath()
    {
        return base_path('composer.json');
    }

    /**
     * Get the path to the given configuration file.
	 * 获取给定配置文件的路径
     *
     * @param  string  $name
     * @return string
     */
    protected function getConfigPath($name)
    {
        return $this->laravel['path.config'].'/'.$name.'.php';
    }

    /**
     * Get the console command arguments.
	 * 获取控制台命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The desired namespace'],
        ];
    }
}
