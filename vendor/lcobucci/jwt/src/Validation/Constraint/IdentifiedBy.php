<?php
/**
 * Lcobucci，JWT，确认，约束，确定的
 */

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class IdentifiedBy implements Constraint
{
    /** @var string */
    private $id;

    /** @param string $id */
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function assert(Token $token)
    {
        if (! $token->isIdentifiedBy($this->id)) {
            throw new ConstraintViolation(
                'The token is not identified with the expected ID'
            );
        }
    }
}
