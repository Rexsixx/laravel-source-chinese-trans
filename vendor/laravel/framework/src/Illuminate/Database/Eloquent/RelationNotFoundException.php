<?php
/**
 * Illuminate，数据库，Eloquent，关联未发现异常
 */

namespace Illuminate\Database\Eloquent;

use RuntimeException;

class RelationNotFoundException extends RuntimeException
{
    /**
     * The name of the affected Eloquent model.
	 * 受影响的Eloquent模型的名称
     *
     * @var string
     */
    public $model;

    /**
     * The name of the relation.
	 * 关系的名称
     *
     * @var string
     */
    public $relation;

    /**
     * Create a new exception instance.
	 * 创建一个新的异常实例
     *
     * @param  mixed  $model
     * @param  string  $relation
     * @return static
     */
    public static function make($model, $relation)
    {
        $class = get_class($model);

        $instance = new static("Call to undefined relationship [{$relation}] on model [{$class}].");

        $instance->model = $model;
        $instance->relation = $relation;

        return $instance;
    }
}
