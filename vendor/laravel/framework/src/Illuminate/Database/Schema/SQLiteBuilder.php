<?php
/**
 * Illuminate，数据库，模式，SQLite 构建器
 */

namespace Illuminate\Database\Schema;

class SQLiteBuilder extends Builder
{
    /**
     * Drop all tables from the database.
	 * 从数据库中删除所有表
     *
     * @return void
     */
    public function dropAllTables()
    {
        if ($this->connection->getDatabaseName() !== ':memory:') {
            return $this->refreshDatabaseFile();
        }

        $this->connection->select($this->grammar->compileEnableWriteableSchema());

        $this->connection->select($this->grammar->compileDropAllTables());

        $this->connection->select($this->grammar->compileDisableWriteableSchema());
    }

    /**
     * Drop all views from the database.
	 * 从数据库中删除所有视图
     *
     * @return void
     */
    public function dropAllViews()
    {
        $this->connection->select($this->grammar->compileEnableWriteableSchema());

        $this->connection->select($this->grammar->compileDropAllViews());

        $this->connection->select($this->grammar->compileDisableWriteableSchema());
    }

    /**
     * Empty the database file.
	 * 清空数据库文件
     *
     * @return void
     */
    public function refreshDatabaseFile()
    {
        file_put_contents($this->connection->getDatabaseName(), '');
    }
}
