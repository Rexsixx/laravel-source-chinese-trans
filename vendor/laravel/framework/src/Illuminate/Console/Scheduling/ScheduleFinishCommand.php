<?php
/**
 * Illuminate，控制台，线程调度，Schedule 完成命令
 */

namespace Illuminate\Console\Scheduling;

use Illuminate\Console\Command;

class ScheduleFinishCommand extends Command
{
    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $signature = 'schedule:finish {id}';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Handle the completion of a scheduled command';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
	 * 指示该命令是否应该显示在Artisan命令列表中
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * The schedule instance.
	 * 调度实例
     *
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * Create a new command instance.
	 * 创建一个新的命令实例
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;

        parent::__construct();
    }

    /**
     * Execute the console command.
	 * 执行控制台命令
     *
     * @return void
     */
    public function handle()
    {
        collect($this->schedule->events())->filter(function ($value) {
            return $value->mutexName() == $this->argument('id');
        })->each->callAfterCallbacks($this->laravel);
    }
}
