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
		// SQLite 支持“内存型”数据库，这类数据库的存续时间仅与其所属的连接持续时间一致。
		// 这些对于测试或进行短期存储查询非常有用。内存数据库通常只允许有一个打开的连接。
        if ($config['database'] === ':memory:') {
            return $this->createConnection('sqlite::memory:', $config, $options);
        }

        $path = realpath($config['database']);

        // Here we'll verify that the SQLite database exists before going any further
        // as the developer probably wants to know if the database exists and this
        // SQLite driver will not throw any exception if it does not by default.
		// 在这里，我们会在继续执行之前先验证 SQLite 数据库是否存在，因为开发人员可能希望了解该数据库是否已存在。
		// 而且，这个 SQLite 驱动程序默认情况下不会抛出任何异常，即便数据库不存在也是如此。
        if ($path === false) {
            throw new InvalidArgumentException("Database ({$config['database']}) does not exist.");
        }

        return $this->createConnection("sqlite:{$path}", $config, $options);
    }
}
