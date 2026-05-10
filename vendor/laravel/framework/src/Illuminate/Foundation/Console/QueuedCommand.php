<?php
/**
 * Illuminate，基础，控制台，排队命令
 */

namespace Illuminate\Foundation\Console;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Console\Kernel as KernelContract;

class QueuedCommand implements ShouldQueue
{
    use Dispatchable, Queueable;

    /**
     * The data to pass to the Artisan command.
	 * 传递给Artisan命令的数据
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new job instance.
	 * 创建一个新的作业实例
     *
     * @param  array  $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Handle the job.
	 * 处理作业
     *
     * @param  \Illuminate\Contracts\Console\Kernel  $kernel
     * @return void
     */
    public function handle(KernelContract $kernel)
    {
        call_user_func_array([$kernel, 'call'], $this->data);
    }
}
