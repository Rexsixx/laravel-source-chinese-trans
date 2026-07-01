<?php
/**
 * 深拷贝，匹配程序，所有权匹配程序
 */

namespace DeepCopy\Matcher;

/**
 * @final
 */
class PropertyMatcher implements Matcher
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $property;

    /**
     * @param string $class    Class name
     * @param string $property Property name
     */
    public function __construct($class, $property)
    {
        $this->class = $class;
        $this->property = $property;
    }

    /**
     * Matches a specific property of a specific class.
	 * 匹配特定类的特定属性
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return ($object instanceof $this->class) && $property == $this->property;
    }
}
