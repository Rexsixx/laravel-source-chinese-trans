<?php declare(strict_types=1);

/**
 * PhpParser，节点，复合类型
 */

namespace PhpParser\Node;

use PhpParser\NodeAbstract;

/**
 * This is a base class for complex types, including nullable types and union types.
 * 这是复杂类型的基类,包括可用类型和union类型。
 *
 * It does not provide any shared behavior and exists only for type-checking purposes.
 * 它没有提供任何共享行为,只存在用于类型检查的目的。
 */
abstract class ComplexType extends NodeAbstract {
}
