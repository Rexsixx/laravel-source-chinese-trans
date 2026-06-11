<?php
/**
 * GuzzleHttp，许诺，任务队列接口
 */

namespace GuzzleHttp\Promise;

interface TaskQueueInterface
{
    /**
     * Returns true if the queue is empty.
	 * 如果队列是空的,返回true。
     *
     * @return bool
     */
    public function isEmpty();

    /**
     * Adds a task to the queue that will be executed the next time run is
     * called.
	 * 将任务添加到队列中，下次调用 run 时会执行该任务。
     */
    public function add(callable $task);

    /**
     * Execute all of the pending task in the queue.
     */
    public function run();
}
