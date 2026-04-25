<?php
/**
 * Illuminate，数据库，Eloquent，工厂生成器
 */

namespace Illuminate\Database\Eloquent;

use Faker\Generator as Faker;
use InvalidArgumentException;
use Illuminate\Support\Traits\Macroable;

class FactoryBuilder
{
    use Macroable;

    /**
     * The model definitions in the container.
	 * 容器中的模型定义
     *
     * @var array
     */
    protected $definitions;

    /**
     * The model being built.
	 * 正在构建的模型
     *
     * @var string
     */
    protected $class;

    /**
     * The name of the model being built.
	 * 正在构建的模型的名称
     *
     * @var string
     */
    protected $name = 'default';

    /**
     * The database connection on which the model instance should be persisted.
	 * 应该在其上持久化模型实例的数据库连接
     *
     * @var string
     */
    protected $connection;

    /**
     * The model states.
	 * 大成模型
     *
     * @var array
     */
    protected $states;

    /**
     * The states to apply.
	 * 要申请的状态
     *
     * @var array
     */
    protected $activeStates = [];

    /**
     * The Faker instance for the builder.
	 * 生成器的Faker实例
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * The number of models to build.
	 * 要构建的模型的数量
     *
     * @var int|null
     */
    protected $amount = null;

    /**
     * Create an new builder instance.
	 * 创建一个新的构建器实例
     *
     * @param  string  $class
     * @param  string  $name
     * @param  array  $definitions
     * @param  array  $states
     * @param  \Faker\Generator  $faker
     * @return void
     */
    public function __construct($class, $name, array $definitions, array $states, Faker $faker)
    {
        $this->name = $name;
        $this->class = $class;
        $this->faker = $faker;
        $this->states = $states;
        $this->definitions = $definitions;
    }

    /**
     * Set the amount of models you wish to create / make.
	 * 设置您希望创建/制作的模型数量
     *
     * @param  int  $amount
     * @return $this
     */
    public function times($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the states to be applied to the model.
	 * 设置要应用于模型的状态
     *
     * @param  array|mixed  $states
     * @return $this
     */
    public function states($states)
    {
        $this->activeStates = is_array($states) ? $states : func_get_args();

        return $this;
    }

    /**
     * Set the database connection on which the model instance should be persisted.
	 * 设置应该持久化模型实例的数据库连接
     *
     * @param  string  $name
     * @return $this
     */
    public function connection($name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * Create a model and persist it in the database if requested.
	 * 如果需要，创建一个模型并将其持久化到数据库中。
     *
     * @param  array  $attributes
     * @return \Closure
     */
    public function lazy(array $attributes = [])
    {
        return function () use ($attributes) {
            return $this->create($attributes);
        };
    }

    /**
     * Create a collection of models and persist them to the database.
	 * 创建一个模型集合，并将它们持久化到数据库中。
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function create(array $attributes = [])
    {
        $results = $this->make($attributes);

        if ($results instanceof Model) {
            $this->store(collect([$results]));
        } else {
            $this->store($results);
        }

        return $results;
    }

    /**
     * Set the connection name on the results and store them.
	 * 在结果上设置连接名称并存储它们
     *
     * @param  \Illuminate\Support\Collection  $results
     * @return void
     */
    protected function store($results)
    {
        $results->each(function ($model) {
            if (! isset($this->connection)) {
                $model->setConnection($model->newQueryWithoutScopes()->getConnection()->getName());
            }

            $model->save();
        });
    }

    /**
     * Create a collection of models.
	 * 创建一个模型集合
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function make(array $attributes = [])
    {
        if ($this->amount === null) {
            return $this->makeInstance($attributes);
        }

        if ($this->amount < 1) {
            return (new $this->class)->newCollection();
        }

        return (new $this->class)->newCollection(array_map(function () use ($attributes) {
            return $this->makeInstance($attributes);
        }, range(1, $this->amount)));
    }

    /**
     * Create an array of raw attribute arrays.
	 * 创建一个原始属性数组数组
     *
     * @param  array  $attributes
     * @return mixed
     */
    public function raw(array $attributes = [])
    {
        if ($this->amount === null) {
            return $this->getRawAttributes($attributes);
        }

        if ($this->amount < 1) {
            return [];
        }

        return array_map(function () use ($attributes) {
            return $this->getRawAttributes($attributes);
        }, range(1, $this->amount));
    }

    /**
     * Get a raw attributes array for the model.
	 * 获取模型的原始属性数组
     *
     * @param  array  $attributes
     * @return mixed
     */
    protected function getRawAttributes(array $attributes = [])
    {
        $definition = call_user_func(
            $this->definitions[$this->class][$this->name],
            $this->faker, $attributes
        );

        return $this->expandAttributes(
            array_merge($this->applyStates($definition, $attributes), $attributes)
        );
    }

    /**
     * Make an instance of the model with the given attributes.
	 * 创建具有给定属性的模型实例
     *
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \InvalidArgumentException
     */
    protected function makeInstance(array $attributes = [])
    {
        return Model::unguarded(function () use ($attributes) {
            if (! isset($this->definitions[$this->class][$this->name])) {
                throw new InvalidArgumentException("Unable to locate factory with name [{$this->name}] [{$this->class}].");
            }

            $instance = new $this->class(
                $this->getRawAttributes($attributes)
            );

            if (isset($this->connection)) {
                $instance->setConnection($this->connection);
            }

            return $instance;
        });
    }

    /**
     * Apply the active states to the model definition array.
	 * 将活动状态应用于模型定义数组
     *
     * @param  array  $definition
     * @param  array  $attributes
     * @return array
     */
    protected function applyStates(array $definition, array $attributes = [])
    {
        foreach ($this->activeStates as $state) {
            if (! isset($this->states[$this->class][$state])) {
                throw new InvalidArgumentException("Unable to locate [{$state}] state for [{$this->class}].");
            }

            $definition = array_merge(
                $definition,
                $this->stateAttributes($state, $attributes)
            );
        }

        return $definition;
    }

    /**
     * Get the state attributes.
	 * 获取状态属性
     *
     * @param  string  $state
     * @param  array  $attributes
     * @return array
     */
    protected function stateAttributes($state, array $attributes)
    {
        $stateAttributes = $this->states[$this->class][$state];

        if (! is_callable($stateAttributes)) {
            return $stateAttributes;
        }

        return call_user_func(
            $stateAttributes,
            $this->faker, $attributes
        );
    }

    /**
     * Expand all attributes to their underlying values.
	 * 将所有属性展开为其基础值
     *
     * @param  array  $attributes
     * @return array
     */
    protected function expandAttributes(array $attributes)
    {
        foreach ($attributes as &$attribute) {
            if (is_callable($attribute) && ! is_string($attribute)) {
                $attribute = $attribute($attributes);
            }

            if ($attribute instanceof static) {
                $attribute = $attribute->create()->getKey();
            }

            if ($attribute instanceof Model) {
                $attribute = $attribute->getKey();
            }
        }

        return $attributes;
    }
}
