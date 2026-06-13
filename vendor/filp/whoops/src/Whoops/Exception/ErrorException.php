<?php
/**
 * Whoops，异常，错误异常
 */
 
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Exception;

use ErrorException as BaseErrorException;

/**
 * Wraps ErrorException; mostly used for typing (at least now)
 * to easily cleanup the stack trace of redundant info.
 * 包ErrorException;主要用于输入(至少现在),方便地清理冗余信息的堆栈跟踪。
 */
class ErrorException extends BaseErrorException
{
}
