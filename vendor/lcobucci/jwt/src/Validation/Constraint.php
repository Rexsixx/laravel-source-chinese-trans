<?php
/**
 * Lcobucci，JWT，确认，约束
 */

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Token;

interface Constraint
{
    /** @throws ConstraintViolation */
    public function assert(Token $token);
}
