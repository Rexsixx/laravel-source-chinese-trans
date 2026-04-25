<?php
/**
 * Illuminate，数据库，架构，Postgres 生成器
 */

namespace Illuminate\Database\Schema;

class PostgresBuilder extends Builder
{
    /**
     * Determine if the given table exists.
	 * 确定给定的表是否存在
     *
     * @param  string  $table
     * @return bool
     */
    public function hasTable($table)
    {
        list($schema, $table) = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix().$table;

        return count($this->connection->select(
            $this->grammar->compileTableExists(), [$schema, $table]
        )) > 0;
    }

    /**
     * Drop all tables from the database.
	 * 从数据库中删除所有表
     *
     * @return void
     */
    public function dropAllTables()
    {
        $tables = [];

        $excludedTables = ['spatial_ref_sys'];

        foreach ($this->getAllTables() as $row) {
            $row = (array) $row;

            $table = reset($row);

            if (! in_array($table, $excludedTables)) {
                $tables[] = $table;
            }
        }

        if (empty($tables)) {
            return;
        }

        $this->connection->statement(
            $this->grammar->compileDropAllTables($tables)
        );
    }

    /**
     * Get all of the table names for the database.
	 * 获取数据库的所有表名
     *
     * @return array
     */
    protected function getAllTables()
    {
        return $this->connection->select(
            $this->grammar->compileGetAllTables($this->connection->getConfig('schema'))
        );
    }

    /**
     * Get the column listing for a given table.
	 * 获取给定表的列清单
     *
     * @param  string  $table
     * @return array
     */
    public function getColumnListing($table)
    {
        list($schema, $table) = $this->parseSchemaAndTable($table);

        $table = $this->connection->getTablePrefix().$table;

        $results = $this->connection->select(
            $this->grammar->compileColumnListing(), [$schema, $table]
        );

        return $this->connection->getPostProcessor()->processColumnListing($results);
    }

    /**
     * Parse the table name and extract the schema and table.
	 * 解析表名并提取模式和表
     *
     * @param  string  $table
     * @return array
     */
    protected function parseSchemaAndTable($table)
    {
        $table = explode('.', $table);

        if (is_array($schema = $this->connection->getConfig('schema'))) {
            if (in_array($table[0], $schema)) {
                return [array_shift($table), implode('.', $table)];
            }

            $schema = head($schema);
        }

        return [$schema ?: 'public', implode('.', $table)];
    }
}
