<?php
/**
 * Http，客户端，异常，请求异常
 */

namespace Http\Client\Exception;

use Psr\Http\Message\RequestInterface;

/**
 * Exception for when a request failed, providing access to the failed request.
 * 异常,当请求失败时,提供对失败请求的访问。
 *
 * This could be due to an invalid request, or one of the extending exceptions
 * for network errors or HTTP error responses.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class RequestException extends TransferException
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string           $message
     * @param RequestInterface $request
     * @param \Exception|null  $previous
     */
    public function __construct($message, RequestInterface $request, \Exception $previous = null)
    {
        $this->request = $request;

        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the request.
	 * 返回请求
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}
