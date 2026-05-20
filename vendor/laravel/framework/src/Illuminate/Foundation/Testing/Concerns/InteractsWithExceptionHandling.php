<?php
/**
 * Illuminate，基础，测试，问题，与异常处理交互
 */

namespace Illuminate\Foundation\Testing\Concerns;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait InteractsWithExceptionHandling
{
    /**
     * The original exception handler.
	 * 原始异常处理程序
     *
     * @var ExceptionHandler|null
     */
    protected $originalExceptionHandler;

    /**
     * Restore exception handling.
	 * 恢复异常处理
     *
     * @return $this
     */
    protected function withExceptionHandling()
    {
        if ($this->originalExceptionHandler) {
            $this->app->instance(ExceptionHandler::class, $this->originalExceptionHandler);
        }

        return $this;
    }

    /**
     * Only handle the given exceptions via the exception handler.
	 * 只通过异常处理程序处理给定的异常
     *
     * @param  array  $exceptions
     * @return $this
     */
    protected function handleExceptions(array $exceptions)
    {
        return $this->withoutExceptionHandling($exceptions);
    }

    /**
     * Only handle validation exceptions via the exception handler.
	 * 只通过异常处理程序处理验证异常
     *
     * @return $this
     */
    protected function handleValidationExceptions()
    {
        return $this->handleExceptions([ValidationException::class]);
    }

    /**
     * Disable exception handling for the test.
	 * 禁用测试的异常处理
     *
     * @param  array  $except
     * @return $this
     */
    protected function withoutExceptionHandling(array $except = [])
    {
        if ($this->originalExceptionHandler == null) {
            $this->originalExceptionHandler = app(ExceptionHandler::class);
        }

        $this->app->instance(ExceptionHandler::class, new class($this->originalExceptionHandler, $except) implements ExceptionHandler {
            protected $except;
            protected $originalHandler;

            /**
             * Create a new class instance.
			 * 创建一个新的类实例
             *
             * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $originalHandler
             * @param  array  $except
             * @return void
             */
            public function __construct($originalHandler, $except = [])
            {
                $this->except = $except;
                $this->originalHandler = $originalHandler;
            }

            /**
             * Report the given exception.
			 * 报告给定的异常
             *
             * @param  \Exception  $e
             * @return void
             */
            public function report(Exception $e)
            {
                //
            }

            /**
             * Render the given exception.
			 * 呈现给定的异常
             *
             * @param  \Illuminate\Http\Request  $request
             * @param  \Exception  $e
             * @return mixed
             *
             * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Exception
             */
            public function render($request, Exception $e)
            {
                if ($e instanceof NotFoundHttpException) {
                    throw new NotFoundHttpException(
                        "{$request->method()} {$request->url()}", null, $e->getCode()
                    );
                }

                foreach ($this->except as $class) {
                    if ($e instanceof $class) {
                        return $this->originalHandler->render($request, $e);
                    }
                }

                throw $e;
            }

            /**
             * Render the exception for the console.
			 * 为控制台呈现异常
             *
             * @param  \Symfony\Component\Console\Output\OutputInterface  $output
             * @param  \Exception  $e
             * @return void
             */
            public function renderForConsole($output, Exception $e)
            {
                (new ConsoleApplication)->renderException($e, $output);
            }
        });

        return $this;
    }
}
