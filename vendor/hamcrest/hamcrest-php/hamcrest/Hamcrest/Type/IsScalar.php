<?php
/**
 * Hamcrest，类型，是否标量
 */

namespace Hamcrest\Type;

/*
 Copyright (c) 2010 hamcrest.org
 */
use Hamcrest\Core\IsTypeOf;

/**
 * Tests whether the value is a scalar (boolean, integer, double, or string).
 * 测试值是否为标量(布尔、整数、double或string)。
 */
class IsScalar extends IsTypeOf
{

    public function __construct()
    {
        parent::__construct('scalar');
    }

    public function matches($item)
    {
        return is_scalar($item);
    }

    /**
     * Is the value a scalar (boolean, integer, double, or string)?
     *
     * @factory
     */
    public static function scalarValue()
    {
        return new self;
    }
}
