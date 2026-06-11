<?php
/**
 * Illuminate，行列，呼叫队列闭包
 */

namespace Illuminate\Queue;

use ReflectionFunction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Container\Container;

class CallQueuedClosure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The serializable Closure instance.
	 * 可序列化的闭包实例
     *
     * @var \Illuminate\Queue\SerializableClosure
     */
    public $closure;

    /**
     * Indicate if the job should be deleted when models are missing.
	 * 创建一个新的处理程序实例
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
	 * 创建一个新的作业实例
     *
     * @param  \Illuminate\Queue\SerializableClosure  $closure
     * @return void
     */
    public function __construct(SerializableClosure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Execute the job.
	 * 执行作业
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return void
     */
    public function handle(Container $container)
    {
        $container->call($this->closure->getClosure());
    }

    /**
     * Get the display name for the queued job.
	 * 获取排队作业的显示名称
     *
     * @return string
     */
    public function displayName()
    {
        $reflection = new ReflectionFunction($this->closure->getClosure());

        return 'Closure ('.basename($reflection->getFileName()).':'.$reflection->getStartLine().')';
    }
}
