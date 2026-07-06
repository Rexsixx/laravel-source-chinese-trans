<?php
/**
 * Illuminate，队列，序列化模型
 */

namespace Illuminate\Queue;

use ReflectionClass;
use ReflectionProperty;

trait SerializesModels
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * Prepare the instance for serialization.
	 * 为序列化准备实例
     *
     * @return array
     */
    public function __sleep()
    {
        $properties = (new ReflectionClass($this))->getProperties();

        foreach ($properties as $property) {
            $property->setValue($this, $this->getSerializedPropertyValue(
                $this->getPropertyValue($property)
            ));
        }

        return array_values(array_filter(array_map(function ($p) {
            return $p->isStatic() ? null : $p->getName();
        }, $properties)));
    }

    /**
     * Restore the model after serialization.
	 * 序列化后恢复模型
     *
     * @return void
     */
    public function __wakeup()
    {
        foreach ((new ReflectionClass($this))->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $property->setValue($this, $this->getRestoredPropertyValue(
                $this->getPropertyValue($property)
            ));
        }
    }

    /**
     * Get the property value for the given property.
	 * 获取给定属性的属性值
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property)
    {
        $property->setAccessible(true);

        return $property->getValue($this);
    }
}
