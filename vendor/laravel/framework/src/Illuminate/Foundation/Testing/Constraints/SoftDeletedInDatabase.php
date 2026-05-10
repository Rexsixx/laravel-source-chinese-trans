<?php
/**
 * Illuminate，基础，测试，约束条件，数据库软删除
 */

namespace Illuminate\Foundation\Testing\Constraints;

use Illuminate\Database\Connection;
use PHPUnit\Framework\Constraint\Constraint;

class SoftDeletedInDatabase extends Constraint
{
    /**
     * Number of records that will be shown in the console in case of failure.
	 * 在发生故障时将在控制台中显示的记录数
     *
     * @var int
     */
    protected $show = 3;

    /**
     * The database connection.
	 * 数据库连接
     *
     * @var \Illuminate\Database\Connection
     */
    protected $database;

    /**
     * The data that will be used to narrow the search in the database table.
	 * 将用于缩小数据库表中搜索范围的数据
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new constraint instance.
	 * 创建一个新的约束实例
     *
     * @param  \Illuminate\Database\Connection  $database
     * @param  array  $data
     * @return void
     */
    public function __construct(Connection $database, array $data)
    {
        $this->data = $data;

        $this->database = $database;
    }

    /**
     * Check if the data is found in the given table.
	 * 检查是否在给定的表中找到数据
     *
     * @param  string  $table
     * @return bool
     */
    public function matches($table): bool
    {
        return $this->database->table($table)
                ->where($this->data)->whereNotNull('deleted_at')->count() > 0;
    }

    /**
     * Get the description of the failure.
	 * 获取失败的描述
     *
     * @param  string  $table
     * @return string
     */
    public function failureDescription($table): string
    {
        return sprintf(
            "any soft deleted row in the table [%s] matches the attributes %s.\n\n%s",
            $table, $this->toString(), $this->getAdditionalInfo($table)
        );
    }

    /**
     * Get additional info about the records found in the database table.
	 * 获取关于在数据库表中找到的记录的其他信息
     *
     * @param  string  $table
     * @return string
     */
    protected function getAdditionalInfo($table)
    {
        $results = $this->database->table($table)->get();

        if ($results->isEmpty()) {
            return 'The table is empty';
        }

        $description = 'Found: '.json_encode($results->take($this->show), JSON_PRETTY_PRINT);

        if ($results->count() > $this->show) {
            $description .= sprintf(' and %s others', $results->count() - $this->show);
        }

        return $description;
    }

    /**
     * Get a string representation of the object.
	 * 获取对象的字符串表示形式
     *
     * @return string
     */
    public function toString(): string
    {
        return json_encode($this->data);
    }
}
