<?php
/**
 * Illuminate，基础，Http，异常，维护模式异常
 */

namespace Illuminate\Foundation\Http\Exceptions;

use Exception;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class MaintenanceModeException extends ServiceUnavailableHttpException
{
    /**
     * When the application was put in maintenance mode.
	 * 当应用程序处于维护模式时
     *
     * @var \Illuminate\Support\Carbon
     */
    public $wentDownAt;

    /**
     * The number of seconds to wait before retrying.
	 * 重试前等待的秒数
     *
     * @var int
     */
    public $retryAfter;

    /**
     * When the application should next be available.
	 * 应用程序下次可用的时间
     *
     * @var \Illuminate\Support\Carbon
     */
    public $willBeAvailableAt;

    /**
     * Create a new exception instance.
	 * 创建一个新的异常实例
     *
     * @param  int  $time
     * @param  int  $retryAfter
     * @param  string  $message
     * @param  \Exception  $previous
     * @param  int  $code
     * @return void
     */
    public function __construct($time, $retryAfter = null, $message = null, Exception $previous = null, $code = 0)
    {
        parent::__construct($retryAfter, $message, $previous, $code);

        $this->wentDownAt = Carbon::createFromTimestamp($time);

        if ($retryAfter) {
            $this->retryAfter = $retryAfter;

            $this->willBeAvailableAt = Carbon::createFromTimestamp($time)->addSeconds($this->retryAfter);
        }
    }
}
