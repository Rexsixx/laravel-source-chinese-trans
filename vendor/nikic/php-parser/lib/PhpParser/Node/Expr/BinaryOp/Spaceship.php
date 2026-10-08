<?php declare(strict_types=1);

/**
 * PhpParser，节点，表达式，BinaryOp，Spaceship
 */

namespace PhpParser\Node\Expr\BinaryOp;

use PhpParser\Node\Expr\BinaryOp;

class Spaceship extends BinaryOp {
    public function getOperatorSigil(): string {
        return '<=>';
    }

    public function getType(): string {
        return 'Expr_BinaryOp_Spaceship';
    }
}
