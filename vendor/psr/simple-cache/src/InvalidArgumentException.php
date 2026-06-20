<?php
/**
 * Psy，简单缓存，无效参数异常
 */

namespace Psr\SimpleCache;

/**
 * Exception interface for invalid cache arguments.
 * 无效缓存参数的异常接口。
 *
 * When an invalid argument is passed it must throw an exception which implements
 * this interface
 * 当一个无效的参数被传递时,它必须抛出一个异常,实现这个接口。
 */
interface InvalidArgumentException extends CacheException
{
}
