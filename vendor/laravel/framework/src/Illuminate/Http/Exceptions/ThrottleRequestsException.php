<?php
/**
 * Illuminate，Http，异常，节流请求异常
 */

namespace Illuminate\Http\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ThrottleRequestsException extends HttpException
{
    /**
     * Create a new exception instance.
	 * 创建一个新的异常实例
     *
     * @param  string|null  $message
     * @param  \Exception|null  $previous
     * @param  array  $headers
     * @param  int  $code
     * @return void
     */
    public function __construct($message = null, Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(429, $message, $previous, $headers, $code);
    }
}
