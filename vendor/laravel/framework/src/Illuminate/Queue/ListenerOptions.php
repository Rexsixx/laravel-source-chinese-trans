<?php
/**
 * Illuminate，行列，侦听器的选项
 */

namespace Illuminate\Queue;

class ListenerOptions extends WorkerOptions
{
    /**
     * The environment the worker should run in.
	 * 工作线程应该运行的环境
     *
     * @var string
     */
    public $environment;

    /**
     * Create a new listener options instance.
	 * 创建一个新的侦听器选项实例
     *
     * @param  string  $environment
     * @param  int  $delay
     * @param  int  $memory
     * @param  int  $timeout
     * @param  int  $sleep
     * @param  int  $maxTries
     * @param  bool  $force
     * @return void
     */
    public function __construct($environment = null, $delay = 0, $memory = 128, $timeout = 60, $sleep = 3, $maxTries = 0, $force = false)
    {
        $this->environment = $environment;

        parent::__construct($delay, $memory, $timeout, $sleep, $maxTries, $force);
    }
}
