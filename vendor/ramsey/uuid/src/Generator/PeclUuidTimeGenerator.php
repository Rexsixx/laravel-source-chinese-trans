<?php
/**
 * Ramsey，Uuid，生成器，Pecl Uuid 时间生成器
 */

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Generator;

/**
 * PeclUuidTimeGenerator provides functionality to generate strings of binary
 * data for version 1 UUIDs using the PECL UUID PHP extension
 * PeclUuidTimeGenerator提供了使用PECL UUID PHP扩展的版本1 UUIDs的二进制数据字符串的功能。
 *
 * @link https://pecl.php.net/package/uuid
 */
class PeclUuidTimeGenerator implements TimeGeneratorInterface
{
    /**
     * Generate a version 1 UUID using the PECL UUID extension
	 * 使用PECL UUID扩展生成版本1 UUID
     *
     * @param int|string $node Not used in this context
     * @param int $clockSeq Not used in this context
     * @return string A binary string
     */
    public function generate($node = null, $clockSeq = null)
    {
        $uuid = uuid_create(UUID_TYPE_TIME);

        return uuid_parse($uuid);
    }
}
