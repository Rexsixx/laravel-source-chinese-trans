<?php
/**
 * Illuminate，数据库，连接器，SQLite 连接器
 */

namespace Illuminate\Database\Connectors;

use InvalidArgumentException;

class SQLiteConnector extends Connector implements ConnectorInterface
{
    /**
     * Establish a database connection.
	 * 建立数据库连接
     *
     * @param  array  $config
     * @return \PDO
     *
     * @throws \InvalidArgumentException
     */
    public function connect(array $config)
    {
        $options = $this->getOptions($config);

        // SQLite supports "in-memory" databases that only last as long as the owning
        // connection does. These are useful for tests or for short lifetime store
        // querying. In-memory databases may only have a single open connection.
		// SQLiteSQLite 支持“内存”数据库，其生命周期仅与所属连接相同。
		// 这些数据库适用于测试或短期存储查询。内存数据库可能只允许一个打开的连接。
        if ($config['database'] === ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['database']);

        // Here we'll verify that the SQLite database exists before going any further
        // as the developer probably wants to know if the database exists and this
        // SQLite driver will not throw any exception if it does not by default.
		// 在此我们先验证 SQLite 数据库是否存在，因为开发者可能希望确认数据库是否已存在，
		// 而该 SQLite SQLite SQLite 驱动程序默认情况下如果不存在不会抛出异常。
        if ($path === false) {
            throw new InvalidArgumentException("Database ({$config['database']}) does not exist.");
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }
}
