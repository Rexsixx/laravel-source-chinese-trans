<?php
/**
 * Illuminate，数据库，查询，语法，Sql Server 语法
 */

namespace Illuminate\Database\Query\Grammars;

use Illuminate\Support\Arr;
use Illuminate\Database\Query\Builder;

class SqlServerGrammar extends Grammar
{
    /**
     * All of the available clause operators.
	 * 所有可用的子句操作符
     *
     * @var array
     */
    protected $operators = [
        '=', '<', '>', '<=', '>=', '!<', '!>', '<>', '!=',
        'like', 'not like', 'ilike',
        '&', '&=', '|', '|=', '^', '^=',
    ];

    /**
     * Compile a select query into SQL.
	 * 将一个选择查询编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileSelect(Builder $query)
    {
        if (! $query->offset) {
            return parent::compileSelect($query);
        }

        // If an offset is present on the query, we will need to wrap the query in
        // a big "ANSI" offset syntax block. This is very nasty compared to the
        // other database systems but is necessary for implementing features.
		// 如果在查询中存在一个偏移量,我们将需要将查询包在一个大的“ANSI”偏移语法块中。
		// 与其他数据库系统相比,这非常令人讨厌,但对于实现特性来说是必要的。
        if (is_null($query->columns)) {
            $query->columns = ['*'];
        }

        return $this->compileAnsiOffset(
            $query, $this->compileComponents($query)
        );
    }

    /**
     * Compile the "select *" portion of the query.
	 * 编译查询的“select *”部分
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $columns
     * @return string|null
     */
    protected function compileColumns(Builder $query, $columns)
    {
        if (! is_null($query->aggregate)) {
            return;
        }

        $select = $query->distinct ? 'select distinct ' : 'select ';

        // If there is a limit on the query, but not an offset, we will add the top
        // clause to the query, which serves as a "limit" type clause within the
        // SQL Server system similar to the limit keywords available in MySQL.
		// 如果查询有一个限制,而不是一个偏差,我们将将顶部子句添加到查询中,
		// 在SQL Server系统中作为一个“限制”类型子句,类似于MySQL中可用的限制关键字。
        if ($query->limit > 0 && $query->offset <= 0) {
            $select .= 'top '.$query->limit.' ';
        }

        return $select.$this->columnize($columns);
    }

    /**
     * Compile the "from" portion of the query.
	 * 编译查询的“from”部分
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @return string
     */
    protected function compileFrom(Builder $query, $table)
    {
        $from = parent::compileFrom($query, $table);

        if (is_string($query->lock)) {
            return $from.' '.$query->lock;
        }

        if (! is_null($query->lock)) {
            return $from.' with(rowlock,'.($query->lock ? 'updlock,' : '').'holdlock)';
        }

        return $from;
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

        return 'cast('.$this->wrap($where['column']).' as date) '.$where['operator'].' '.$value;
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

        return 'cast('.$this->wrap($where['column']).' as time) '.$where['operator'].' '.$value;
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
        [$field, $path] = $this->wrapJsonFieldAndPath($column);

        return $value.' in (select [value] from openjson('.$field.$path.'))';
    }

    /**
     * Prepare the binding for a "JSON contains" statement.
	 * 为“JSON contains”语句准备绑定
     *
     * @param  mixed  $binding
     * @return string
     */
    public function prepareBindingForJsonContains($binding)
    {
        return is_bool($binding) ? json_encode($binding) : $binding;
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
        [$field, $path] = $this->wrapJsonFieldAndPath($column);

        return '(select count(*) from openjson('.$field.$path.')) '.$operator.' '.$value;
    }

    /**
     * Create a full ANSI offset clause for the query.
	 * 为查询创建一个完整的ANSI偏移子句
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  array  $components
     * @return string
     */
    protected function compileAnsiOffset(Builder $query, $components)
    {
        // An ORDER BY clause is required to make this offset query work, so if one does
        // not exist we'll just create a dummy clause to trick the database and so it
        // does not complain about the queries for not having an "order by" clause.
		// 为了使此偏移查询工作,需要一个命令。
		// 因此,如果一个不存在,我们将创建一个虚拟子句来欺骗数据库,因此它不会抱怨查询没有“ORDER BY”子句。
        if (empty($components['orders'])) {
            $components['orders'] = 'order by (select 0)';
        }

        // We need to add the row number to the query so we can compare it to the offset
        // and limit values given for the statements. So we will add an expression to
        // the "select" that will give back the row numbers on each of the records.
		// 我们需要将行号添加到查询中,这样我们就可以将其与所给出的值的偏移量和极限值进行比较。
		// 因此,我们将向“select”添加一个表达式,它将在每个记录上给出行号。
        $components['columns'] .= $this->compileOver($components['orders']);

        unset($components['orders']);

        // Next we need to calculate the constraints that should be placed on the query
        // to get the right offset and limit from our query but if there is no limit
        // set we will just handle the offset only since that is all that matters.
		// 接下来,我们需要计算在查询中应该放置的约束,以得到我们的查询的正确偏移和限制,
		// 但如果没有限制,我们只会在所有重要的事情上只处理偏移量。
        $sql = $this->concatenate($components);

        return $this->compileTableExpression($sql, $query);
    }

    /**
     * Compile the over statement for a table expression.
	 * 编译表表达式的over语句
     *
     * @param  string  $orderings
     * @return string
     */
    protected function compileOver($orderings)
    {
        return ", row_number() over ({$orderings}) as row_num";
    }

    /**
     * Compile a common table expression for a query.
	 * 为查询编译公共表表达式
     *
     * @param  string  $sql
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileTableExpression($sql, $query)
    {
        $constraint = $this->compileRowConstraint($query);

        return "select * from ({$sql}) as temp_table where row_num {$constraint} order by row_num";
    }

    /**
     * Compile the limit / offset row constraint for a query.
	 * 为查询编译限制/偏移行约束
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    protected function compileRowConstraint($query)
    {
        $start = $query->offset + 1;

        if ($query->limit > 0) {
            $finish = $query->offset + $query->limit;

            return "between {$start} and {$finish}";
        }

        return ">= {$start}";
    }

    /**
     * Compile the random statement into SQL.
	 * 将随机语句编译成SQL
     *
     * @param  string  $seed
     * @return string
     */
    public function compileRandom($seed)
    {
        return 'NEWID()';
    }

    /**
     * Compile the "limit" portions of the query.
	 * 编译查询的“limit”部分
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $limit
     * @return string
     */
    protected function compileLimit(Builder $query, $limit)
    {
        return '';
    }

    /**
     * Compile the "offset" portions of the query.
	 * 编译查询的“偏移”部分
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  int  $offset
     * @return string
     */
    protected function compileOffset(Builder $query, $offset)
    {
        return '';
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
        return '';
    }

    /**
     * Compile an exists statement into SQL.
	 * 将exists语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return string
     */
    public function compileExists(Builder $query)
    {
        $existsQuery = clone $query;

        $existsQuery->columns = [];

        return $this->compileSelect($existsQuery->selectRaw('1 [exists]')->limit(1));
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

        $where = is_array($query->wheres) ? $this->compileWheres($query) : '';

        return isset($query->joins)
                    ? $this->compileDeleteWithJoins($query, $table, $where)
                    : trim("delete from {$table} {$where}");
    }

    /**
     * Compile a delete statement with joins into SQL.
	 * 将带有连接的删除语句编译成SQL
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @param  string  $table
     * @param  string  $where
     * @return string
     */
    protected function compileDeleteWithJoins(Builder $query, $table, $where)
    {
        $joins = ' '.$this->compileJoins($query, $query->joins);

        $alias = stripos($table, ' as ') !== false
                ? explode(' as ', $table)[1] : $table;

        return trim("delete {$alias} from {$table}{$joins} {$where}");
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
        return ['truncate table '.$this->wrapTable($query->from) => []];
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
        [$table, $alias] = $this->parseUpdateTable($query->from);

        // Each one of the columns in the update statements needs to be wrapped in the
        // keyword identifiers, also a place-holder needs to be created for each of
        // the values in the list of bindings so we can make the sets statements.
		// 更新语句中的每一个列都需要被包在关键字标识中,也需要为绑定列表中的每个值创建一个place-holder,
		// 这样我们就可以进行集合语句。
        $columns = collect($values)->map(function ($value, $key) {
            return $this->wrap($key).' = '.$this->parameter($value);
        })->implode(', ');

        // If the query has any "join" clauses, we will setup the joins on the builder
        // and compile them so we can attach them to this update, as update queries
        // can get join statements to attach to other tables when they're needed.
		// 如果查询有任何“join”子句,我们将在构建器上设置连接,并编译它们,
		// 这样我们就可以将它们连接到这个更新,因为更新查询可以在需要时连接到其他表的连接语句。
        $joins = '';

        if (isset($query->joins)) {
            $joins = ' '.$this->compileJoins($query, $query->joins);
        }

        // Of course, update queries may also be constrained by where clauses so we'll
        // need to compile the where clauses and attach it to the query so only the
        // intended records are updated by the SQL statements we generate to run.
		// 当然,更新查询也可能受到where子句的限制,因此我们需要编译where子句,
		// 并将其附加到查询中,所以只有预期的记录由我们生成的SQL语句更新。
        $where = $this->compileWheres($query);

        if (! empty($joins)) {
            return trim("update {$alias} set {$columns} from {$table}{$joins} {$where}");
        }

        return trim("update {$table}{$joins} set $columns $where");
    }

    /**
     * Get the table and alias for the given table.
	 * 获取给定表的表和别名
     *
     * @param  string  $table
     * @return array
     */
    protected function parseUpdateTable($table)
    {
        $table = $alias = $this->wrapTable($table);

        if (stripos($table, '] as [') !== false) {
            $alias = '['.explode('] as [', $table)[1];
        }

        return [$table, $alias];
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
        // Update statements with joins in SQL Servers utilize an unique syntax. We need to
        // take all of the bindings and put them on the end of this array since they are
        // added to the end of the "where" clause statements as typical where clauses.
		// 在SQL server中使用连接更新语句使用一种独特的语法。
		// 我们需要把所有的绑定都放在这个数组的末尾,因为它们被添加到“where”子句语句的末尾,这是典型的where子句。
        $bindingsWithoutJoin = Arr::except($bindings, 'join');

        return array_values(
            array_merge($values, $bindings['join'], Arr::flatten($bindingsWithoutJoin))
        );
    }

    /**
     * Compile the SQL statement to define a savepoint.
	 * 编译SQL语句以定义保存点
     *
     * @param  string  $name
     * @return string
     */
    public function compileSavepoint($name)
    {
        return 'SAVE TRANSACTION '.$name;
    }

    /**
     * Compile the SQL statement to execute a savepoint rollback.
	 * 编译SQL语句以执行保存点回滚
     *
     * @param  string  $name
     * @return string
     */
    public function compileSavepointRollBack($name)
    {
        return 'ROLLBACK TRANSACTION '.$name;
    }

    /**
     * Get the format for database stored dates.
	 * 获取数据库存储日期的格式
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.v';
    }

    /**
     * Wrap a single string in keyword identifiers.
	 * 在关键字标识符中包装单个字符串
     *
     * @param  string  $value
     * @return string
     */
    protected function wrapValue($value)
    {
        return $value === '*' ? $value : '['.str_replace(']', ']]', $value).']';
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
        $parts = explode('->', $value, 2);

        $field = $this->wrapSegments(explode('.', array_shift($parts)));

        return 'json_value('.$field.', '.$this->wrapJsonPath($parts[0]).')';
    }

    /**
     * Wrap a table in keyword identifiers.
	 * 用关键字标识符包装表
     *
     * @param  \Illuminate\Database\Query\Expression|string  $table
     * @return string
     */
    public function wrapTable($table)
    {
        if (! $this->isExpression($table)) {
            return $this->wrapTableValuedFunction(parent::wrapTable($table));
        }

        return $this->getValue($table);
    }

    /**
     * Wrap a table in keyword identifiers.
	 * 用关键字标识符包装表。
     *
     * @param  string  $table
     * @return string
     */
    protected function wrapTableValuedFunction($table)
    {
        if (preg_match('/^(.+?)(\(.*?\))]$/', $table, $matches) === 1) {
            $table = $matches[1].']'.$matches[2];
        }

        return $table;
    }
}
