<?php
/**
 * Illuminate，数据库，查询，语法，Postgres 语法
 */

namespace Illuminate\Database\Query\Grammars;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Query\Builder;

class PostgresGrammar extends Grammar
{
    /**
     * The components that make up a select clause.
	 * 组成select子句的组件
     *
     * @var array
     */
    protected $selectComponents = [
        'aggregate',
        'columns',
        'from',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'lock',
    ];

    /**
     * All of the available clause operators.
	 * 所有可用的子句操作符
     *
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'like', 'not like', 'between', 'ilike', 'not ilike',
        '~', '&', '|', '#', '<<', '>>', '<<=', '>>=',
        '&&', '@>', '<@', '?', '?|', '?&', '||', '-', '-', '#-',
        'is distinct from', 'is not distinct from',
    ];

    /**
     * {@inheritdoc}
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereBasic(Builder $query, $where)
    {
        if (Str::contains(strtolower($where['operator']), 'like')) {
            return sprintf(
                '%s::text %s %s',
                $this->wrap($where['column']),
                $where['operator'],
                $this->parameter($where['value'])
            );
        }

        return parent::whereBasic($query, $where);
    }

    /**
     * Compile a "where date" clause.
	 * 编译一个“where date”子句
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereDate(Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        return $this->wrap($where['column']).'::date '.$where['operator'].' '.$value;
    }

    /**
     * Compile a "where time" clause.
	 * 编写一个“where time”子句
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function whereTime(Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        return $this->wrap($where['column']).'::time '.$where['operator'].' '.$value;
    }

    /**
     * Compile a date based where clause.
	 * 编译一个基于日期的where子句
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $where
     * @return string
     */
    protected function dateBasedWhere($type, Builder $query, $where)
    {
        $value = $this->parameter($where['value']);

        return 'extract('.$type.' from '.$this->wrap($where['column']).') '.$where['operator'].' '.$value;
    }

    /**
     * Compile a select query into SQL.
	 * 将一个选择查询编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        if ($query->unions && $query->aggregate) {
            return $this->compileUnionAggregate($query);
        }

        $sql = parent::compileSelect($query);

        if ($query->unions) {
            $sql = '('.$sql.') '.$this->compileUnions($query);
        }

        return $sql;
    }

    /**
     * Compile a single union statement.
	 * 编译单个联合语句
     *
     * @param  array  $union
     * @return string
     */
    protected function compileUnion(array $union)
    {
        $conjunction = $union['all'] ? ' union all ' : ' union ';

        return $conjunction.'('.$union['query']->toSql().')';
    }

    /**
     * Compile a "JSON contains" statement into SQL.
	 * 将“JSON contains”语句编译成SQL
     *
     * @param  string  $column
     * @param  string  $value
     * @return string
     */
    protected function compileJsonContains($column, $value)
    {
        $column = str_replace('->>', '->', $this->wrap($column));

        return '('.$column.')::jsonb @> '.$value;
    }

    /**
     * Compile a "JSON length" statement into SQL.
	 * 将“JSON长度”语句编译成SQL
     *
     * @param  string  $column
     * @param  string  $operator
     * @param  string  $value
     * @return string
     */
    protected function compileJsonLength($column, $operator, $value)
    {
        $column = str_replace('->>', '->', $this->wrap($column));

        return 'json_array_length(('.$column.')::json) '.$operator.' '.$value;
    }

    /**
     * Compile the lock into SQL.
	 * 将锁编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  bool|string  $value
     * @return string
     */
    protected function compileLock(Builder $query, $value)
    {
        if (! is_string($value)) {
            return $value ? 'for update' : 'for share';
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function compileInsert(Builder $query, array $values)
    {
        $table = $this->wrapTable($query->from);

        return empty($values)
                ? "insert into {$table} DEFAULT VALUES"
                : parent::compileInsert($query, $values);
    }

    /**
     * Compile an insert ignore statement into SQL.
	 * 将插入忽略语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileInsertOrIgnore(Builder $query, array $values)
    {
        return $this->compileInsert($query, $values).' on conflict do nothing';
    }

    /**
     * Compile an insert and get ID statement into SQL.
	 * 将插入和获取ID语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array   $values
     * @param  string  $sequence
     * @return string
     */
    public function compileInsertGetId(Builder $query, $values, $sequence)
    {
        return $this->compileInsert($query, $values).' returning '.$this->wrap($sequence ?: 'id');
    }

    /**
     * Compile an update statement into SQL.
	 * 将update语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $values
     * @return string
     */
    public function compileUpdate(Builder $query, $values)
    {
        $table = $this->wrapTable($query->from);

        // Each one of the columns in the update statements needs to be wrapped in the
        // keyword identifiers, also a place-holder needs to be created for each of
        // the values in the list of bindings so we can make the sets statements.
		// 在更新语句中的每一列都需要用“关键字标识符”进行包裹，同时还需要为绑定列表中的每个值创建一个占位符，这样我们才能编写集合语句。
        $columns = $this->compileUpdateColumns($query, $values);

        $from = $this->compileUpdateFrom($query);

        $where = $this->compileUpdateWheres($query);

        return trim("update {$table} set {$columns}{$from} {$where}");
    }

    /**
     * Compile the columns for the update statement.
	 * 编译update语句的列
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array   $values
     * @return string
     */
    protected function compileUpdateColumns($query, $values)
    {
        // When gathering the columns for an update statement, we'll wrap each of the
        // columns and convert it to a parameter value. Then we will concatenate a
        // list of the columns that can be added into this update query clauses.
		// 在为更新语句收集列时，我们会将每个列进行封装，并将其转换为参数值。
		// 然后，我们将把可以添加到此更新查询语句中的列列表进行连接。
        return collect($values)->map(function ($value, $key) {
            $column = last(explode('.', $key));

            if ($this->isJsonSelector($key)) {
                return $this->compileJsonUpdateColumn($column, $value);
            }

            return $this->wrap($column).' = '.$this->parameter($value);
        })->implode(', ');
    }

    /**
     * Prepares a JSON column being updated using the JSONB_SET function.
	 * 使用JSONB_SET函数准备要更新的JSON列
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return string
     */
    protected function compileJsonUpdateColumn($key, $value)
    {
        $parts = explode('->', $key);

        $field = $this->wrap(array_shift($parts));

        $path = '\'{"'.implode('","', $parts).'"}\'';

        return "{$field} = jsonb_set({$field}::jsonb, {$path}, {$this->parameter($value)})";
    }

    /**
     * Compile the "from" clause for an update with a join.
	 * 编译带有连接的更新的“from”子句
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string|null
     */
    protected function compileUpdateFrom(Builder $query)
    {
        if (! isset($query->joins)) {
            return '';
        }

        // When using Postgres, updates with joins list the joined tables in the from
        // clause, which is different than other systems like MySQL. Here, we will
        // compile out the tables that are joined and add them to a from clause.
		// 在使用 Postgres 时，通过连接进行的更新会在“from”子句中列出所连接的表，这与像 MySQL 这样的其他系统有所不同。
		// 在这里，我们将编译出已连接的表，并将其添加到 from 子句中。
        $froms = collect($query->joins)->map(function ($join) {
            return $this->wrapTable($join->table);
        })->all();

        if (count($froms) > 0) {
            return ' from '.implode(', ', $froms);
        }
    }

    /**
     * Compile the additional where clauses for updates with joins.
	 * 编译用于连接更新的附加where子句
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateWheres(Builder $query)
    {
        $baseWheres = $this->compileWheres($query);

        if (! isset($query->joins)) {
            return $baseWheres;
        }

        // Once we compile the join constraints, we will either use them as the where
        // clause or append them to the existing base where clauses. If we need to
        // strip the leading boolean we will do so when using as the only where.
		// 一旦我们完成了连接约束的编译工作，我们就会将它们用作“where”子句，或者将其附加到现有的基本“where”子句之后。
		// 如果需要去除前面的布尔值部分，那么在仅使用“as”作为条件时，我们将进行相应的处理。
        $joinWheres = $this->compileUpdateJoinWheres($query);

        if (trim($baseWheres) == '') {
            return 'where '.$this->removeLeadingBoolean($joinWheres);
        }

        return $baseWheres.' '.$joinWheres;
    }

    /**
     * Compile the "join" clause where clauses for an update.
	 * 编译“join”子句，其中的子句用于更新。
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileUpdateJoinWheres(Builder $query)
    {
        $joinWheres = [];

        // Here we will just loop through all of the join constraints and compile them
        // all out then implode them. This should give us "where" like syntax after
        // everything has been built and then we will join it to the real wheres.
		// 在这里，我们将逐一遍历所有的连接约束条件，将它们全部整理出来，然后进行合并。
		// 这应该会在所有内容构建完成后为我们提供类似“何处”的语法结构，然后我们将将其与实际的“何处”结构相结合。
        foreach ($query->joins as $join) {
            foreach ($join->wheres as $where) {
                $method = "where{$where['type']}";

                $joinWheres[] = $where['boolean'].' '.$this->$method($query, $where);
            }
        }

        return implode(' ', $joinWheres);
    }

    /**
     * Prepare the bindings for an update statement.
	 * 为更新语句准备绑定
     *
     * @param  array  $bindings
     * @param  array  $values
     * @return array
     */
    public function prepareBindingsForUpdate(array $bindings, array $values)
    {
        $values = collect($values)->map(function ($value, $column) {
            return $this->isJsonSelector($column) && ! $this->isExpression($value)
                ? json_encode($value)
                : $value;
        })->all();

        $bindingsWithoutWhere = Arr::except($bindings, ['select', 'where']);

        return array_values(
            array_merge($values, $bindings['where'], Arr::flatten($bindingsWithoutWhere))
        );
    }

    /**
     * Compile a delete statement into SQL.
	 * 将delete语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileDelete(Builder $query)
    {
        $table = $this->wrapTable($query->from);

        return isset($query->joins)
            ? $this->compileDeleteWithJoins($query, $table)
            : parent::compileDelete($query);
    }

    /**
     * Compile a delete query that uses joins.
	 * 编译一个使用连接的删除查询
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @return string
     */
    protected function compileDeleteWithJoins($query, $table)
    {
        $using = ' USING '.collect($query->joins)->map(function ($join) {
            return $this->wrapTable($join->table);
        })->implode(', ');

        $where = $this->compileUpdateWheres($query);

        return trim("delete from {$table}{$using} {$where}");
    }

    /**
     * Prepare the bindings for a delete statement.
	 * 为delete语句准备绑定
     *
     * @param  array  $bindings
     * @return array
     */
    public function prepareBindingsForDelete(array $bindings)
    {
        $bindingsWithoutWhere = Arr::except($bindings, ['select', 'where']);

        return array_values(
            array_merge($bindings['where'], Arr::flatten($bindingsWithoutWhere))
        );
    }

    /**
     * Compile a truncate table statement into SQL.
	 * 将截断表语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return array
     */
    public function compileTruncate(Builder $query)
    {
        return ['truncate '.$this->wrapTable($query->from).' restart identity cascade' => []];
    }

    /**
     * Wrap the given JSON selector.
	 * 包装给定的JSON选择器
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonSelector($value)
    {
        $path = explode('->', $value);

        $field = $this->wrapSegments(explode('.', array_shift($path)));

        $wrappedPath = $this->wrapJsonPathAttributes($path);

        $attribute = array_pop($wrappedPath);

        if (! empty($wrappedPath)) {
            return $field.'->'.implode('->', $wrappedPath).'->>'.$attribute;
        }

        return $field.'->>'.$attribute;
    }

    /**
     * Wrap the given JSON selector for boolean values.
	 * 将给定的JSON选择器包装为布尔值
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanSelector($value)
    {
        $selector = str_replace(
            '->>', '->',
            $this->wrapJsonSelector($value)
        );

        return '('.$selector.')::jsonb';
    }

    /**
     * Wrap the given JSON boolean value.
	 * 包装给定的JSON布尔值
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapJsonBooleanValue($value)
    {
        return "'".$value."'::jsonb";
    }

    /**
     * Wrap the attributes of the give JSON path.
	 * 包装给定JSON路径的属性
     *
     * @param  array  $path
     * @return array
     */
    protected function wrapJsonPathAttributes($path)
    {
        return array_map(function ($attribute) {
            return "'$attribute'";
        }, $path);
    }
}
