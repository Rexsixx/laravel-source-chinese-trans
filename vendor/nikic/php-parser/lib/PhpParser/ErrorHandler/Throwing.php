<?php declare(strict_types=1);

/**
 * PhpParser，错误处理程序，Throwing
 */

namespace PhpParser\ErrorHandler;

use PhpParser\Error;
use PhpParser\ErrorHandler;

/**
 * Error handler that handles all errors by throwing them.
 * 错误处理程序,通过抛出错误处理所有错误。
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error) {
        throw $error;
    }
}
