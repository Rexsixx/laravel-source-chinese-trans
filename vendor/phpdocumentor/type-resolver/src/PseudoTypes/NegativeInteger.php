<?php
/**
 * phpDocumentor，Reflection，伪类型，负整数
 */

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\PseudoTypes;

use phpDocumentor\Reflection\PseudoType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Value Object representing the type 'int'.
 * 表示“int”的值对象
 *
 * @psalm-immutable
 */
final class NegativeInteger extends Integer implements PseudoType
{
    public function underlyingType(): Type
    {
        return new Integer();
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
	 * 返回该类型的输出输出,因为它将在DocBlock中使用。
     */
    public function __toString(): string
    {
        return 'negative-int';
    }
}
