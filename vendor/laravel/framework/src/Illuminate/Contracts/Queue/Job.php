<?php
/**
 * Illuminate，契约，队列，作业
 */

namespace Illuminate\Contracts\Queue;

interface Job
{
    /**
     * Get the job identifier.
	 * 获取工作标识符
     *
     * @return string
     */
    public function getJobId();

    /**
     * Get the decoded body of the job.
	 * 拿到解码后的文件
     *
     * @return array
     */
    public function payload();

    /**
     * Fire the job.
	 * 触发这个作业
     *
     * @return void
     */
    public function fire();

    /**
     * Release the job back into the queue.
	 * 将作业释放回队列。
     *
     * Accepts a delay specified in seconds.
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0);

    /**
     * Delete the job from the queue.
	 * 从队列中删除作业
     *
     * @return void
     */
    public function delete();

    /**
     * Determine if the job has been deleted.
	 * 确定作业是否已删除
     *
     * @return bool
     */
    public function isDeleted();

    /**
     * Determine if the job has been deleted or released.
	 * 确定作业是否已被删除或释放
     *
     * @return bool
     */
    public function isDeletedOrReleased();

    /**
     * Get the number of times the job has been attempted.
	 * 获取该任务被尝试的次数
     *
     * @return int
     */
    public function attempts();

    /**
     * Process an exception that caused the job to fail.
	 * 处理导致作业失败的异常
     *
     * @param  \Throwable  $e
     * @return void
     */
    public function failed($e);

    /**
     * Get the number of times to attempt a job.
	 * 获取尝试某项工作的次数
     *
     * @return int|null
     */
    public function maxTries();

    /**
     * Get the number of seconds the job can run.
	 * 获取作业可以运行的秒数
     *
     * @return int|null
     */
    public function timeout();

    /**
     * Get the timestamp indicating when the job should timeout.
	 * 获取指示作业何时应该超时的时间戳
     *
     * @return int|null
     */
    public function timeoutAt();

    /**
     * Get the name of the queued job class.
	 * 获取排队作业类的名称
     *
     * @return string
     */
    public function getName();

    /**
     * Get the resolved name of the queued job class.
	 * 获取排队作业类的解析名称。
     *
     * Resolves the name of "wrapped" jobs such as class-based handlers.
     *
     * @return string
     */
    public function resolveName();

    /**
     * Get the name of the connection the job belongs to.
	 * 获取作业所属的连接的名称
     *
     * @return string
     */
    public function getConnectionName();

    /**
     * Get the name of the queue the job belongs to.
	 * 获取作业所属队列的名称
     *
     * @return string
     */
    public function getQueue();

    /**
     * Get the raw body string for the job.
	 * 获取工作的原始主体字符串
     *
     * @return string
     */
    public function getRawBody();
}
