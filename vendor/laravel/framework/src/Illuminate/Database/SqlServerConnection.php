<?php
/**
 * Illuminate，数据库，Sql Server 连接
 */

namespace Illuminate\Database;

use Closure;
use Exception;
use Throwable;
use Illuminate\Database\Schema\SqlServerBuilder;
use Doctrine\DBAL\Driver\PDOSqlsrv\Driver as DoctrineDriver;
use Illuminate\Database\Query\Processors\SqlServerProcessor;
use Illuminate\Database\Query\Grammars\SqlServerGrammar as QueryGrammar;
use Illuminate\Database\Schema\Grammars\SqlServerGrammar as SchemaGrammar;

class SqlServerConnection extends Connection
{
    /**
     * Execute a Closure within a transaction.
	 * 在事务中执行闭包
     *
     * @param  \Closure  $callback
     * @param  int  $attempts
     * @return mixed
     *
     * @throws \Exception|\Throwable
     */
    public function transaction(Closure $callback, $attempts = 1)
    {
        for ($a = 1; $a <= $attempts; $a++) {
            if ($this->getDriverName() === 'sqlsrv') {
                return parent::transaction($callback);
            }

            $this->getPdo()->exec('BEGIN TRAN');

            // We'll simply execute the given callback within a try / catch block
            // and if we catch any exception we can rollback the transaction
            // so that none of the changes are persisted to the database.
			// 我们将直接在“try”/“catch”块中执行给定的回调函数，如果捕获到任何异常，
			// 我们就可以回滚事务，这样就不会有任何更改被保存到数据库中。
            try {
                $result = $callback($this);

                $this->getPdo()->exec('COMMIT TRAN');
            }

            // If we catch an exception, we will roll back so nothing gets messed
            // up in the database. Then we'll re-throw the exception so it can
            // be handled how the developer sees fit for their applications.
			// 如果出现异常情况，我们将进行回滚操作，以确保数据库中的数据不会遭到破坏。
			// 然后我们将重新抛出异常,这样就可以处理开发人员如何看待它们的应用程序。
            catch (Exception $e) {
                $this->getPdo()->exec('ROLLBACK TRAN');

                throw $e;
            } catch (Throwable $e) {
                $this->getPdo()->exec('ROLLBACK TRAN');

                throw $e;
            }

            return $result;
        }
    }

    /**
     * Get the default query grammar instance.
	 * 获取默认查询语法实例
     *
     * @return \Illuminate\Database\Query\Grammars\SqlServerGrammar
     */
    protected function getDefaultQueryGrammar()
    {
        return $this->withTablePrefix(new QueryGrammar);
    }

    /**
     * Get a schema builder instance for the connection.
	 * 获取连接的架构构建器实例
     *
     * @return \Illuminate\Database\Schema\SqlServerBuilder
     */
    public function getSchemaBuilder()
    {
        if (is_null($this->schemaGrammar)) {
            $this->useDefaultSchemaGrammar();
        }

        return new SqlServerBuilder($this);
    }

    /**
     * Get the default schema grammar instance.
	 * 获取默认模式语法实例
     *
     * @return \Illuminate\Database\Schema\Grammars\SqlServerGrammar
     */
    protected function getDefaultSchemaGrammar()
    {
        return $this->withTablePrefix(new SchemaGrammar);
    }

    /**
     * Get the default post processor instance.
	 * 获取默认的后处理器实例
     *
     * @return \Illuminate\Database\Query\Processors\SqlServerProcessor
     */
    protected function getDefaultPostProcessor()
    {
        return new SqlServerProcessor;
    }

    /**
     * Get the Doctrine DBAL driver.
	 * 获取Doctrine DBAL驱动程序
     *
     * @return \Doctrine\DBAL\Driver\PDOSqlsrv\Driver
     */
    protected function getDoctrineDriver()
    {
        return new DoctrineDriver;
    }
}
