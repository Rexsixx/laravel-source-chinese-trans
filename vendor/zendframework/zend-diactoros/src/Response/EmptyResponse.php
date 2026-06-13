<?php
/**
 * Zend，Diactoros，响应，空响应
 */

/**
 * @see       https://github.com/zendframework/zend-diactoros for the canonical source repository
 * @copyright Copyright (c) 2015-2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-diactoros/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Zend\Diactoros\Response;

use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * A class representing empty HTTP responses.
 * 表示空HTTP响应的类。
 */
class EmptyResponse extends Response
{
    /**
     * Create an empty response with the given status code.
	 * 使用给定的状态码创建一个空响应
     *
     * @param int $status Status code for the response, if any.
     * @param array $headers Headers for the response, if any.
     */
    public function __construct(int $status = 204, array $headers = [])
    {
        $body = new Stream('php://temp', 'r');
        parent::__construct($body, $status, $headers);
    }

    /**
     * Create an empty response with the given headers.
     *
     * @param array $headers Headers for the response.
     * @return EmptyResponse
     */
    public static function withHeaders(array $headers) : EmptyResponse
    {
        return new static(204, $headers);
    }
}
