<?php
/**
 * Illuminate，队列，监听器
 */

namespace Illuminate\Queue;

use Closure;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class Listener
{
    /**
     * The command working path.
	 * 命令工作路径
     *
     * @var string
     */
    protected $commandPath;

    /**
     * The environment the workers should run under.
	 * 工作线程的运行环境
     *
     * @var string
     */
    protected $environment;

    /**
     * The amount of seconds to wait before polling the queue.
	 * 轮询队列之前等待的秒数
     *
     * @var int
     */
    protected $sleep = 3;

    /**
     * The amount of times to try a job before logging it failed.
	 * 在记录作业失败之前尝试该作业的次数
     *
     * @var int
     */
    protected $maxTries = 0;

    /**
     * The output handler callback.
	 * 输出处理程序回调
     *
     * @var \Closure|null
     */
    protected $outputHandler;

    /**
     * Create a new queue listener.
	 * 创建一个新的队列侦听器
     *
     * @param  string  $commandPath
     * @return void
     */
    public function __construct($commandPath)
    {
        $this->commandPath = $commandPath;
    }

    /**
     * Get the PHP binary.
	 * 获取PHP二进制文件
     *
     * @return string
     */
    protected function phpBinary()
    {
        return (new PhpExecutableFinder)->find(false);
    }

    /**
     * Get the Artisan binary.
	 * 获取Artisan二进制文件
     *
     * @return string
     */
    protected function artisanBinary()
    {
        return defined('ARTISAN_BINARY') ? ARTISAN_BINARY : 'artisan';
    }

    /**
     * Listen to the given queue connection.
	 * 监听给定的队列连接
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return void
     */
    public function listen($connection, $queue, ListenerOptions $options)
    {
        $process = $this->makeProcess($connection, $queue, $options);

        while (true) {
            $this->runProcess($process, $options->memory);
        }
    }

    /**
     * Create a new Symfony process for the worker.
	 * 为工作者创建一个新的Symfony进程
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return \Symfony\Component\Process\Process
     */
    public function makeProcess($connection, $queue, ListenerOptions $options)
    {
        $command = $this->createCommand(
            $connection,
            $queue,
            $options
        );

        // If the environment is set, we will append it to the command array so the
        // workers will run under the specified environment. Otherwise, they will
        // just run under the production environment which is not always right.
		// 如果环境已设置好，我们将将其添加到命令数组中，以便工作进程能够在指定的环境中运行。
		// 否则，他们就会在不恰当的生产环境中运行程序，而这样的操作往往并不可靠。
        if (isset($options->environment)) {
            $command = $this->addEnvironment($command, $options);
        }

        return new Process(
            $command,
            $this->commandPath,
            null,
            null,
            $options->timeout
        );
    }

    /**
     * Add the environment option to the given command.
	 * 将环境选项添加到给定命令中
     *
     * @param  array  $command
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return array
     */
    protected function addEnvironment($command, ListenerOptions $options)
    {
        return array_merge($command, ["--env={$options->environment}"]);
    }

    /**
     * Create the command with the listener options.
	 * 使用侦听器选项创建命令
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Queue\ListenerOptions  $options
     * @return array
     */
    protected function createCommand($connection, $queue, ListenerOptions $options)
    {
        return array_filter([
            $this->phpBinary(),
            $this->artisanBinary(),
            'queue:work',
            $connection,
            '--once',
            "--queue={$queue}",
            "--delay={$options->delay}",
            "--memory={$options->memory}",
            "--sleep={$options->sleep}",
            "--tries={$options->maxTries}",
        ], function ($value) {
            return ! is_null($value);
        });
    }

    /**
     * Run the given process.
	 * 运行给定的进程
     *
     * @param  \Symfony\Component\Process\Process  $process
     * @param  int  $memory
     * @return void
     */
    public function runProcess(Process $process, $memory)
    {
        $process->run(function ($type, $line) {
            $this->handleWorkerOutput($type, $line);
        });

        // Once we have run the job we'll go check if the memory limit has been exceeded
        // for the script. If it has, we will kill this script so the process manager
        // will restart this with a clean slate of memory automatically on exiting.
		// 一旦我们完成了这项任务，就会去检查一下该脚本的内存使用量是否已超过限制。
		// 如果存在这种情况，我们将删除该脚本，这样进程管理器在退出时就能自动以全新的内存状态重新启动此程序。
        if ($this->memoryExceeded($memory)) {
            $this->stop();
        }
    }

    /**
     * Handle output from the worker process.
	 * 处理工作进程的输出
     *
     * @param  int  $type
     * @param  string  $line
     * @return void
     */
    protected function handleWorkerOutput($type, $line)
    {
        if (isset($this->outputHandler)) {
            call_user_func($this->outputHandler, $type, $line);
        }
    }

    /**
     * Determine if the memory limit has been exceeded.
	 * 确定是否已超过内存限制
     *
     * @param  int  $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
	 * 别再听了，跳出剧本。
     *
     * @return void
     */
    public function stop()
    {
        die;
    }

    /**
     * Set the output handler callback.
	 * 设置输出处理程序回调
     *
     * @param  \Closure  $outputHandler
     * @return void
     */
    public function setOutputHandler(Closure $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }
}
