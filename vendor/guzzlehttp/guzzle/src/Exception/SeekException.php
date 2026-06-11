<?php
/**
 * GuzzleHttp，异常，搜索异常
 */

namespace GuzzleHttp\Exception;

use Psr\Http\Message\StreamInterface;

/**
 * Exception thrown when a seek fails on a stream.
 * 当查找在流上失败时抛出的异常。
 */
class SeekException extends \RuntimeException implements GuzzleException
{
    private $stream;

    public function __construct(StreamInterface $stream, $pos = 0, $msg = '')
    {
        $this->stream = $stream;
        $msg = $msg ?: 'Could not seek the stream to position ' . $pos;
        parent::__construct($msg);
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }
}
