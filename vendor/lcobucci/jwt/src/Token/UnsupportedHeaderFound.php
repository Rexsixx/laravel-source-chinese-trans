<?php
/**
 * Lcobucci，JWT，令牌，未支持的头发现
 */

namespace Lcobucci\JWT\Token;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class UnsupportedHeaderFound extends InvalidArgumentException implements Exception
{
    /** @return self */
    public static function encryption()
    {
        return new self('Encryption is not supported yet');
    }
}
