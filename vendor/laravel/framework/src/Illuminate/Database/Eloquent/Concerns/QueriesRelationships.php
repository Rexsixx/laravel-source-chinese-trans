<?php
/**
 * Illuminate，数据库，Eloquent，问题，查询关系
 */

namespace Illuminate\Database\Eloquent\Concerns;

use Closure;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait QueriesRelationships
{
    /**
     * Add a relationship count / exists condition to the query.
	 * 向查询添加关系计数/存在条件
     *
     * @param  string|\Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @param  string  $operator
     * @param  int     $count
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function has($relation, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null)
    {
        if (is_string($relation)) {
            if (strpos($relation, '.') !== false) {
                return $this->hasNested($relation, $operator, $count, $boolean, $callback);
            }

            $relation = $this->getRelationWithoutConstraints($relation);
        }

        if ($relation instanceof MorphTo) {
            throw new RuntimeException('Please use whereHasMorph() for MorphTo relationships.');
        }

        // If we only need to check for the existence of the relation, then we can optimize
        // the subquery to only run a "where exists" clause instead of this full "count"
        // clause. This will make these queries run much faster compared with a count.
		// 如果我们只是需要检查这种关系是否存在，那么就可以对子查询进行优化，
		// 使其仅运行“存在”条件的子句，而不再使用这个完整的“计数”子句。
        $method = $this->canUseExistsForExistenceCheck($operator, $count)
                        ? 'getRelationExistenceQuery'
                        : 'getRelationExistenceCountQuery';

        $hasQuery = $relation->{$method}(
            $relation->getRelated()->newQueryWithoutRelationships(), $this
        );

        // Next we will call any given callback as an "anonymous" scope so they can get the
        // proper logical grouping of the where clauses if needed by this Eloquent query
        // builder. Then, we will be ready to finalize and return this query instance.
		// 接下来，我们将将任何给定的回调函数视为“匿名”作用域，以便在需要时，
		// 它们能够根据此 Eloquent 查询构建器获取各“where”子句的正确逻辑分组。
        if ($callback) {
            $hasQuery->callScope($callback);
        }

        return $this->addHasWhere(
            $hasQuery, $relation, $operator, $count, $boolean
        );
    }

    /**
     * Add nested relationship count / exists conditions to the query.
	 * 向查询添加嵌套关系count / exists条件。
     *
     * Sets up recursive call to whereHas until we finish the nested relation.
	 * 设置对whereHas的递归调用，直到完成嵌套关系。
     *
     * @param  string  $relations
     * @param  string  $operator
     * @param  int     $count
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    protected function hasNested($relations, $operator = '>=', $count = 1, $boolean = 'and', $callback = null)
    {
        $relations = explode('.', $relations);

        $doesntHave = $operator === '<' && $count === 1;

        if ($doesntHave) {
            $operator = '>=';
            $count = 1;
        }

        $closure = function ($q) use (&$closure, &$relations, $operator, $count, $callback) {
            // In order to nest "has", we need to add count relation constraints on the
            // callback Closure. We'll do this by simply passing the Closure its own
            // reference to itself so it calls itself recursively on each segment.
			// 为了实现“has”这种关系的嵌套，我们需要在回调闭包上添加计数关系约束。
			// 我们可以通过让闭包自身获得对其自身的引用来实现这一点，这样它就能在每个段落中递归地调用自身。
            count($relations) > 1
                ? $q->whereHas(array_shift($relations), $closure)
                : $q->has(array_shift($relations), $operator, $count, 'and', $callback);
        };

        return $this->has(array_shift($relations), $doesntHave ? '<' : '>=', 1, $boolean, $closure);
    }

    /**
     * Add a relationship count / exists condition to the query with an "or".
	 * 使用“或”向查询添加关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string  $operator
     * @param  int     $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orHas($relation, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'or');
    }

    /**
     * Add a relationship count / exists condition to the query.
	 * 向查询添加关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function doesntHave($relation, $boolean = 'and', Closure $callback = null)
    {
        return $this->has($relation, '<', 1, $boolean, $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with an "or".
	 * 使用“或”向查询添加关系计数/存在条件
     *
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orDoesntHave($relation)
    {
        return $this->doesntHave($relation, 'or');
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
	 * 使用where子句向查询添加关系count / exists条件
     *
     * @param  string  $relation
     * @param  \Closure|null  $callback
     * @param  string  $operator
     * @param  int     $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function whereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'and', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses and an "or".
	 * 使用where子句和“或”向查询添加关系count / exists条件
     *
     * @param  string    $relation
     * @param  \Closure  $callback
     * @param  string    $operator
     * @param  int       $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereHas($relation, Closure $callback = null, $operator = '>=', $count = 1)
    {
        return $this->has($relation, $operator, $count, 'or', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses.
	 * 使用where子句向查询添加关系count / exists条件
     *
     * @param  string  $relation
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function whereDoesntHave($relation, Closure $callback = null)
    {
        return $this->doesntHave($relation, 'and', $callback);
    }

    /**
     * Add a relationship count / exists condition to the query with where clauses and an "or".
	 * 使用where子句和“or”向查询添加关系count / exists条件
     *
     * @param  string    $relation
     * @param  \Closure  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereDoesntHave($relation, Closure $callback = null)
    {
        return $this->doesntHave($relation, 'or', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query.
	 * 向查询添加一个多态关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  string  $operator
     * @param  int  $count
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function hasMorph($relation, $types, $operator = '>=', $count = 1, $boolean = 'and', Closure $callback = null)
    {
        $relation = $this->getRelationWithoutConstraints($relation);

        $types = (array) $types;

        if ($types === ['*']) {
            $types = $this->model->newModelQuery()->distinct()->pluck($relation->getMorphType())->all();

            foreach ($types as &$type) {
                $type = Relation::getMorphedModel($type) ?? $type;
            }
        }

        return $this->where(function ($query) use ($relation, $callback, $operator, $count, $types) {
            foreach ($types as $type) {
                $query->orWhere(function ($query) use ($relation, $callback, $operator, $count, $type) {
                    $belongsTo = $this->getBelongsToRelation($relation, $type);

                    if ($callback) {
                        $callback = function ($query) use ($callback, $type) {
                            return $callback($query, $type);
                        };
                    }

                    $query->where($relation->getMorphType(), '=', (new $type)->getMorphClass())
                        ->whereHas($belongsTo, $callback, $operator, $count);
                });
            }
        }, null, null, $boolean);
    }

    /**
     * Get the BelongsTo relationship for a single polymorphic type.
	 * 获取单个多态类型的BelongsTo关系
     *
     * @param  \Illuminate\Database\Eloquent\Relations\MorphTo  $relation
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function getBelongsToRelation(MorphTo $relation, $type)
    {
        $belongsTo = Relation::noConstraints(function () use ($relation, $type) {
            return $this->model->belongsTo(
                $type,
                $relation->getForeignKeyName(),
                $relation->getOwnerKeyName()
            );
        });

        $belongsTo->getQuery()->mergeConstraintsFrom($relation->getQuery());

        return $belongsTo;
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with an "or".
	 * 使用“或”向查询添加多态关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  string  $operator
     * @param  int  $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orHasMorph($relation, $types, $operator = '>=', $count = 1)
    {
        return $this->hasMorph($relation, $types, $operator, $count, 'or');
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query.
	 * 向查询添加一个多态关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  string  $boolean
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function doesntHaveMorph($relation, $types, $boolean = 'and', Closure $callback = null)
    {
        return $this->hasMorph($relation, $types, '<', 1, $boolean, $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with an "or".
	 * 使用“或”向查询添加多态关系计数/存在条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orDoesntHaveMorph($relation, $types)
    {
        return $this->doesntHaveMorph($relation, $types, 'or');
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses.
	 * 使用where子句向查询添加一个多态关系count / exists条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  \Closure|null  $callback
     * @param  string  $operator
     * @param  int  $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function whereHasMorph($relation, $types, Closure $callback = null, $operator = '>=', $count = 1)
    {
        return $this->hasMorph($relation, $types, $operator, $count, 'and', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
	 * 使用where子句和“或”向查询添加一个多态关系count / exists条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  \Closure  $callback
     * @param  string  $operator
     * @param  int  $count
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereHasMorph($relation, $types, Closure $callback = null, $operator = '>=', $count = 1)
    {
        return $this->hasMorph($relation, $types, $operator, $count, 'or', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses.
	 * 使用where子句向查询添加一个多态关系count / exists条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  \Closure|null  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function whereDoesntHaveMorph($relation, $types, Closure $callback = null)
    {
        return $this->doesntHaveMorph($relation, $types, 'and', $callback);
    }

    /**
     * Add a polymorphic relationship count / exists condition to the query with where clauses and an "or".
	 * 使用where子句和“或”向查询添加一个多态关系count / exists条件
     *
     * @param  string  $relation
     * @param  string|array  $types
     * @param  \Closure  $callback
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function orWhereDoesntHaveMorph($relation, $types, Closure $callback = null)
    {
        return $this->doesntHaveMorph($relation, $types, 'or', $callback);
    }

    /**
     * Add subselect queries to count the relations.
	 * 添加子选择查询来统计关系
     *
     * @param  mixed  $relations
     * @return $this
     */
    public function withCount($relations)
    {
        if (empty($relations)) {
            return $this;
        }

        if (is_null($this->query->columns)) {
            $this->query->select([$this->query->from.'.*']);
        }

        $relations = is_array($relations) ? $relations : func_get_args();

        foreach ($this->parseWithRelations($relations) as $name => $constraints) {
            // First we will determine if the name has been aliased using an "as" clause on the name
            // and if it has we will extract the actual relationship name and the desired name of
            // the resulting column. This allows multiple counts on the same relationship name.
			// 首先，我们将通过检查名称上是否存在“as”子句来确定该名称是否已被别名化。
			// 如果存在此类别名，则我们将提取出实际的关系名称以及所生成列的期望名称。
            $segments = explode(' ', $name);

            unset($alias);

            if (count($segments) === 3 && Str::lower($segments[1]) === 'as') {
                [$name, $alias] = [$segments[0], $segments[2]];
            }

            $relation = $this->getRelationWithoutConstraints($name);

            // Here we will get the relationship count query and prepare to add it to the main query
            // as a sub-select. First, we'll get the "has" query and use that to get the relation
            // count query. We will normalize the relation name then append _count as the name.
			// 接下来我们将编写关系计数查询，并准备将其添加到主查询中作为子查询。
			// 首先，我们将获取“has”查询，并利用该查询来获取关系计数查询。我们会对关系名称进行规范化处理，然后在名称后添加“_count”。
            $query = $relation->getRelationExistenceCountQuery(
                $relation->getRelated()->newQuery(), $this
            );

            $query->callScope($constraints);

            $query = $query->mergeConstraintsFrom($relation->getQuery())->toBase();

            if (count($query->columns) > 1) {
                $query->columns = [$query->columns[0]];
            }

            // Finally we will add the proper result column alias to the query and run the subselect
            // statement against the query builder. Then we will return the builder instance back
            // to the developer for further constraint chaining that needs to take place on it.
			// 最后，我们将为查询添加正确的结果列别名，并在查询构建器中运行子查询语句。
			// 然后，我们将把构建器实例返回给开发人员，以便对其进行后续的约束链操作。
            $column = $alias ?? Str::snake($name.'_count');

            $this->selectSub($query, $column);
        }

        return $this;
    }

    /**
     * Add the "has" condition where clause to the query.
	 * 向查询中添加“has”条件where子句
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $hasQuery
     * @param  \Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @param  string  $operator
     * @param  int  $count
     * @param  string  $boolean
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    protected function addHasWhere(Builder $hasQuery, Relation $relation, $operator, $count, $boolean)
    {
        $hasQuery->mergeConstraintsFrom($relation->getQuery());

        return $this->canUseExistsForExistenceCheck($operator, $count)
                ? $this->addWhereExistsQuery($hasQuery->toBase(), $boolean, $operator === '<' && $count === 1)
                : $this->addWhereCountQuery($hasQuery->toBase(), $operator, $count, $boolean);
    }

    /**
     * Merge the where constraints from another query to the current query.
	 * 将来自另一个查询的where约束合并到当前查询
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $from
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function mergeConstraintsFrom(Builder $from)
    {
        $whereBindings = $from->getQuery()->getRawBindings()['where'] ?? [];

        // Here we have some other query that we want to merge the where constraints from. We will
        // copy over any where constraints on the query as well as remove any global scopes the
        // query might have removed. Then we will return ourselves with the finished merging.
		// 这里我们有一些其他的查询我们想要合并where约束。
		// 我们会将查询中的任何位置限制信息一并复制过来，并移除查询可能已设定的任何全局范围限制。
		// 然后，我们将完成合并操作并将其返回给用户。
        return $this->withoutGlobalScopes(
            $from->removedScopes()
        )->mergeWheres(
            $from->getQuery()->wheres, $whereBindings
        );
    }

    /**
     * Add a sub-query count clause to this query.
	 * 向该查询添加子查询计数子句
	 * 
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @param  string  $operator
     * @param  int  $count
     * @param  string  $boolean
     * @return $this
     */
    protected function addWhereCountQuery(QueryBuilder $query, $operator = '>=', $count = 1, $boolean = 'and')
    {
        $this->query->addBinding($query->getBindings(), 'where');

        return $this->where(
            new Expression('('.$query->toSql().')'),
            $operator,
            is_numeric($count) ? new Expression($count) : $count,
            $boolean
        );
    }

    /**
     * Get the "has relation" base query instance.
	 * 获取“有关系”基查询实例
     *
     * @param  string  $relation
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    protected function getRelationWithoutConstraints($relation)
    {
        return Relation::noConstraints(function () use ($relation) {
            return $this->getModel()->{$relation}();
        });
    }

    /**
     * Check if we can run an "exists" query to optimize performance.
	 * 检查我们是否可以运行“exists”查询来优化性能
     *
     * @param  string  $operator
     * @param  int  $count
     * @return bool
     */
    protected function canUseExistsForExistenceCheck($operator, $count)
    {
        return ($operator === '>=' || $operator === '<') && $count === 1;
    }
}
