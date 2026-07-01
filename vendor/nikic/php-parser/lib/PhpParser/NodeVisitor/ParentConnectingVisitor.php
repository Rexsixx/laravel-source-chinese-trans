<?php declare(strict_types=1);

/**
 * PhpParser，节点访问者，父亲连接访问者
 */

namespace PhpParser\NodeVisitor;

use function array_pop;
use function count;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Visitor that connects a child node to its parent node.
 *
 * On the child node, the parent node can be accessed through
 * <code>$node->getAttribute('parent')</code>.
 */
final class ParentConnectingVisitor extends NodeVisitorAbstract
{
    /**
     * @var Node[]
     */
    private $stack = [];

    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }

    public function enterNode(Node $node)
    {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }

        $this->stack[] = $node;
    }

    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}
