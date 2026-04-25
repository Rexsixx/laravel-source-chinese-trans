<?php
/**
 * Illuminate，数据库，Eloquent，关系，支点
 */

namespace Illuminate\Database\Eloquent\Relations;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pivot extends Model
{
    /**
     * The parent model of the relationship.
	 * 关系的父模型
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $pivotParent;

    /**
     * The name of the foreign key column.
	 * 外键列的名称
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The name of the "other key" column.
	 * “其他键”列的名称
     *
     * @var string
     */
    protected $relatedKey;

    /**
     * The attributes that aren't mass assignable.
	 * 不能大规模分配的属性
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Create a new pivot model instance.
	 * 创建一个新的pivot模型实例
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array   $attributes
     * @param  string  $table
     * @param  bool    $exists
     * @return static
     */
    public static function fromAttributes(Model $parent, $attributes, $table, $exists = false)
    {
        $instance = new static;

        // The pivot model is a "dynamic" model since we will set the tables dynamically
        // for the instance. This allows it work for any intermediate tables for the
        // many to many relationship that are defined by this developer's classes.
        $instance->setConnection($parent->getConnectionName())
                ->setTable($table)
                ->forceFill($attributes)
                ->syncOriginal();

        // We store off the parent instance so we will access the timestamp column names
        // for the model, since the pivot model timestamps aren't easily configurable
        // from the developer's point of view. We can use the parents to get these.
        $instance->pivotParent = $parent;

        $instance->exists = $exists;

        $instance->timestamps = $instance->hasTimestampAttributes();

        return $instance;
    }

    /**
     * Create a new pivot model from raw values returned from a query.
	 * 根据查询返回的原始值创建新的数据透视模型
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array   $attributes
     * @param  string  $table
     * @param  bool    $exists
     * @return static
     */
    public static function fromRawAttributes(Model $parent, $attributes, $table, $exists = false)
    {
        $instance = static::fromAttributes($parent, [], $table, $exists);

        $instance->setRawAttributes($attributes, true);

        $instance->timestamps = $instance->hasTimestampAttributes();

        return $instance;
    }

    /**
     * Set the keys for a save update query.
	 * 为保存更新查询设置键
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        if (isset($this->attributes[$this->getKeyName()])) {
            return parent::setKeysForSaveQuery($query);
        }

        $query->where($this->foreignKey, $this->getOriginal(
            $this->foreignKey, $this->getAttribute($this->foreignKey)
        ));

        return $query->where($this->relatedKey, $this->getOriginal(
            $this->relatedKey, $this->getAttribute($this->relatedKey)
        ));
    }

    /**
     * Delete the pivot model record from the database.
	 * 从数据库中删除数据透视模型记录
     *
     * @return int
     */
    public function delete()
    {
        if (isset($this->attributes[$this->getKeyName()])) {
            return parent::delete();
        }

        return $this->getDeleteQuery()->delete();
    }

    /**
     * Get the query builder for a delete operation on the pivot.
	 * 获取对数据透视进行删除操作的查询构建器
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getDeleteQuery()
    {
        return $this->newQuery()->where([
            $this->foreignKey => $this->getOriginal($this->foreignKey, $this->getAttribute($this->foreignKey)),
            $this->relatedKey => $this->getOriginal($this->relatedKey, $this->getAttribute($this->relatedKey)),
        ]);
    }

    /**
     * Get the table associated with the model.
	 * 获取与模型相关联的表
     *
     * @return string
     */
    public function getTable()
    {
        if (! isset($this->table)) {
            $this->setTable(str_replace(
                '\\', '', Str::snake(Str::singular(class_basename($this)))
            ));
        }

        return $this->table;
    }

    /**
     * Get the foreign key column name.
	 * 获取外键列名
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Get the "related key" column name.
	 * 获取“相关键”列名
     *
     * @return string
     */
    public function getRelatedKey()
    {
        return $this->relatedKey;
    }

    /**
     * Get the "related key" column name.
	 * 获取“相关键”列名
     *
     * @return string
     */
    public function getOtherKey()
    {
        return $this->getRelatedKey();
    }

    /**
     * Set the key names for the pivot model instance.
	 * 设置pivot模型实例的键名
     *
     * @param  string  $foreignKey
     * @param  string  $relatedKey
     * @return $this
     */
    public function setPivotKeys($foreignKey, $relatedKey)
    {
        $this->foreignKey = $foreignKey;

        $this->relatedKey = $relatedKey;

        return $this;
    }

    /**
     * Determine if the pivot model has timestamp attributes.
	 * 确定数据透视模型是否具有时间戳属性
     *
     * @return bool
     */
    public function hasTimestampAttributes()
    {
        return array_key_exists($this->getCreatedAtColumn(), $this->attributes);
    }

    /**
     * Get the name of the "created at" column.
	 * 获取“创建位置”列的名称
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return ($this->pivotParent)
                        ? $this->pivotParent->getCreatedAtColumn()
                        : parent::getCreatedAtColumn();
    }

    /**
     * Get the name of the "updated at" column.
	 * 获取“更新时间”列的名称
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return ($this->pivotParent)
                        ? $this->pivotParent->getUpdatedAtColumn()
                        : parent::getUpdatedAtColumn();
    }
}
