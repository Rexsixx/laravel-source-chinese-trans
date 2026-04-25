<?php
/**
 * Illuminate，基础，测试，问题，与控制台交互
 */

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;

trait InteractsWithConsole
{
    /**
     * Call artisan command and return code.
	 * 调用artisan命令并返回代码
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function artisan($command, $parameters = [])
    {
        return $this->app[Kernel::class]->call($command, $parameters);
    }
}
