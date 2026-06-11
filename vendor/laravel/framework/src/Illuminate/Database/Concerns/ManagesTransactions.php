<?php
/**
 * Illuminate，数据库，问题，管理事务
 */

namespace Illuminate\Database\Concerns;

use Closure;
use Exception;
use Throwable;

trait ManagesTransactions
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
        for ($currentAttempt = 1; $currentAttempt <= $attempts; $currentAttempt++) {
            $this->beginTransaction();

            // We'll simply execute the given callback within a try / catch block and if we
            // catch any exception we can rollback this transaction so that none of this
            // gets actually persisted to a database or stored in a permanent fashion.
			// 我们将简单地在一个try / catch块中执行给定的回调,如果我们知道任何例外,
			// 我们可以回滚这个事务,这样就不会有任何一个东西被持久化到数据库中,或者以永久的方式存储。
            try {
                return tap($callback($this), function () {
                    $this->commit();
                });
            }

            // If we catch an exception we'll rollback this transaction and try again if we
            // are not out of attempts. If we are out of attempts we will just throw the
            // exception back out and let the developer handle an uncaught exceptions.
			// 如果我们捕获一个例外,我们将回滚该事务,如果我们不退出尝试,再试一次。
			// 如果我们没有尝试,我们将抛出异常,让开发人员处理一个未捕获的异常。
            catch (Exception $e) {
                $this->handleTransactionException(
                    $e, $currentAttempt, $attempts
                );
            } catch (Throwable $e) {
                $this->rollBack();

                throw $e;
            }
        }
    }

    /**
     * Handle an exception encountered when running a transacted statement.
	 * 处理运行事务语句时遇到的异常
     *
     * @param  \Exception  $e
     * @param  int  $currentAttempt
     * @param  int  $maxAttempts
     * @return void
     *
     * @throws \Exception
     */
    protected function handleTransactionException($e, $currentAttempt, $maxAttempts)
    {
        // On a deadlock, MySQL rolls back the entire transaction so we can't just
        // retry the query. We have to throw this exception all the way out and
        // let the developer handle it in another way. We will decrement too.
		// 在死锁上,MySQL将整个事务卷回来,所以我们不能仅仅重新尝试查询。
		// 我们必须把这个异常抛出,让开发人员以另一种方式来处理它。我们也会堕落。
        if ($this->causedByDeadlock($e) &&
            $this->transactions > 1) {
            $this->transactions--;

            throw $e;
        }

        // If there was an exception we will rollback this transaction and then we
        // can check if we have exceeded the maximum attempt count for this and
        // if we haven't we will return and try this query again in our loop.
		// 如果有例外,我们将回滚这个事务,然后我们可以检查是否已经超过了这个的最大尝试,
		// 如果我们没有返回,我们将在循环中再次尝试这个查询。
        $this->rollBack();

        if ($this->causedByDeadlock($e) &&
            $currentAttempt < $maxAttempts) {
            return;
        }

        throw $e;
    }

    /**
     * Start a new database transaction.
	 * 启动一个新的数据库事务
     *
     * @return void
     *
     * @throws \Exception
     */
    public function beginTransaction()
    {
        $this->createTransaction();

        $this->transactions++;

        $this->fireConnectionEvent('beganTransaction');
    }

    /**
     * Create a transaction within the database.
	 * 在数据库中创建一个事务
     *
     * @return void
     */
    protected function createTransaction()
    {
        if ($this->transactions == 0) {
            try {
                $this->getPdo()->beginTransaction();
            } catch (Exception $e) {
                $this->handleBeginTransactionException($e);
            }
        } elseif ($this->transactions >= 1 && $this->queryGrammar->supportsSavepoints()) {
            $this->createSavepoint();
        }
    }

    /**
     * Create a save point within the database.
	 * 在数据库中创建一个保存点
     *
     * @return void
     */
    protected function createSavepoint()
    {
        $this->getPdo()->exec(
            $this->queryGrammar->compileSavepoint('trans'.($this->transactions + 1))
        );
    }

    /**
     * Handle an exception from a transaction beginning.
	 * 从事务开始处理异常
     *
     * @param  \Throwable  $e
     * @return void
     *
     * @throws \Exception
     */
    protected function handleBeginTransactionException($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->reconnect();

            $this->pdo->beginTransaction();
        } else {
            throw $e;
        }
    }

    /**
     * Commit the active database transaction.
	 * 提交活动数据库事务
     *
     * @return void
     */
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->getPdo()->commit();
        }

        $this->transactions = max(0, $this->transactions - 1);

        $this->fireConnectionEvent('committed');
    }

    /**
     * Rollback the active database transaction.
	 * 回滚活动数据库事务
     *
     * @param  int|null  $toLevel
     * @return void
     *
     * @throws \Exception
     */
    public function rollBack($toLevel = null)
    {
        // We allow developers to rollback to a certain transaction level. We will verify
        // that this given transaction level is valid before attempting to rollback to
        // that level. If it's not we will just return out and not attempt anything.
		// 我们允许开发人员回滚到某个事务级别。
		// 我们将验证这个给定的事务级别在尝试回滚到这个级别之前是有效的。
		// 如果不是我们要回来,不要尝试任何东西。
        $toLevel = is_null($toLevel)
                    ? $this->transactions - 1
                    : $toLevel;

        if ($toLevel < 0 || $toLevel >= $this->transactions) {
            return;
        }

        // Next, we will actually perform this rollback within this database and fire the
        // rollback event. We will also set the current transaction level to the given
        // level that was passed into this method so it will be right from here out.
		// 接下来,我们将在这个数据库中执行这个回滚,并启动回滚事件。
		// 我们还将将当前的事务级别设置为给定的级别,它被传递到这个方法中,因此它将是正确的。
        try {
            $this->performRollBack($toLevel);
        } catch (Exception $e) {
            $this->handleRollBackException($e);
        }

        $this->transactions = $toLevel;

        $this->fireConnectionEvent('rollingBack');
    }

    /**
     * Perform a rollback within the database.
	 * 在数据库中执行回滚
     *
     * @param  int  $toLevel
     * @return void
     */
    protected function performRollBack($toLevel)
    {
        if ($toLevel == 0) {
            $this->getPdo()->rollBack();
        } elseif ($this->queryGrammar->supportsSavepoints()) {
            $this->getPdo()->exec(
                $this->queryGrammar->compileSavepointRollBack('trans'.($toLevel + 1))
            );
        }
    }

    /**
     * Handle an exception from a rollback.
	 * 处理回滚的异常
     *
     * @param \Exception  $e
     *
     * @throws \Exception
     */
    protected function handleRollBackException($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->transactions = 0;
        }

        throw $e;
    }

    /**
     * Get the number of active transactions.
	 * 获取活动事务的数量
     *
     * @return int
     */
    public function transactionLevel()
    {
        return $this->transactions;
    }
}
