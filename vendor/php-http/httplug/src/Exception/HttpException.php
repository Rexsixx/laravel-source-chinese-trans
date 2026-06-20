<?php
/**
 * Http，客户端，异常，Http 异常
 */

namespace Http\Client\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Thrown when a response was received but the request itself failed.
 * 当响应被接收时抛出,但请求本身失败。
 *
 * In addition to the request, this exception always provides access to the response object.
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class HttpException extends RequestException
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param string            $message
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param \Exception|null   $previous
     */
    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ) {
        parent::__construct($message, $request, $previous);

        $this->response = $response;
        $this->code = $response->getStatusCode();
    }

    /**
     * Returns the response.
	 * 返回响应
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Factory method to create a new exception with a normalized error message.
	 * 工厂方法以标准化的错误消息创建一个新的异常
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param \Exception|null   $previous
     *
     * @return HttpException
     */
    public static function create(
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ) {
        $message = sprintf(
            '[url] %s [http method] %s [status code] %s [reason phrase] %s',
            $request->getRequestTarget(),
            $request->getMethod(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        return new self($message, $request, $response, $previous);
    }
}
