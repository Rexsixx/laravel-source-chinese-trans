<?php declare(strict_types=1);

/**
 * PhpParser，节点，标量，被包围的
 */

namespace PhpParser\Node\Scalar;

use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Encapsed extends Scalar
{
    /** @var Expr[] list of string parts */
    public $parts;

    /**
     * Constructs an encapsed string node.
	 * 构造一个被包的字符串节点
     *
     * @param Expr[] $parts      Encaps list
     * @param array  $attributes Additional attributes
     */
    public function __construct(array $parts, array $attributes = []) {
        $this->attributes = $attributes;
        $this->parts = $parts;
    }

    public function getSubNodeNames() : array {
        return ['parts'];
    }
    
    public function getType() : string {
        return 'Scalar_Encapsed';
    }
}
