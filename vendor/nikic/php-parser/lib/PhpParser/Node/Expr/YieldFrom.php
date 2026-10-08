<?php declare(strict_types=1);

/**
 * PhpParser，节点，表达式，Yield From
 */

namespace PhpParser\Node\Expr;

use PhpParser\Node\Expr;

class YieldFrom extends Expr {
    /** @var Expr Expression to yield from */
    public Expr $expr;

    /**
     * Constructs an "yield from" node.
	 * 构造“从”节点产生的“收益”。
     *
     * @param Expr $expr Expression
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $expr, array $attributes = []) {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }

    public function getSubNodeNames(): array {
        return ['expr'];
    }

    public function getType(): string {
        return 'Expr_YieldFrom';
    }
}
