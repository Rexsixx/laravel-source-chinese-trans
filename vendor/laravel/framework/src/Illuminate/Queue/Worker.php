<?php
/**
 * Illuminate，队列，工作线程
 */

namespace Illuminate\Queue;

use Exception;
use Throwable;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DetectsLostConnections;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Contracts\Cache\Repository as CacheContract;

class Worker
{
    use DetectsLostConnections;

    /**
     * The queue manager instance.
	 * 队列管理器实例
     *
     * @var \Illuminate\Queue\QueueManager
     */
    protected $manager;

    /**
     * The event dispatcher instance.
	 * 事件调度程序实例
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
	 * 缓存存储库实现
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
	 * 异常处理程序实例
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * Indicates if the worker should exit.
	 * 指示工作线程是否应该退出
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * Indicates if the worker is paused.
	 * 指示工作线程是否暂停
     *
     * @var bool
     */
    public $paused = false;

    /**
     * Create a new queue worker.
	 * 创建一个新的队列工作线程
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptions
     * @return void
     */
    public function __construct(QueueManager $manager,
                                Dispatcher $events,
                                ExceptionHandler $exceptions)
    {
        $this->events = $events;
        $this->manager = $manager;
        $this->exceptions = $exceptions;
    }

    /**
     * Listen to the given queue in a loop.
	 * 在循环中监听给定队列
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function daemon($connectionName, $queue, WorkerOptions $options)
    {
        if ($this->supportsAsyncSignals()) {
            $this->listenForSignals();
        }

        $lastRestart = $this->getTimestampOfLastQueueRestart();

        while (true) {
            // Before reserving any jobs, we will make sure this queue is not paused and
            // if it is we will just pause this worker for a given amount of time and
            // make sure we do not need to kill this worker process off completely.
			// 在安排任何工作之前，我们会先确认这个队列没有被暂停。如果确实处于暂停状态，
			// 我们会让这个工作进程暂停一段时间，以确保我们无需完全终止这个工作进程。
            if (! $this->daemonShouldRun($options, $connectionName, $queue)) {
                $this->pauseWorker($options, $lastRestart);

                continue;
            }

            // First, we will attempt to get the next job off of the queue. We will also
            // register the timeout handler and reset the alarm for this job so it is
            // not stuck in a frozen state forever. Then, we can fire off this job.
			// 首先，我们将尝试从队列中取出下一个任务。同时，我们还会注册超时处理程序，
			// 并为该任务重置警报，以避免其永远处于冻结状态。然后，我们可以启动这个任务。
            $job = $this->getNextJob(
                $this->manager->connection($connectionName), $queue
            );

            if ($this->supportsAsyncSignals()) {
                $this->registerTimeoutHandler($job, $options);
            }

            // If the daemon should run (not in maintenance mode, etc.), then we can run
            // fire off this job for processing. Otherwise, we will need to sleep the
            // worker so no more jobs are processed until they should be processed.
			// 如果守护进程正在运行（并非处于维护模式等状态），那么我们就可以启动并执行这项任务进行处理。
			// 否则，我们就需要让该工人进入休眠状态，这样在他们被唤醒之前就不会再处理任何任务了。
            if ($job) {
                $this->runJob($job, $connectionName, $options);
            } else {
                $this->sleep($options->sleep);
            }

            // Finally, we will check to see if we have exceeded our memory limits or if
            // the queue should restart based on other indications. If so, we'll stop
            // this worker and let whatever is "monitoring" it restart the process.
			// 最后，我们将检查是否已超出内存限制，或者根据其他指示来判断是否需要重新启动队列。
			// 如果是这样的话，我们就会让这个线程停下来，然后让负责“监控”的人员重新启动这个流程。
            $this->stopIfNecessary($options, $lastRestart, $job);
        }
    }

    /**
     * Register the worker timeout handler.
	 * 注册工作超时处理程序
     *
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function registerTimeoutHandler($job, WorkerOptions $options)
    {
        // We will register a signal handler for the alarm signal so that we can kill this
        // process if it is running too long because it has frozen. This uses the async
        // signals supported in recent versions of PHP to accomplish it conveniently.
		// 我们将为警报信号注册一个信号处理程序，以便在该进程运行时间过长且出现卡顿时终止它。
		// 这利用了 PHP 在较新版本中支持的异步信号来实现这一目的，操作起来十分方便。
        pcntl_signal(SIGALRM, function () use ($job, $options) {
            if ($job) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $job->getConnectionName(), $job, (int) $options->maxTries, $this->maxAttemptsExceededException($job)
                );
            }

            $this->kill(1);
        });

        pcntl_alarm(
            max($this->timeoutForJob($job, $options), 0)
        );
    }

    /**
     * Get the appropriate timeout for the given job.
	 * 获取给定作业的适当超时
     *
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return int
     */
    protected function timeoutForJob($job, WorkerOptions $options)
    {
        return $job && ! is_null($job->timeout()) ? $job->timeout() : $options->timeout;
    }

    /**
     * Determine if the daemon should process on this iteration.
	 * 确定守护进程是否应该在此迭代中进行处理
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  string  $connectionName
     * @param  string  $queue
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options, $connectionName, $queue)
    {
        return ! (($this->manager->isDownForMaintenance() && ! $options->force) ||
            $this->paused ||
            $this->events->until(new Events\Looping($connectionName, $queue)) === false);
    }

    /**
     * Pause the worker for the current loop.
	 * 为当前循环暂停工作线程
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @return void
     */
    protected function pauseWorker(WorkerOptions $options, $lastRestart)
    {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);

        $this->stopIfNecessary($options, $lastRestart);
    }

    /**
     * Stop the process if necessary.
	 * 如有必要，请停止该进程。
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @param  mixed  $job
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart, $job = null)
    {
        if ($this->shouldQuit) {
            $this->stop();
        } elseif ($this->memoryExceeded($options->memory)) {
            $this->stop(12);
        } elseif ($this->queueShouldRestart($lastRestart)) {
            $this->stop();
        } elseif ($options->stopWhenEmpty && is_null($job)) {
            $this->stop();
        }
    }

    /**
     * Process the next job on the queue.
	 * 处理队列上的下一个作业
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function runNextJob($connectionName, $queue, WorkerOptions $options)
    {
        $job = $this->getNextJob(
            $this->manager->connection($connectionName), $queue
        );

        // If we're able to pull a job off of the stack, we will process it and then return
        // from this method. If there is no job on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing jobs after sleep.
		// 如果我们能够从队列中取出一项任务，我们将对其进行处理，然后从这个方法中返回。
		// 如果队列中没有任务，我们将让工作进程休眠指定的秒数，然后在休眠结束后继续处理任务。
        if ($job) {
            return $this->runJob($job, $connectionName, $options);
        }

        $this->sleep($options->sleep);
    }

    /**
     * Get the next job from the queue connection.
	 * 从队列连接中获取下一个作业
     *
     * @param  \Illuminate\Contracts\Queue\Queue  $connection
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    protected function getNextJob($connection, $queue)
    {
        try {
            foreach (explode(',', $queue) as $queue) {
                if (! is_null($job = $connection->pop($queue))) {
                    return $job;
                }
            }
        } catch (Exception $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);

            $this->sleep(1);
        } catch (Throwable $e) {
            $this->exceptions->report($e = new FatalThrowableError($e));

            $this->stopWorkerIfLostConnection($e);

            $this->sleep(1);
        }
    }

    /**
     * Process the given job.
	 * 处理给定的作业
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  string  $connectionName
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    protected function runJob($job, $connectionName, WorkerOptions $options)
    {
        try {
            return $this->process($connectionName, $job, $options);
        } catch (Exception $e) {
            $this->exceptions->report($e);

            $this->stopWorkerIfLostConnection($e);
        } catch (Throwable $e) {
            $this->exceptions->report($e = new FatalThrowableError($e));

            $this->stopWorkerIfLostConnection($e);
        }
    }

    /**
     * Stop the worker if we have lost connection to a database.
	 * 如果我们失去了与数据库的连接，则停止worker。
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function stopWorkerIfLostConnection($e)
    {
        if ($this->causedByLostConnection($e)) {
            $this->shouldQuit = true;
        }
    }

    /**
     * Process the given job from the queue.
	 * 从队列中处理给定的作业
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     *
     * @throws \Throwable
     */
    public function process($connectionName, $job, WorkerOptions $options)
    {
        try {
            // First we will raise the before job event and determine if the job has already ran
            // over its maximum attempt limits, which could primarily happen when this job is
            // continually timing out and not actually throwing any exceptions from itself.
			// 首先，我们将触发之前的任务事件，并检查该任务是否已超出其最大尝试次数限制。
			// 这种情况通常会在该任务持续超时且自身未抛出任何异常时发生。
            $this->raiseBeforeJobEvent($connectionName, $job);

            $this->markJobAsFailedIfAlreadyExceedsMaxAttempts(
                $connectionName, $job, (int) $options->maxTries
            );

            if ($job->isDeleted()) {
                return $this->raiseAfterJobEvent($connectionName, $job);
            }

            // Here we will fire off the job and let it process. We will catch any exceptions so
            // they can be reported to the developers logs, etc. Once the job is finished the
            // proper events will be fired to let any listeners know this job has finished.
			// 接下来我们将启动任务并让其开始运行。我们会捕获任何异常情况，以便将其记录到开发人员的日志中等。
			// 一旦任务完成，将会触发相应的事件，让任何监听者知晓该任务已结束。
            $job->fire();

            $this->raiseAfterJobEvent($connectionName, $job);
        } catch (Exception $e) {
            $this->handleJobException($connectionName, $job, $options, $e);
        } catch (Throwable $e) {
            $this->handleJobException(
                $connectionName, $job, $options, new FatalThrowableError($e)
            );
        }
    }

    /**
     * Handle an exception that occurred while the job was running.
	 * 处理作业运行时发生的异常
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  \Exception  $e
     * @return void
     *
     * @throws \Exception
     */
    protected function handleJobException($connectionName, $job, WorkerOptions $options, $e)
    {
        try {
            // First, we will go ahead and mark the job as failed if it will exceed the maximum
            // attempts it is allowed to run the next time we process it. If so we will just
            // go ahead and mark it as failed now so we do not have to release this again.
			// 首先，如果该任务的运行次数超过其允许的最大次数（下次处理该任务时会再次限制次数），我们将将其标记为失败。
			// 如果是这种情况，我们就会立即将其标记为失败，这样就不必再次进行释放操作了。
            if (! $job->hasFailed()) {
                $this->markJobAsFailedIfWillExceedMaxAttempts(
                    $connectionName, $job, (int) $options->maxTries, $e
                );
            }

            $this->raiseExceptionOccurredJobEvent(
                $connectionName, $job, $e
            );
        } finally {
            // If we catch an exception, we will attempt to release the job back onto the queue
            // so it is not lost entirely. This'll let the job be retried at a later time by
            // another listener (or this same one). We will re-throw this exception after.
			// 如果出现异常情况，我们将尝试将任务重新放回队列中，以免任务完全丢失。
			// 这样，之后的监听者（或者还是这个监听者）就能在稍后重新执行该任务。之后我们会再次抛出这个异常。
            if (! $job->isDeleted() && ! $job->isReleased() && ! $job->hasFailed()) {
                $job->release(
                    method_exists($job, 'delaySeconds') && ! is_null($job->delaySeconds())
                                ? $job->delaySeconds()
                                : $options->delay
                );
            }
        }

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
	 * 如果给定的作业超过了允许的最大尝试次数，则将其标记为失败。
     *
     * This will likely be because the job previously exceeded a timeout.
	 * 这可能是因为作业之前超过了超时时间
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  int  $maxTries
     * @return void
     */
    protected function markJobAsFailedIfAlreadyExceedsMaxAttempts($connectionName, $job, $maxTries)
    {
        $maxTries = ! is_null($job->maxTries()) ? $job->maxTries() : $maxTries;

        $timeoutAt = $job->timeoutAt();

        if ($timeoutAt && Carbon::now()->getTimestamp() <= $timeoutAt) {
            return;
        }

        if (! $timeoutAt && ($maxTries === 0 || $job->attempts() <= $maxTries)) {
            return;
        }

        $this->failJob($job, $e = $this->maxAttemptsExceededException($job));

        throw $e;
    }

    /**
     * Mark the given job as failed if it has exceeded the maximum allowed attempts.
	 * 如果给定的作业超过了允许的最大尝试次数，则将其标记为失败。
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  int  $maxTries
     * @param  \Exception  $e
     * @return void
     */
    protected function markJobAsFailedIfWillExceedMaxAttempts($connectionName, $job, $maxTries, $e)
    {
        $maxTries = ! is_null($job->maxTries()) ? $job->maxTries() : $maxTries;

        if ($job->timeoutAt() && $job->timeoutAt() <= Carbon::now()->getTimestamp()) {
            $this->failJob($job, $e);
        }

        if ($maxTries > 0 && $job->attempts() >= $maxTries) {
            $this->failJob($job, $e);
        }
    }

    /**
     * Mark the given job as failed and raise the relevant event.
	 * 将给定的作业标记为失败，并引发相关事件。
     *
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Exception  $e
     * @return void
     */
    protected function failJob($job, $e)
    {
        return $job->fail($e);
    }

    /**
     * Raise the before queue job event.
	 * 引发before队列作业事件
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseBeforeJobEvent($connectionName, $job)
    {
        $this->events->dispatch(new Events\JobProcessing(
            $connectionName, $job
        ));
    }

    /**
     * Raise the after queue job event.
	 * 引发队列后作业事件
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @return void
     */
    protected function raiseAfterJobEvent($connectionName, $job)
    {
        $this->events->dispatch(new Events\JobProcessed(
            $connectionName, $job
        ));
    }

    /**
     * Raise the exception occurred queue job event.
	 * 引发异常发生的队列作业事件
     *
     * @param  string  $connectionName
     * @param  \Illuminate\Contracts\Queue\Job  $job
     * @param  \Exception  $e
     * @return void
     */
    protected function raiseExceptionOccurredJobEvent($connectionName, $job, $e)
    {
        $this->events->dispatch(new Events\JobExceptionOccurred(
            $connectionName, $job, $e
        ));
    }

    /**
     * Determine if the queue worker should restart.
	 * 确定队列工作线程是否应该重新启动
     *
     * @param  int|null  $lastRestart
     * @return bool
     */
    protected function queueShouldRestart($lastRestart)
    {
        return $this->getTimestampOfLastQueueRestart() != $lastRestart;
    }

    /**
     * Get the last queue restart timestamp, or null.
	 * 获取最后一次队列重启时间戳，或null。
     *
     * @return int|null
     */
    protected function getTimestampOfLastQueueRestart()
    {
        if ($this->cache) {
            return $this->cache->get('illuminate:queue:restart');
        }
    }

    /**
     * Enable async signals for the process.
	 * 为进程启用异步信号
     *
     * @return void
     */
    protected function listenForSignals()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, function () {
            $this->shouldQuit = true;
        });

        pcntl_signal(SIGUSR2, function () {
            $this->paused = true;
        });

        pcntl_signal(SIGCONT, function () {
            $this->paused = false;
        });
    }

    /**
     * Determine if "async" signals are supported.
	 * 确定是否支持“async”信号
     *
     * @return bool
     */
    protected function supportsAsyncSignals()
    {
        return extension_loaded('pcntl');
    }

    /**
     * Determine if the memory limit has been exceeded.
	 * 确定是否已超过内存限制
     *
     * @param  int   $memoryLimit
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage(true) / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
	 * 别再听了，跳出剧本。
     *
     * @param  int  $status
     * @return void
     */
    public function stop($status = 0)
    {
        $this->events->dispatch(new Events\WorkerStopping($status));

        exit($status);
    }

    /**
     * Kill the process.
	 * 结束进程
     *
     * @param  int  $status
     * @return void
     */
    public function kill($status = 0)
    {
        $this->events->dispatch(new Events\WorkerStopping($status));

        if (extension_loaded('posix')) {
            posix_kill(getmypid(), SIGKILL);
        }

        exit($status);
    }

    /**
     * Create an instance of MaxAttemptsExceededException.
	 * 创建MaxAttemptsExceededException实例
     *
     * @param  \Illuminate\Contracts\Queue\Job|null  $job
     * @return \Illuminate\Queue\MaxAttemptsExceededException
     */
    protected function maxAttemptsExceededException($job)
    {
        return new MaxAttemptsExceededException(
            $job->resolveName().' has been attempted too many times or run too long. The job may have previously timed out.'
        );
    }

    /**
     * Sleep the script for a given number of seconds.
	 * 让脚本休眠给定的秒数
     *
     * @param  int|float   $seconds
     * @return void
     */
    public function sleep($seconds)
    {
        if ($seconds < 1) {
            usleep($seconds * 1000000);
        } else {
            sleep($seconds);
        }
    }

    /**
     * Set the cache repository implementation.
	 * 设置缓存存储库实现
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     * @return void
     */
    public function setCache(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the queue manager instance.
	 * 获取队列管理器实例
     *
     * @return \Illuminate\Queue\QueueManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Set the queue manager instance.
	 * 设置队列管理器实例
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function setManager(QueueManager $manager)
    {
        $this->manager = $manager;
    }
}
