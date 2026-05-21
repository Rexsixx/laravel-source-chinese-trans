<?php declare(strict_types=1);

/**
 * PhpParser，构建器，特征使用适应
 */

namespace PhpParser\Builder;

use PhpParser\Builder;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class TraitUseAdaptation implements Builder
{
    const TYPE_UNDEFINED  = 0;
    const TYPE_ALIAS      = 1;
    const TYPE_PRECEDENCE = 2;

    /** @var int Type of building adaptation */
    protected $type;

    protected $trait;
    protected $method;

    protected $modifier = null;
    protected $alias = null;

    protected $insteadof = [];

    /**
     * Creates a trait use adaptation builder.
	 * 创建一个特性使用适应性构建器
     *
     * @param Node\Name|string|null  $trait  Name of adaptated trait
     * @param Node\Identifier|string $method Name of adaptated method
     */
    public function __construct($trait, $method) {
        $this->type = self::TYPE_UNDEFINED;

        $this->trait = is_null($trait)? null: BuilderHelpers::normalizeName($trait);
        $this->method = BuilderHelpers::normalizeIdentifier($method);
    }

    /**
     * Sets alias of method.
	 * 设置方法的别名
     *
     * @param Node\Identifier|string $alias Alias for adaptated method
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function as($alias) {
        if ($this->type === self::TYPE_UNDEFINED) {
            $this->type = self::TYPE_ALIAS;
        }

        if ($this->type !== self::TYPE_ALIAS) {
            throw new \LogicException('Cannot set alias for not alias adaptation buider');
        }

        $this->alias = $alias;
        return $this;
    }

    /**
     * Sets adaptated method public.
	 * 设置可修改的方法公开
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePublic() {
        $this->setModifier(Stmt\Class_::MODIFIER_PUBLIC);
        return $this;
    }

    /**
     * Sets adaptated method protected.
	 * 设置可修改的方法保护
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makeProtected() {
        $this->setModifier(Stmt\Class_::MODIFIER_PROTECTED);
        return $this;
    }

    /**
     * Sets adaptated method private.
	 * 设置可修改的方法私有
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function makePrivate() {
        $this->setModifier(Stmt\Class_::MODIFIER_PRIVATE);
        return $this;
    }

    /**
     * Adds overwritten traits.
	 * 增加超写特征
     *
     * @param Node\Name|string ...$traits Traits for overwrite
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function insteadof(...$traits) {
        if ($this->type === self::TYPE_UNDEFINED) {
            if (is_null($this->trait)) {
                throw new \LogicException('Precedence adaptation must have trait');
            }

            $this->type = self::TYPE_PRECEDENCE;
        }

        if ($this->type !== self::TYPE_PRECEDENCE) {
            throw new \LogicException('Cannot add overwritten traits for not precedence adaptation buider');
        }

        foreach ($traits as $trait) {
            $this->insteadof[] = BuilderHelpers::normalizeName($trait);
        }

        return $this;
    }

    protected function setModifier(int $modifier) {
        if ($this->type === self::TYPE_UNDEFINED) {
            $this->type = self::TYPE_ALIAS;
        }

        if ($this->type !== self::TYPE_ALIAS) {
            throw new \LogicException('Cannot set access modifier for not alias adaptation buider');
        }

        if (is_null($this->modifier)) {
            $this->modifier = $modifier;
        } else {
            throw new \LogicException('Multiple access type modifiers are not allowed');
        }
    }

    /**
     * Returns the built node.
	 * 返回构建节点
     *
     * @return Node The built node
     */
    public function getNode() : Node {
        switch ($this->type) {
            case self::TYPE_ALIAS:
                return new Stmt\TraitUseAdaptation\Alias($this->trait, $this->method, $this->modifier, $this->alias);
            case self::TYPE_PRECEDENCE:
                return new Stmt\TraitUseAdaptation\Precedence($this->trait, $this->method, $this->insteadof);
            default:
                throw new \LogicException('Type of adaptation is not defined');
        }
    }
}
