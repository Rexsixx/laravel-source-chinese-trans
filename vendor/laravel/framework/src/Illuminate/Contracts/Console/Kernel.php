<?php
/**
 * Illuminate，契约，控制台，内核
 */

namespace Illuminate\Contracts\Console;

interface Kernel
{
    /**
     * Handle an incoming console command.
	 * 处理传入的控制台命令
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    public function handle($input, $output = null);

    /**
     * Run an Artisan console command by name.
	 * 按名称运行Artisan控制台命令
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * Queue an Artisan console command by name.
	 * 按名称将Artisan控制台命令排队
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function queue($command, array $parameters = []);

    /**
     * Get all of the commands registered with the console.
	 * 获取在控制台注册的所有命令
     *
     * @return array
     */
    public function all();

    /**
     * Get the output for the last run command.
	 * 获取最后一个运行命令的输出
     *
     * @return string
     */
    public function output();
}
