<?php
/**
 * Faker，计算器，TCNo
 */

namespace Faker\Calculator;

use InvalidArgumentException;

class TCNo
{
    /**
     * Generates Turkish Identity Number Checksum
     * Gets first 9 digit as prefix and calcuates checksums
	 * 生成土耳其身份证号码校验和，取前9位作为前缀并计算校验和。
     *
     * https://en.wikipedia.org/wiki/Turkish_Identification_Number
     *
     * @param string $identityPrefix
     * @return string Checksum (two digit)
     */
    public static function checksum($identityPrefix)
    {
        if (strlen((string)$identityPrefix) !== 9) {
            throw new InvalidArgumentException('Argument should be an integer and should be 9 digits.');
        }

        $oddSum = 0;
        $evenSum = 0;

        $identityArray = array_map('intval', str_split($identityPrefix)); // Creates array from int
        foreach ($identityArray as $index => $digit) {
            if ($index % 2 == 0) {
                $evenSum += $digit;
            } else {
                $oddSum += $digit;
            }
        }

        $tenthDigit = (7 * $evenSum - $oddSum) % 10;
        $eleventhDigit = ($evenSum + $oddSum + $tenthDigit) % 10;

        return $tenthDigit . $eleventhDigit;
    }

    /**
     * Checks whether an TCNo has a valid checksum
	 * 检查TCNo是否有有效的校验和
     *
     * @param string $tcNo
     * @return boolean
     */
    public static function isValid($tcNo)
    {
        return self::checksum(substr($tcNo, 0, -2)) === substr($tcNo, -2, 2);
    }
}
