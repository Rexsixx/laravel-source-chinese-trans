<?php
/**
 * Illuminate，基础，测试，问题，与身份验证交互
 */

namespace Illuminate\Foundation\Testing\Concerns;

use Illuminate\Foundation\Testing\Constraints\HasInDatabase;
use PHPUnit\Framework\Constraint\LogicalNot as ReverseConstraint;
use Illuminate\Foundation\Testing\Constraints\SoftDeletedInDatabase;

trait InteractsWithDatabase
{
    /**
     * Assert that a given where condition exists in the database.
	 * 断言给定的where条件存在于数据库中
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string  $connection
     * @return $this
     */
    protected function assertDatabaseHas($table, array $data, $connection = null)
    {
        $this->assertThat(
            $table, new HasInDatabase($this->getConnection($connection), $data)
        );

        return $this;
    }

    /**
     * Assert that a given where condition does not exist in the database.
	 * 断言给定的where条件在数据库中不存在
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string  $connection
     * @return $this
     */
    protected function assertDatabaseMissing($table, array $data, $connection = null)
    {
        $constraint = new ReverseConstraint(
            new HasInDatabase($this->getConnection($connection), $data)
        );

        $this->assertThat($table, $constraint);

        return $this;
    }

    /**
     * Assert the given record has been deleted.
	 * 断言给定的记录已被删除
     *
     * @param  string  $table
     * @param  array  $data
     * @param  string  $connection
     * @return $this
     */
    protected function assertSoftDeleted($table, array $data, $connection = null)
    {
        $this->assertThat(
            $table, new SoftDeletedInDatabase($this->getConnection($connection), $data)
        );

        return $this;
    }

    /**
     * Get the database connection.
	 * 获取数据库连接
     *
     * @param  string|null  $connection
     * @return \Illuminate\Database\Connection
     */
    protected function getConnection($connection = null)
    {
        $database = $this->app->make('db');

        $connection = $connection ?: $database->getDefaultConnection();

        return $database->connection($connection);
    }

    /**
     * Seed a given database connection.
	 * 为给定的数据库连接播种
     *
     * @param  string  $class
     * @return $this
     */
    public function seed($class = 'DatabaseSeeder')
    {
        $this->artisan('db:seed', ['--class' => $class]);

        return $this;
    }
}
