<?php
/**
 * DeepCopy，过滤器，过滤器
 */

namespace DeepCopy\Filter;

/**
 * Filter to apply to a property while copying an object
 * 在复制对象时,过滤器适用于属性。
 */
interface Filter
{
    /**
     * Applies the filter to the object.
     *
     * @param object   $object
     * @param string   $property
     * @param callable $objectCopier
     */
    public function apply($object, $property, $objectCopier);
}
