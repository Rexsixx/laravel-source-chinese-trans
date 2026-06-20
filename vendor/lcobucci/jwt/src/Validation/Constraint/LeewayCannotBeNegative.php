<?php
/**
 * Lcobucci，JWT，确认，约束，回旋余地不可能是负的
 */

namespace Lcobucci\JWT\Validation\Constraint;

use InvalidArgumentException;
use Lcobucci\JWT\Exception;

final class LeewayCannotBeNegative extends InvalidArgumentException implements Exception
{
    /** @return self */
    public static function create()
    {
        return new self('Leeway cannot be negative');
    }
}
