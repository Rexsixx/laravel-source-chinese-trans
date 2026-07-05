<?php
/**
 * PhpParser，匹配程序，所有权名称匹配程序
 */

namespace DeepCopy\Matcher;

/**
 * @final
 */
class PropertyNameMatcher implements Matcher
{
    /**
     * @var string
     */
    private $property;

    /**
     * @param string $property Property name
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * Matches a property by its name.
	 * 以其名称匹配属性
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $property == $this->property;
    }
}
