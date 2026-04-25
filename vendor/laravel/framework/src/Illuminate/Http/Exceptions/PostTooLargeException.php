<?php
/**
 * Illuminate，Http，异常，请求太大异常
 */

namespace Illuminate\Http\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostTooLargeException extends HttpException
{
    /**
     * PostTooLargeException constructor.
	 * PostTooLargeException构造函数
     *
     * @param  string|null  $message
     * @param  \Exception|null  $previous
     * @param  array  $headers
     * @param  int  $code
     * @return void
     */
    public function __construct($message = null, Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(413, $message, $previous, $headers, $code);
    }
}
