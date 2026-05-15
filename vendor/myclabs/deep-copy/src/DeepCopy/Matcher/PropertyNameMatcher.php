<?php
/**
 * DeepCopy，匹配器，属性名称匹配器
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
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $property == $this->property;
    }
}
