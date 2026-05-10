<?php
/**
 * Illuminate，数据库，模式，Sql Server 构建器
 */

namespace Illuminate\Database\Schema;

class SqlServerBuilder extends Builder
{
    /**
     * Drop all tables from the database.
	 * 从数据库中删除所有表
     *
     * @return void
     */
    public function dropAllTables()
    {
        $this->disableForeignKeyConstraints();

        $this->connection->statement($this->grammar->compileDropAllTables());

        $this->enableForeignKeyConstraints();
    }
}
