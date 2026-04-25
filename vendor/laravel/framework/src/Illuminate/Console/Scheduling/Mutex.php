<?php
/**
 * Illuminate，控制台，线程调度，互斥
 */

namespace Illuminate\Console\Scheduling;

interface Mutex
{
    /**
     * Attempt to obtain a mutex for the given event.
	 * 尝试获取给定事件的互斥锁
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return bool
     */
    public function create(Event $event);

    /**
     * Determine if a mutex exists for the given event.
	 * 确定给定事件是否存在互斥锁
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return bool
     */
    public function exists(Event $event);

    /**
     * Clear the mutex for the given event.
	 * 清除给定事件的互斥锁
     *
     * @param  \Illuminate\Console\Scheduling\Event  $event
     * @return void
     */
    public function forget(Event $event);
}
