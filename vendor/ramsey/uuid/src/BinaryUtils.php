<?php
/**
 * Ramsey，Uuid，二进制 Utils
 */

namespace Ramsey\Uuid;

/**
 * Provides binary math utilities
 * 提供二进制数学实用程序
 */
class BinaryUtils
{
    /**
     * Applies the RFC 4122 variant field to the `clock_seq_hi_and_reserved` field
	 * 将RFC 4122变体字段应用于‘ clock_seq_hi_and_reserved ’字段
     *
     * @param $clockSeqHi
     * @return int The high field of the clock sequence multiplexed with the variant
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.1
     */
    public static function applyVariant($clockSeqHi)
    {
        // Set the variant to RFC 4122
        $clockSeqHi = $clockSeqHi & 0x3f;
        $clockSeqHi |= 0x80;

        return $clockSeqHi;
    }

    /**
     * Applies the RFC 4122 version number to the `time_hi_and_version` field
	 * 将RFC 4122版本号应用到“time_hi_and_version”字段
     *
     * @param string $timeHi
     * @param integer $version
     * @return int The high field of the timestamp multiplexed with the version number
     * @link http://tools.ietf.org/html/rfc4122#section-4.1.3
     */
    public static function applyVersion($timeHi, $version)
    {
        $timeHi = hexdec($timeHi) & 0x0fff;
        $timeHi |= $version << 12;

        return $timeHi;
    }
}
