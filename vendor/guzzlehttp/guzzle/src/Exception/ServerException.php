<?php
/**
 * GuzzleHttp，异常，服务器异常
 */

namespace GuzzleHttp\Exception;

/**
 * Exception when a server error is encountered (5xx codes)
 * 当遇到服务器错误时(5xx代码)
 */
class ServerException extends BadResponseException
{
}
