<?php
/**
 * Illuminate，引擎，Php 引擎
 */

namespace Illuminate\View\Engines;

use Exception;
use Throwable;
use Illuminate\Contracts\View\Engine;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class PhpEngine implements Engine
{
    /**
     * Get the evaluated contents of the view.
	 * 获取视图的评估内容
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        return $this->evaluatePath($path, $data);
    }

    /**
     * Get the evaluated contents of the view at the given path.
	 * 获取给定路径上的视图的求值内容
     *
     * @param  string  $__path
     * @param  array   $__data
     * @return string
     */
    protected function evaluatePath($__path, $__data)
    {
        $obLevel = ob_get_level();

        ob_start();

        extract($__data, EXTR_SKIP);

        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
		// 我们将在一个 try/catch 块内对视图的内容进行评估，
		// 这样就能清除在错误发生或异常抛出之前可能泄漏出来的任何残余输出。这样可以避免部分视图出现泄露的情况。
        try {
            include $__path;
        } catch (Exception $e) {
            $this->handleViewException($e, $obLevel);
        } catch (Throwable $e) {
            $this->handleViewException(new FatalThrowableError($e), $obLevel);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle a view exception.
	 * 处理视图异常
     *
     * @param  \Exception  $e
     * @param  int  $obLevel
     * @return void
     *
     * @throws \Exception
     */
    protected function handleViewException(Exception $e, $obLevel)
    {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    }
}
