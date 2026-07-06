<?php
/**
 * Illuminate，基础，测试，Assert
 */

namespace Illuminate\Foundation\Testing;

use ArrayAccess;
use PHPUnit\Util\InvalidArgumentHelper;
use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\Constraint\ArraySubset;

/**
 * @internal This class is not meant to be used or overwritten outside the framework itself.
 * 这个类不打算在框架本身之外使用或覆盖。
 */
abstract class Assert extends PHPUnit
{
    /**
     * Asserts that an array has a specified subset.
	 * 断言数组是否具有指定的子集。
     *
     * This method was taken over from PHPUnit where it was deprecated. See link for more info.
	 * 这个方法是从PHPUnit中继承过来的，在PHPUnit中它被弃用了。更多信息请参见链接。
     *
     * @param  array|\ArrayAccess  $subset
     * @param  array|\ArrayAccess  $array
     * @param  bool  $checkForObjectIdentity
     * @param  string  $message
     * @return void
     *
     * @link https://github.com/sebastianbergmann/phpunit/issues/3494
     */
    public static function assertArraySubset($subset, $array, bool $checkForObjectIdentity = false, string $message = ''): void
    {
        if (! (is_array($subset) || $subset instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(1, 'array or ArrayAccess');
        }

        if (! (is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(2, 'array or ArrayAccess');
        }

        $constraint = new ArraySubset($subset, $checkForObjectIdentity);

        static::assertThat($array, $constraint, $message);
    }
}
