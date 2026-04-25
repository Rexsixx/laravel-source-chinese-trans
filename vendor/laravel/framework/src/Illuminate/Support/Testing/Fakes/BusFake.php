<?php
/**
 * Illuminate，支持，测试，佯装，总线 Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Contracts\Bus\Dispatcher;
use PHPUnit\Framework\Assert as PHPUnit;

class BusFake implements Dispatcher
{
    /**
     * The commands that have been dispatched.
	 * 已调度的命令
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Assert if a job was dispatched based on a truth-test callback.
	 * 断言作业是否基于真值测试回调进行分派
     *
     * @param  string  $command
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertDispatched($command, $callback = null)
    {
        if (is_numeric($callback)) {
            return $this->assertDispatchedTimes($command, $callback);
        }

        PHPUnit::assertTrue(
            $this->dispatched($command, $callback)->count() > 0,
            "The expected [{$command}] job was not dispatched."
        );
    }

    /**
     * Assert if a job was pushed a number of times.
	 * 判断一个作业是否被推送了多次
     *
     * @param  string  $command
     * @param  int  $times
     * @return void
     */
    protected function assertDispatchedTimes($command, $times = 1)
    {
        PHPUnit::assertTrue(
            ($count = $this->dispatched($command)->count()) === $times,
            "The expected [{$command}] job was pushed {$count} times instead of {$times} times."
        );
    }

    /**
     * Determine if a job was dispatched based on a truth-test callback.
	 * 确定是否根据true -test回调分派了作业
     *
     * @param  string  $command
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotDispatched($command, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->dispatched($command, $callback)->count() === 0,
            "The unexpected [{$command}] job was dispatched."
        );
    }

    /**
     * Get all of the jobs matching a truth-test callback.
	 * 获取所有符合真实测试回调的工作
     *
     * @param  string  $command
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function dispatched($command, $callback = null)
    {
        if (! $this->hasDispatched($command)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->commands[$command])->filter(function ($command) use ($callback) {
            return $callback($command);
        });
    }

    /**
     * Determine if there are any stored commands for a given class.
	 * 确定是否有任何针对给定类的存储命令
     *
     * @param  string  $command
     * @return bool
     */
    public function hasDispatched($command)
    {
        return isset($this->commands[$command]) && ! empty($this->commands[$command]);
    }

    /**
     * Dispatch a command to its appropriate handler.
	 * 将命令分派给相应的处理程序
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatch($command)
    {
        return $this->dispatchNow($command);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
	 * 将命令分派给当前进程中相应的处理程序
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchNow($command, $handler = null)
    {
        $this->commands[get_class($command)][] = $command;
    }

    /**
     * Set the pipes commands should be piped through before dispatching.
	 * 设置调度前需要通过管道的命令
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes)
    {
        //
    }
}
