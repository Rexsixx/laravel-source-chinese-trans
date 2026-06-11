<?php
/**
 * Illuminate，控制台，线程调度，调度运行命令 schedule:run
 */

namespace Illuminate\Console\Scheduling;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class ScheduleRunCommand extends Command
{
    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $name = 'schedule:run';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Run the scheduled commands';

    /**
     * The schedule instance.
	 * 调度实例
     *
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * The 24 hour timestamp this scheduler command started running.
	 * 这个调度器命令开始运行的时间戳是24小时
     *
     * @var \Illuminate\Support\Carbon;
     */
    protected $startedAt;

    /**
     * Check if any events ran.
	 * 检查是否运行了任何事件
     *
     * @var bool
     */
    protected $eventsRan = false;

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

        $this->startedAt = Carbon::now();

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
        foreach ($this->schedule->dueEvents($this->laravel) as $event) {
            if (! $event->filtersPass($this->laravel)) {
                continue;
            }

            if ($event->onOneServer) {
                $this->runSingleServerEvent($event);
            } else {
                $this->runEvent($event);
            }

            $this->eventsRan = true;
        }

        if (! $this->eventsRan) {
            $this->info('No scheduled commands are ready to run.');
        }
    }

    /**
     * Run the given single server event.
	 * 运行给定的单个服务器事件
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runSingleServerEvent($event)
    {
        if ($this->schedule->serverShouldRun($event, $this->startedAt)) {
            $this->runEvent($event);
        } else {
            $this->line('<info>Skipping command (has already run on another server):</info> '.$event->getSummaryForDisplay());
        }
    }

    /**
     * Run the given event.
	 * 运行给定的事件
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    protected function runEvent($event)
    {
        $this->line('<info>Running scheduled command:</info> '.$event->getSummaryForDisplay());

        $event->run($this->laravel);

        $this->eventsRan = true;
    }
}
