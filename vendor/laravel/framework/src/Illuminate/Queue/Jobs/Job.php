<?php
/**
 * Illuminate，队列，作业，Job
 */

namespace Illuminate\Queue\Jobs;

use Illuminate\Support\InteractsWithTime;

abstract class Job
{
    use InteractsWithTime;

    /**
     * The job handler instance.
	 * 作业处理程序实例
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The IoC container instance.
	 * IoC容器实例
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Indicates if the job has been deleted.
	 * 指示作业是否已删除
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Indicates if the job has been released.
	 * 指示作业是否已释放
     *
     * @var bool
     */
    protected $released = false;

    /**
     * Indicates if the job has failed.
	 * 指示作业是否失败
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The name of the connection the job belongs to.
	 * 该工作所属的连接名称
     */
    protected $connectionName;

    /**
     * The name of the queue the job belongs to.
	 * 工作所属的队列的名称
     *
     * @var string
     */
    protected $queue;

    /**
     * Get the job identifier.
	 * 获取工作标识符
     *
     * @return string
     */
    abstract public function getJobId();

    /**
     * Get the raw body of the job.
	 * 得到工作的原始身体
     *
     * @return string
     */
    abstract public function getRawBody();

    /**
     * Fire the job.
	 * 解雇工作
     *
     * @return void
     */
    public function fire()
    {
        $payload = $this->payload();

        [$class, $method] = JobName::parse($payload['job']);

        ($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    }

    /**
     * Delete the job from the queue.
	 * 从队列中删除工作
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * Determine if the job has been deleted.
	 * 确定作业是否已删除
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Release the job back into the queue.
	 * 将作业释放回队列
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        $this->released = true;
    }

    /**
     * Determine if the job was released back into the queue.
	 * 确定作业是否被释放回队列
     *
     * @return bool
     */
    public function isReleased()
    {
        return $this->released;
    }

    /**
     * Determine if the job has been deleted or released.
	 * 确定工作是否已被删除或发布
     *
     * @return bool
     */
    public function isDeletedOrReleased()
    {
        return $this->isDeleted() || $this->isReleased();
    }

    /**
     * Determine if the job has been marked as a failure.
	 * 确定工作是否被标记为失败
     *
     * @return bool
     */
    public function hasFailed()
    {
        return $this->failed;
    }

    /**
     * Mark the job as "failed".
	 * 标记作业为“失败”
     *
     * @return void
     */
    public function markAsFailed()
    {
        $this->failed = true;
    }

    /**
     * Process an exception that caused the job to fail.
	 * 流程一个导致作业失败的异常
     *
     * @param  \Exception  $e
     * @return void
     */
    public function failed($e)
    {
        $this->markAsFailed();

        $payload = $this->payload();

        [$class, $method] = JobName::parse($payload['job']);

        if (method_exists($this->instance = $this->resolve($class), 'failed')) {
            $this->instance->failed($payload['data'], $e);
        }
    }

    /**
     * Resolve the given class.
	 * 解析给定的类
     *
     * @param  string  $class
     * @return mixed
     */
    protected function resolve($class)
    {
        return $this->container->make($class);
    }

    /**
     * Get the decoded body of the job.
	 * 获得作业的解码体
     *
     * @return array
     */
    public function payload()
    {
        return json_decode($this->getRawBody(), true);
    }

    /**
     * Get the number of times to attempt a job.
	 * 获得作业的次数
     *
     * @return int|null
     */
    public function maxTries()
    {
        return $this->payload()['maxTries'] ?? null;
    }

    /**
     * Get the number of seconds the job can run.
	 * 得到作业的数量
     *
     * @return int|null
     */
    public function timeout()
    {
        return $this->payload()['timeout'] ?? null;
    }

    /**
     * Get the timestamp indicating when the job should timeout.
	 * 获取指示何时应该超时的时间戳
     *
     * @return int|null
     */
    public function timeoutAt()
    {
        return $this->payload()['timeoutAt'] ?? null;
    }

    /**
     * Get the name of the queued job class.
	 * 获取排队作业类的名称
     *
     * @return string
     */
    public function getName()
    {
        return $this->payload()['job'];
    }

    /**
     * Get the resolved name of the queued job class.
	 * 获取排队工作类的解析名称。
     *
     * Resolves the name of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveName()
    {
        return JobName::resolve($this->getName(), $this->payload());
    }

    /**
     * Get the name of the connection the job belongs to.
	 * 获取该工作所属的连接的名称
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connectionName;
    }

    /**
     * Get the name of the queue the job belongs to.
	 * 获取该作业所属队列的名称
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Get the service container instance.
	 * 获取服务容器实例
     *
     * @return \Illuminate\Container\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
