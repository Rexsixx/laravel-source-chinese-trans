<?php
/**
 * Illuminate，数据库，Eloquent，关系，转变为
 */

namespace Illuminate\Database\Eloquent\Relations;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class MorphTo extends BelongsTo
{
    /**
     * The type of the polymorphic relation.
	 * 多态关系的类型
     *
     * @var string
     */
    protected $morphType;

    /**
     * The models whose relations are being eager loaded.
	 * 其关系被热切加载的模型
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    protected $models;

    /**
     * All of the models keyed by ID.
	 * 所有以ID为键的模型
     *
     * @var array
     */
    protected $dictionary = [];

    /**
     * A buffer of dynamic calls to query macros.
	 * 用于动态调用查询宏的缓冲区
     *
     * @var array
     */
    protected $macroBuffer = [];

    /**
     * Create a new morph to relationship instance.
	 * 创建关系实例的新变形
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $type
     * @param  string  $relation
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation)
    {
        $this->morphType = $type;

        parent::__construct($query, $parent, $foreignKey, $ownerKey, $relation);
    }

    /**
     * Set the constraints for an eager load of the relation.
	 * 为关系的即时加载设置约束
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        $this->buildDictionary($this->models = Collection::make($models));
    }

    /**
     * Build a dictionary with the models.
	 * 用这些模型构建一个字典
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    protected function buildDictionary(Collection $models)
    {
        foreach ($models as $model) {
            if ($model->{$this->morphType}) {
                $this->dictionary[$model->{$this->morphType}][$model->{$this->foreignKey}][] = $model;
            }
        }
    }

    /**
     * Get the results of the relationship.
	 * 得到关系的结果
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->ownerKey ? $this->query->first() : null;
    }

    /**
     * Get the results of the relationship.
	 * 得到关系的结果。
     *
     * Called via eager load method of Eloquent query builder.
	 * 通过Eloquent查询生成器的急切加载方法调用。
     *
     * @return mixed
     */
    public function getEager()
    {
        foreach (array_keys($this->dictionary) as $type) {
            $this->matchToMorphParents($type, $this->getResultsByType($type));
        }

        return $this->models;
    }

    /**
     * Get all of the relation results for a type.
	 * 获取一个类型的所有关系结果
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getResultsByType($type)
    {
        $instance = $this->createModelByType($type);

        $query = $this->replayMacros($instance->newQuery())
                            ->mergeConstraintsFrom($this->getQuery())
                            ->with($this->getQuery()->getEagerLoads());

        return $query->whereIn(
            $instance->getTable().'.'.$instance->getKeyName(), $this->gatherKeysByType($type)
        )->get();
    }

    /**
     * Gather all of the foreign keys for a given type.
	 * 收集给定类型的所有外键
     *
     * @param  string  $type
     * @return array
     */
    protected function gatherKeysByType($type)
    {
        return collect($this->dictionary[$type])->map(function ($models) {
            return head($models)->{$this->foreignKey};
        })->values()->unique()->all();
    }

    /**
     * Create a new model instance by type.
	 * 按类型创建一个新的模型实例
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModelByType($type)
    {
        $class = Model::getActualClassNameForMorph($type);

        return new $class;
    }

    /**
     * Match the eagerly loaded results to their parents.
	 * 将急切加载的结果与他们的父母匹配
     *
     * @param  array   $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, Collection $results, $relation)
    {
        return $models;
    }

    /**
     * Match the results for a given type to their parents.
	 * 将给定类型的结果与其父类型进行匹配
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @return void
     */
    protected function matchToMorphParents($type, Collection $results)
    {
        foreach ($results as $result) {
            if (isset($this->dictionary[$type][$result->getKey()])) {
                foreach ($this->dictionary[$type][$result->getKey()] as $model) {
                    $model->setRelation($this->relation, $result);
                }
            }
        }
    }

    /**
     * Associate the model instance to the given parent.
	 * 将模型实例关联到给定的父实例
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function associate($model)
    {
        $this->parent->setAttribute(
            $this->foreignKey, $model instanceof Model ? $model->getKey() : null
        );

        $this->parent->setAttribute(
            $this->morphType, $model instanceof Model ? $model->getMorphClass() : null
        );

        return $this->parent->setRelation($this->relation, $model);
    }

    /**
     * Dissociate previously associated model from the given parent.
	 * 将先前关联的模型与给定的父模型分离
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dissociate()
    {
        $this->parent->setAttribute($this->foreignKey, null);

        $this->parent->setAttribute($this->morphType, null);

        return $this->parent->setRelation($this->relation, null);
    }

    /**
     * Get the foreign key "type" name.
	 * 获取外键“类型”名称
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the dictionary used by the relationship.
	 * 获取关系使用的字典
     *
     * @return array
     */
    public function getDictionary()
    {
        return $this->dictionary;
    }

    /**
     * Replay stored macro calls on the actual related instance.
	 * 在实际相关实例上重播存储的宏调用
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function replayMacros(Builder $query)
    {
        foreach ($this->macroBuffer as $macro) {
            $query->{$macro['method']}(...$macro['parameters']);
        }

        return $query;
    }

    /**
     * Handle dynamic method calls to the relationship.
	 * 处理对关系的动态方法调用
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        try {
            return parent::__call($method, $parameters);
        }

        // If we tried to call a method that does not exist on the parent Builder instance,
        // we'll assume that we want to call a query macro (e.g. withTrashed) that only
        // exists on related models. We will just store the call and replay it later.
        catch (BadMethodCallException $e) {
            $this->macroBuffer[] = compact('method', 'parameters');

            return $this;
        }
    }
}
