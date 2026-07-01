<?php
/**
 * Illuminate，视图，引擎，编译器引擎
 */

namespace Illuminate\View\Engines;

use Exception;
use ErrorException;
use Illuminate\View\Compilers\CompilerInterface;

class CompilerEngine extends PhpEngine
{
    /**
     * The Blade compiler instance.
	 * Blade编译器实例
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * A stack of the last compiled templates.
	 * 最后编译的模板的堆栈
     *
     * @var array
     */
    protected $lastCompiled = [];

    /**
     * Create a new Blade view engine instance.
	 * 创建一个新的Blade视图引擎实例
     *
     * @param  \Illuminate\View\Compilers\CompilerInterface  $compiler
     * @return void
     */
    public function __construct(CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Get the evaluated contents of the view.
	 * 获取视图的求值内容
     *
     * @param  string  $path
     * @param  array   $data
     * @return string
     */
    public function get($path, array $data = [])
    {
        $this->lastCompiled[] = $path;

        // If this given view has expired, which means it has simply been edited since
        // it was last compiled, we will re-compile the views so we can evaluate a
        // fresh copy of the view. We'll pass the compiler the path of the view.
		// 如果此设定的视图已过期（即自上次编译以来已进行了修改），我们将重新编译这些视图，以便能够评估视图的新副本。
		// 我们将把视图的路径传递给编译器。
        if ($this->compiler->isExpired($path)) {
            $this->compiler->compile($path);
        }

        $compiled = $this->compiler->getCompiledPath($path);

        // Once we have the path to the compiled file, we will evaluate the paths with
        // typical PHP just like any other templates. We also keep a stack of views
        // which have been rendered for right exception messages to be generated.
		// 一旦我们获取到编译后的文件路径，我们就会像处理其他模板一样，用标准的 PHP 来对这些路径进行评估。
		// 我们还保存了一组已生成的视图，这些视图用于生成正确的错误消息。
        $results = $this->evaluatePath($compiled, $data);

        array_pop($this->lastCompiled);

        return $results;
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
        $e = new ErrorException($this->getMessage($e), 0, 1, $e->getFile(), $e->getLine(), $e);

        parent::handleViewException($e, $obLevel);
    }

    /**
     * Get the exception message for an exception.
	 * 获取异常的异常消息
     *
     * @param  \Exception  $e
     * @return string
     */
    protected function getMessage(Exception $e)
    {
        return $e->getMessage().' (View: '.realpath(last($this->lastCompiled)).')';
    }

    /**
     * Get the compiler implementation.
	 * 获取编译器实现
     *
     * @return \Illuminate\View\Compilers\CompilerInterface
     */
    public function getCompiler()
    {
        return $this->compiler;
    }
}
