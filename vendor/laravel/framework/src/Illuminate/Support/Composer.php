<?php
/**
 * Illuminate，支持，Composer
 */

namespace Illuminate\Support;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class Composer
{
    /**
     * The filesystem instance.
	 * 文件系统实例
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The working path to regenerate from.
	 * 工作路径以再生
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Create a new Composer manager instance.
	 * 创建一个新的Composer管理器实例
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string|null  $workingPath
     * @return void
     */
    public function __construct(Filesystem $files, $workingPath = null)
    {
        $this->files = $files;
        $this->workingPath = $workingPath;
    }

    /**
     * Regenerate the Composer autoloader files.
	 * 重新生成Composer的autoloader文件
     *
     * @param  string  $extra
     * @return void
     */
    public function dumpAutoloads($extra = '')
    {
        $process = $this->getProcess();

        $process->setCommandLine(trim($this->findComposer().' dump-autoload '.$extra));

        $process->run();
    }

    /**
     * Regenerate the optimized Composer autoloader files.
	 * 重新生成优化的Composer autoloader文件
     *
     * @return void
     */
    public function dumpOptimized()
    {
        $this->dumpAutoloads('--optimize');
    }

    /**
     * Get the composer command for the environment.
	 * 为环境获取作曲家命令
     *
     * @return string
     */
    protected function findComposer()
    {
        if ($this->files->exists($this->workingPath.'/composer.phar')) {
            return ProcessUtils::escapeArgument((new PhpExecutableFinder)->find(false)).' composer.phar';
        }

        return 'composer';
    }

    /**
     * Get a new Symfony process instance.
	 * 获取一个新的Symfony流程实例
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess()
    {
        return (new Process('', $this->workingPath))->setTimeout(null);
    }

    /**
     * Set the working path used by the class.
	 * 设置类使用的工作路径
     *
     * @param  string  $path
     * @return $this
     */
    public function setWorkingPath($path)
    {
        $this->workingPath = realpath($path);

        return $this;
    }
}
