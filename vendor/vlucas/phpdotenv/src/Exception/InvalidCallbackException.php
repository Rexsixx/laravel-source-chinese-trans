<?php
/**
 * Dotenv，异常，无效应答异常
 */

namespace Dotenv\Exception;

use InvalidArgumentException;

/**
 * This is the invalid callback exception class.
 * 这是无效的回调异常类
 */
class InvalidCallbackException extends InvalidArgumentException implements ExceptionInterface
{
    //
}
