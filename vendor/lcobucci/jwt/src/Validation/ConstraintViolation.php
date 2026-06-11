<?php
/**
 * Lcobucci，JWT，确认，约束违反
 */

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Exception;
use RuntimeException;

final class ConstraintViolation extends RuntimeException implements Exception
{
}
