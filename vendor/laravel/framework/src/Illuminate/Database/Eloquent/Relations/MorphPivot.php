<?php
/**
 * Illuminate，数据库，Eloquent，关系，变形支点
 */

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Database\Eloquent\Builder;

class MorphPivot extends Pivot
{
    /**
     * The type of the polymorphic relation.
	 * 多态关系的类型。
     *
     * Explicitly define this so it's not included in saved attributes.
	 * 显式地定义它，使它不包含在保存的属性中。
     *
     * @var string
     */
    protected $morphType;

    /**
     * The value of the polymorphic relation.
	 * 多态关系的值。
     *
     * Explicitly define this so it's not included in saved attributes.
	 * 显式地定义它，使它不包含在保存的属性中。
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Set the keys for a save update query.
	 * 为保存更新查询设置键
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where($this->morphType, $this->morphClass);

        return parent::setKeysForSaveQuery($query);
    }

    /**
     * Delete the pivot model record from the database.
	 * 从数据库中删除数据透视模型记录
     *
     * @return int
     */
    public function delete()
    {
        $query = $this->getDeleteQuery();

        $query->where($this->morphType, $this->morphClass);

        return $query->delete();
    }

    /**
     * Set the morph type for the pivot.
	 * 设置pivot的变形类型
     *
     * @param  string  $morphType
     * @return $this
     */
    public function setMorphType($morphType)
    {
        $this->morphType = $morphType;

        return $this;
    }

    /**
     * Set the morph class for the pivot.
	 * 为枢轴设置变形类
     *
     * @param  string  $morphClass
     * @return \Illuminate\Database\Eloquent\Relations\MorphPivot
     */
    public function setMorphClass($morphClass)
    {
        $this->morphClass = $morphClass;

        return $this;
    }
}
