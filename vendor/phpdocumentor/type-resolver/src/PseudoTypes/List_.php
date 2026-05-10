<?php
/**
 * phpDocumentor，Reflection，伪类型，List
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
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Value Object representing the type 'list'.
 * 表示“列表”的值对象
 *
 * @psalm-immutable
 */
final class List_ extends Array_ implements PseudoType
{
    public function underlyingType(): Type
    {
        return new Array_();
    }

    public function __construct(?Type $valueType = null)
    {
        parent::__construct($valueType, new Integer());
    }

    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
	 * 返回该类型的输出输出,因为它将在DocBlock中使用。
     */
    public function __toString(): string
    {
        if ($this->valueType instanceof Mixed_) {
            return 'list';
        }

        return 'list<' . $this->valueType . '>';
    }
}
