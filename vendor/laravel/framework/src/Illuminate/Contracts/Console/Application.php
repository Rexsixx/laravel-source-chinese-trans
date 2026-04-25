<?php
/**
 * Illuminate，契约，控制台，应用
 */

namespace Illuminate\Contracts\Console;

interface Application
{
    /**
     * Call a console application command.
	 * 调用控制台应用程序命令
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * Get the output from the last command.
	 * 获取最后一个命令的输出
     *
     * @return string
     */
    public function output();
}
