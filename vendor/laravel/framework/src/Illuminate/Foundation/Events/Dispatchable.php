<?php
/**
 * Illuminate，基础，事件，调度单元
 */

namespace Illuminate\Foundation\Events;

trait Dispatchable
{
    /**
     * Dispatch the event with the given arguments.
	 * 使用给定的参数调度事件
     *
     * @return void
     */
    public static function dispatch()
    {
        return event(new static(...func_get_args()));
    }

    /**
     * Broadcast the event with the given arguments.
	 * 使用给定参数广播事件
     *
     * @return \Illuminate\Broadcasting\PendingBroadcast
     */
    public static function broadcast()
    {
        return broadcast(new static(...func_get_args()));
    }
}
