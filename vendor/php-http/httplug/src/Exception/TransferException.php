<?php
/**
 * Http，客户端，异常，转移异常
 */

namespace Http\Client\Exception;

use Http\Client\Exception;

/**
 * Base exception for transfer related exceptions.
 * 转移相关异常的基异常。
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class TransferException extends \RuntimeException implements Exception
{
}
