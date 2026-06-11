<?php
/**
 * Lcobucci，JWT，编码，不能编码内容
 */

namespace Lcobucci\JWT\Encoding;

use JsonException;
use Lcobucci\JWT\Exception;
use RuntimeException;

final class CannotEncodeContent extends RuntimeException implements Exception
{
    /**
     * @param JsonException $previous
     *
     * @return self
     */
    public static function jsonIssues(JsonException $previous)
    {
        return new self('Error while encoding to JSON', 0, $previous);
    }
}
