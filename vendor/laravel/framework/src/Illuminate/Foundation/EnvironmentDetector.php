<?php
/**
 * Illuminate，基础，环境检测器
 */

namespace Illuminate\Foundation;

use Closure;
use Illuminate\Support\Str;

class EnvironmentDetector
{
    /**
     * Detect the application's current environment.
	 * 检测应用程序的当前环境
     *
     * @param  \Closure  $callback
     * @param  array|null  $consoleArgs
     * @return string
     */
    public function detect(Closure $callback, $consoleArgs = null)
    {
        if ($consoleArgs) {
            return $this->detectConsoleEnvironment($callback, $consoleArgs);
        }

        return $this->detectWebEnvironment($callback);
    }

    /**
     * Set the application environment for a web request.
	 * 设置web请求的应用环境
     *
     * @param  \Closure  $callback
     * @return string
     */
    protected function detectWebEnvironment(Closure $callback)
    {
        return call_user_func($callback);
    }

    /**
     * Set the application environment from command-line arguments.
	 * 通过命令行参数设置应用程序环境
     *
     * @param  \Closure  $callback
     * @param  array  $args
     * @return string
     */
    protected function detectConsoleEnvironment(Closure $callback, array $args)
    {
        // First we will check if an environment argument was passed via console arguments
        // and if it was that automatically overrides as the environment. Otherwise, we
        // will check the environment as a "web" request like a typical HTTP request.
		// 首先，我们将检查是否通过控制台参数传递了环境参数，如果是的话，那么该参数将自动作为环境参数被使用。
		// 否则，我们将像处理常规的 HTTP 请求那样，以“网络”请求的形式来检查环境。
        if (! is_null($value = $this->getEnvironmentArgument($args))) {
            return $value;
        }

        return $this->detectWebEnvironment($callback);
    }

    /**
     * Get the environment argument from the console.
	 * 从控制台中获取环境参数
     *
     * @param  array  $args
     * @return string|null
     */
    protected function getEnvironmentArgument(array $args)
    {
        foreach ($args as $i => $value) {
            if ($value === '--env') {
                return $args[$i + 1] ?? null;
            }

            if (Str::startsWith($value, '--env')) {
                return head(array_slice(explode('=', $value), 1));
            }
        }
    }
}
