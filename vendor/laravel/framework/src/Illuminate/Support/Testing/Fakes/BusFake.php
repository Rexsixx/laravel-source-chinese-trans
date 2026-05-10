<?php
/**
 * Illuminate，支持，测试，Fake，总线 Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Contracts\Bus\Dispatcher;
use PHPUnit\Framework\Assert as PHPUnit;

class BusFake implements Dispatcher
{
    /**
     * The commands that have been dispatched.
	 * 已发出的命令
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Assert if a job was dispatched based on a truth-test callback.
	 * 断言是否根据trutest callback发送了一份工作
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
	 * 断言如果工作被推了很多次
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
	 * 确定是否根据trutest callback发送了一份工作
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
	 * 让所有的工作都匹配一个truand测试回调
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
	 * 确定给定类是否有存储命令
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
	 * 向其适当的处理程序发送命令
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
	 * 在当前过程中向其适当的处理程序发送命令
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
	 * 在调度前设置管道命令
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes)
    {
        //
    }

    /**
     * Determine if the given command has a handler.
	 * 确定给定的命令是否有处理程序
     *
     * @param  mixed  $command
     * @return bool
     */
    public function hasCommandHandler($command)
    {
        return false;
    }

    /**
     * Retrieve the handler for a command.
	 * 检索命令的处理程序
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function getCommandHandler($command)
    {
        return false;
    }

    /**
     * Map a command to a handler.
	 * 向处理程序映射一个命令
     *
     * @param  array  $map
     * @return $this
     */
    public function map(array $map)
    {
        return $this;
    }
}
