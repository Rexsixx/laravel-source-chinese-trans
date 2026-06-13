<?php
/**
 * Lcobucci，JWT，确认，没有约束条件
 */

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Exception;
use RuntimeException;

final class NoConstraintsGiven extends RuntimeException implements Exception
{
}
