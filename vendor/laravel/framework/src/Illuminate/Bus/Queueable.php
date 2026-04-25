<?php
/**
 * Illuminate，总线，可排队的
 */

namespace Illuminate\Bus;

trait Queueable
{
    /**
     * The name of the connection the job should be sent to.
	 * 应该将作业发送到的连接的名称
     *
     * @var string|null
     */
    public $connection;

    /**
     * The name of the queue the job should be sent to.
	 * 应该将作业发送到的队列的名称
     *
     * @var string|null
     */
    public $queue;

    /**
     * The name of the connection the chain should be sent to.
	 * 链应该被发送到的连接的名称
     *
     * @var string|null
     */
    public $chainConnection;

    /**
     * The name of the queue the chain should be sent to.
	 * 链应该被发送到的队列的名称
     *
     * @var string|null
     */
    public $chainQueue;

    /**
     * The number of seconds before the job should be made available.
	 * 在作业可用之前的秒数
     *
     * @var \DateTimeInterface|\DateInterval|int|null
     */
    public $delay;

    /**
     * The jobs that should run if this job is successful.
	 * 如果此作业成功，应该运行的作业。
     *
     * @var array
     */
    public $chained = [];

    /**
     * Set the desired connection for the job.
	 * 为作业设置所需的连接
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function onConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the job.
	 * 为作业设置所需的队列
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function onQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired connection for the chain.
	 * 为链条设置所需的连接
     *
     * @param  string|null  $connection
     * @return $this
     */
    public function allOnConnection($connection)
    {
        $this->chainConnection = $connection;
        $this->connection = $connection;

        return $this;
    }

    /**
     * Set the desired queue for the chain.
	 * 为链设置所需的队列
     *
     * @param  string|null  $queue
     * @return $this
     */
    public function allOnQueue($queue)
    {
        $this->chainQueue = $queue;
        $this->queue = $queue;

        return $this;
    }

    /**
     * Set the desired delay for the job.
	 * 为作业设置所需的延迟
     *
     * @param  \DateTimeInterface|\DateInterval|int|null  $delay
     * @return $this
     */
    public function delay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Set the jobs that should run if this job is successful.
	 * 设置作业成功时应该运行的作业
     *
     * @param  array  $chain
     * @return $this
     */
    public function chain($chain)
    {
        $this->chained = collect($chain)->map(function ($job) {
            return serialize($job);
        })->all();

        return $this;
    }

    /**
     * Dispatch the next job on the chain.
	 * 执行链条上的下一个任务
     *
     * @return void
     */
    public function dispatchNextJobInChain()
    {
        if (! empty($this->chained)) {
            dispatch(tap(unserialize(array_shift($this->chained)), function ($next) {
                $next->chained = $this->chained;

                $next->onConnection($next->connection ?: $this->chainConnection);
                $next->onQueue($next->queue ?: $this->chainQueue);

                $next->chainConnection = $this->chainConnection;
                $next->chainQueue = $this->chainQueue;
            }));
        }
    }
}
