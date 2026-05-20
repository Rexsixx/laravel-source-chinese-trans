<?php declare(strict_types=1);

/**
 * PhpParser，节点交叉界面
 */

namespace PhpParser;

interface NodeTraverserInterface
{
    /**
     * Adds a visitor.
	 * 添加一个访问者
     *
     * @param NodeVisitor $visitor Visitor to add
     */
    public function addVisitor(NodeVisitor $visitor);

    /**
     * Removes an added visitor.
	 * 删除添加的访问者
     *
     * @param NodeVisitor $visitor
     */
    public function removeVisitor(NodeVisitor $visitor);

    /**
     * Traverses an array of nodes using the registered visitors.
	 * 使用注册访问者遍历一系列节点
     *
     * @param Node[] $nodes Array of nodes
     *
     * @return Node[] Traversed array of nodes
     */
    public function traverse(array $nodes) : array;
}
