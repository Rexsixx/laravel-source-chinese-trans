<?php
/**
 * Psr，简单缓存，无效参数异常
 */

namespace Psr\SimpleCache;

/**
 * Exception interface for invalid cache arguments.
 *
 * When an invalid argument is passed it must throw an exception which implements
 * this interface
 */
interface InvalidArgumentException extends CacheException
{
}
