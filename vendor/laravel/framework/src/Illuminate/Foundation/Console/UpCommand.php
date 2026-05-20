<?php
/**
 * Illuminate，基础，控制台，Up 命令
 */

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;

class UpCommand extends Command
{
    /**
     * The console command name.
	 * 控制台命令名
     *
     * @var string
     */
    protected $name = 'up';

    /**
     * The console command description.
	 * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Bring the application out of maintenance mode';

    /**
     * Execute the console command.
	 * 执行控制台命令
     *
     * @return void
     */
    public function handle()
    {
        @unlink(storage_path('framework/down'));

        $this->info('Application is now live.');
    }
}
