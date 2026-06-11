<?php
/**
 * GuzzleHttp，异常，连接异常
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Exception thrown when a connection cannot be established.
 * 当无法建立连接时抛出的异常。
 *
 * Note that no response is present for a ConnectException
 */
class ConnectException extends RequestException
{
    public function __construct(
        $message,
        RequestInterface $request,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        parent::__construct($message, $request, null, $previous, $handlerContext);
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return false;
    }
}
