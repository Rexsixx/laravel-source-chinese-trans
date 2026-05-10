<?php
/**
 * Dotenv，异常，无效路径异常
 */

namespace Dotenv\Exception;

use InvalidArgumentException;

/**
 * This is the invalid path exception class.
 * 这是无效路径异常类
 */
class InvalidPathException extends InvalidArgumentException implements ExceptionInterface
{
    //
}
