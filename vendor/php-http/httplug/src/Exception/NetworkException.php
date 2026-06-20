<?php
/**
 * Http，客户端，异常，网络异常
 */

namespace Http\Client\Exception;

/**
 * Thrown when the request cannot be completed because of network issues.
 * 当请求不能通过网络问题完成时抛出。
 *
 * There is no response object as this exception is thrown when no response has been received.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class NetworkException extends RequestException
{
}
