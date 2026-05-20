<?php
/**
 * SebastianBergmann，CodeCoverage，缺失覆盖注释异常
 */

/*
 * This file is part of the php-code-coverage package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage;

/**
 * Exception that is raised when @covers must be used but is not.
 * 当@ cover必须使用时提出的异常,但不是
 */
final class MissingCoversAnnotationException extends RuntimeException
{
}
