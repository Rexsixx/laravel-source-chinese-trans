<?php
/**
 * Illuminate，基础，总线，等待链
 */

namespace Illuminate\Foundation\Bus;

class PendingChain
{
    /**
     * The class name of the job being dispatched.
	 * 正在分派的作业的类名
     *
     * @var string
     */
    public $class;

    /**
     * The jobs to be chained.
	 * 这些作业将被捆绑起来
     *
     * @var array
     */
    public $chain;

    /**
     * Create a new PendingChain instance.
	 * 创建一个新的PendingChain实例
     *
     * @param  string  $class
     * @param  array  $chain
     * @return void
     */
    public function __construct($class, $chain)
    {
        $this->class = $class;
        $this->chain = $chain;
    }

    /**
     * Dispatch the job with the given arguments.
	 * 使用给定的参数调度作业
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatch()
    {
        return (new PendingDispatch(
            new $this->class(...func_get_args())
        ))->chain($this->chain);
    }
}
