<?php declare(strict_types=1);

/**
 * PhpParser，错误处理程序
 */

namespace PhpParser;

interface ErrorHandler
{
    /**
     * Handle an error generated during lexing, parsing or some other operation.
	 * 处理在lexing、解析或其他操作过程中生成的错误。
     *
     * @param Error $error The error that needs to be handled
     */
    public function handleError(Error $error);
}
