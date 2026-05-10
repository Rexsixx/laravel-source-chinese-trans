<?php
/**
 * Illuminate，支持，测试，Fake，队列 Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Queue\QueueManager;
use Illuminate\Contracts\Queue\Queue;
use PHPUnit\Framework\Assert as PHPUnit;

class QueueFake extends QueueManager implements Queue
{
    /**
     * All of the jobs that have been pushed.
	 * 所有的工作都被推动了
     *
     * @var array
     */
    protected $jobs = [];

    /**
     * Assert if a job was pushed based on a truth-test callback.
	 * 断言如果工作是基于trutest callback的
     *
     * @param  string  $job
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertPushed($job, $callback = null)
    {
        if (is_numeric($callback)) {
            return $this->assertPushedTimes($job, $callback);
        }

        PHPUnit::assertTrue(
            $this->pushed($job, $callback)->count() > 0,
            "The expected [{$job}] job was not pushed."
        );
    }

    /**
     * Assert if a job was pushed a number of times.
	 * 断言是否作业被推了很多次
     *
     * @param  string  $job
     * @param  int  $times
     * @return void
     */
    protected function assertPushedTimes($job, $times = 1)
    {
        PHPUnit::assertTrue(
            ($count = $this->pushed($job)->count()) === $times,
            "The expected [{$job}] job was pushed {$count} times instead of {$times} times."
        );
    }

    /**
     * Assert if a job was pushed based on a truth-test callback.
	 * 断言如果工作是基于trutest callback的
     *
     * @param  string  $queue
     * @param  string  $job
     * @param  callable|null  $callback
     * @return void
     */
    public function assertPushedOn($queue, $job, $callback = null)
    {
        return $this->assertPushed($job, function ($job, $pushedQueue) use ($callback, $queue) {
            if ($pushedQueue !== $queue) {
                return false;
            }

            return $callback ? $callback(...func_get_args()) : true;
        });
    }

    /**
     * Assert if a job was pushed with chained jobs based on a truth-test callback.
	 * 断言如果基于一个tru- test回调,工作就会被链接的工作所推动。
     *
     * @param  string $job
     * @param  array $expectedChain
     * @param  callable|null $callback
     * @return void
     */
    public function assertPushedWithChain($job, $expectedChain = [], $callback = null)
    {
        PHPUnit::assertTrue(
            $this->pushed($job, $callback)->isNotEmpty(),
            "The expected [{$job}] job was not pushed."
        );

        PHPUnit::assertTrue(
            collect($expectedChain)->isNotEmpty(),
            'The expected chain can not be empty.'
        );

        $this->isChainOfObjects($expectedChain)
                ? $this->assertPushedWithChainOfObjects($job, $expectedChain, $callback)
                : $this->assertPushedWithChainOfClasses($job, $expectedChain, $callback);
    }

    /**
     * Assert if a job was pushed with chained jobs based on a truth-test callback.
	 * 断言如果基于一个tru- test回调,工作就会被链接的工作所推动。
     *
     * @param  string $job
     * @param  array $expectedChain
     * @param  callable|null $callback
     * @return void
     */
    protected function assertPushedWithChainOfObjects($job, $expectedChain, $callback)
    {
        $chain = collect($expectedChain)->map(function ($job) {
            return serialize($job);
        })->all();

        PHPUnit::assertTrue(
            $this->pushed($job, $callback)->filter(function ($job) use ($chain) {
                return $job->chained == $chain;
            })->isNotEmpty(),
            'The expected chain was not pushed.'
        );
    }

    /**
     * Assert if a job was pushed with chained jobs based on a truth-test callback.
	 * 断言如果基于一个tru- test回调,工作就会被链接的工作所推动。
     *
     * @param  string $job
     * @param  array $expectedChain
     * @param  callable|null $callback
     * @return void
     */
    protected function assertPushedWithChainOfClasses($job, $expectedChain, $callback)
    {
        $matching = $this->pushed($job, $callback)->map->chained->map(function ($chain) {
            return collect($chain)->map(function ($job) {
                return get_class(unserialize($job));
            });
        })->filter(function ($chain) use ($expectedChain) {
            return $chain->all() === $expectedChain;
        });

        PHPUnit::assertTrue(
            $matching->isNotEmpty(), 'The expected chain was not pushed.'
        );
    }

    /**
     * Determine if the given chain is entirely composed of objects.
	 * 确定给定的链是否完全由对象组成
     *
     * @param  array  $chain
     * @return bool
     */
    protected function isChainOfObjects($chain)
    {
        return collect($chain)->count() == collect($chain)
                    ->filter(function ($job) {
                        return is_object($job);
                    })->count();
    }

    /**
     * Determine if a job was pushed based on a truth-test callback.
	 * 确定是否基于实际测试回调工作
     *
     * @param  string  $job
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotPushed($job, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->pushed($job, $callback)->count() === 0,
            "The unexpected [{$job}] job was pushed."
        );
    }

    /**
     * Assert that no jobs were pushed.
	 * 断言没有任何工作被推动
     *
     * @return void
     */
    public function assertNothingPushed()
    {
        PHPUnit::assertEmpty($this->jobs, 'Jobs were pushed unexpectedly.');
    }

    /**
     * Get all of the jobs matching a truth-test callback.
	 * 让所有的工作都匹配一个truand测试回调
     *
     * @param  string  $job
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function pushed($job, $callback = null)
    {
        if (! $this->hasPushed($job)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return collect($this->jobs[$job])->filter(function ($data) use ($callback) {
            return $callback($data['job'], $data['queue']);
        })->pluck('job');
    }

    /**
     * Determine if there are any stored jobs for a given class.
	 * 用“点”符号从数组中获取一个项目
     *
     * @param  string  $job
     * @return bool
     */
    public function hasPushed($job)
    {
        return isset($this->jobs[$job]) && ! empty($this->jobs[$job]);
    }

    /**
     * Resolve a queue connection instance.
	 * 解决队列连接实例
     *
     * @param  mixed  $value
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connection($value = null)
    {
        return $this;
    }

    /**
     * Get the size of the queue.
	 * 获取队列的大小
     *
     * @param  string  $queue
     * @return int
     */
    public function size($queue = null)
    {
        return count($this->jobs);
    }

    /**
     * Push a new job onto the queue.
	 * 将新工作推到队列上
     *
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
     */
    public function push($job, $data = '', $queue = null)
    {
        $this->jobs[is_object($job) ? get_class($job) : $job][] = [
            'job' => $job,
            'queue' => $queue,
        ];
    }

    /**
     * Push a raw payload onto the queue.
	 * 将原始有效负载推到队列上
     *
     * @param  string  $payload
     * @param  string  $queue
     * @param  array   $options
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        //
    }

    /**
     * Push a new job onto the queue after a delay.
	 * 延迟后将新工作推到队列上
     *
     * @param  \DateTime|int  $delay
     * @param  string  $job
     * @param  mixed   $data
     * @param  string  $queue
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Push a new job onto the queue.
	 * 将新工作推到队列上
     *
     * @param  string  $queue
     * @param  string  $job
     * @param  mixed   $data
     * @return mixed
     */
    public function pushOn($queue, $job, $data = '')
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
	 * 延迟后将新工作推到队列上
     *
     * @param  string  $queue
     * @param  \DateTime|int  $delay
     * @param  string  $job
     * @param  mixed   $data
     * @return mixed
     */
    public function laterOn($queue, $delay, $job, $data = '')
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Pop the next job off of the queue.
	 * 从队列中启动下一个工作
     *
     * @param  string  $queue
     * @return \Illuminate\Contracts\Queue\Job|null
     */
    public function pop($queue = null)
    {
        //
    }

    /**
     * Push an array of jobs onto the queue.
	 * 将一系列工作推到队列上
     *
     * @param  array $jobs
     * @param  mixed $data
     * @param  string $queue
     * @return mixed
     */
    public function bulk($jobs, $data = '', $queue = null)
    {
        foreach ($jobs as $job) {
            $this->push($job, $data, $queue);
        }
    }

    /**
     * Get the connection name for the queue.
	 * 获取队列的连接名
     *
     * @return string
     */
    public function getConnectionName()
    {
        //
    }

    /**
     * Set the connection name for the queue.
	 * 设置队列的连接名
     *
     * @param  string $name
     * @return $this
     */
    public function setConnectionName($name)
    {
        return $this;
    }
}
