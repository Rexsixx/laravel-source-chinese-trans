<?php declare(strict_types=1);

/**
 * PhpParser，节点访问者，寻找访客
 */

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * This visitor can be used to find and collect all nodes satisfying some criterion determined by
 * a filter callback.
 * 这个访问者可以用来查找和收集所有节点,以满足一个过滤器回调决定的标准。
 */
class FindingVisitor extends NodeVisitorAbstract
{
    /** @var callable Filter callback */
    protected $filterCallback;
    /** @var Node[] Found nodes */
    protected $foundNodes;

    public function __construct(callable $filterCallback) {
        $this->filterCallback = $filterCallback;
    }

    /**
     * Get found nodes satisfying the filter callback.
	 * 找到满足过滤器回调的节点。
     *
     * Nodes are returned in pre-order.
     *
     * @return Node[] Found nodes
     */
    public function getFoundNodes() : array {
        return $this->foundNodes;
    }

    public function beforeTraverse(array $nodes) {
        $this->foundNodes = [];

        return null;
    }

    public function enterNode(Node $node) {
        $filterCallback = $this->filterCallback;
        if ($filterCallback($node)) {
            $this->foundNodes[] = $node;
        }

        return null;
    }
}
