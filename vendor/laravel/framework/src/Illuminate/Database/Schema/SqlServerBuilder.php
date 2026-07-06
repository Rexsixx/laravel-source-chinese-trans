<?php
/**
 * Illuminate，数据库，架构，Sql Server 构建器
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
        $this->connection->statement($this->grammar->compileDropAllForeignKeys());

        $this->connection->statement($this->grammar->compileDropAllTables());
    }
}
