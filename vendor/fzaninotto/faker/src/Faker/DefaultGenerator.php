<?php
/**
 * Faker，默认生成器
 */

namespace Faker;

/**
 * This generator returns a default value for all called properties
 * and methods. It works with Faker\Generator\Base->optional().
 * 这个生成器返回所有被称为属性和方法的默认值。
 */
class DefaultGenerator
{
    protected $default;

    public function __construct($default = null)
    {
        $this->default = $default;
    }

    /**
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->default;
    }

    /**
     * @param string $method
     * @param array $attributes
     *
     * @return mixed
     */
    public function __call($method, $attributes)
    {
        return $this->default;
    }
}
