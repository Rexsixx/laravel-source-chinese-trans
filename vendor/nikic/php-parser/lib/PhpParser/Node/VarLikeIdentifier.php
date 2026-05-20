<?php declare(strict_types=1);

/**
 * PhpParser，节点，Var 类标识符
 */

namespace PhpParser\Node;

/**
 * Represents a name that is written in source code with a leading dollar,
 * but is not a proper variable. The leading dollar is not stored as part of the name.
 * 代表一个以领先美元的源代码编写的名称，但不是一个合适的变量。主要的美元不是作为名称的一部分存储。
 *
 * Examples: Names in property declarations are formatted as variables. Names in static property
 * lookups are also formatted as variables.
 */
class VarLikeIdentifier extends Identifier
{
    public function getType() : string {
        return 'VarLikeIdentifier';
    }
}
