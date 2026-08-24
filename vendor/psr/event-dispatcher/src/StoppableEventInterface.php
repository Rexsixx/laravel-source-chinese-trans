<?php
/**
 * Psr，EventDispatcher，可停止的事件接口
 */

declare(strict_types=1);

namespace Psr\EventDispatcher;

/**
 * An Event whose processing may be interrupted when the event has been handled.
 * 事件处理时可能被中断的事件。
 *
 * A Dispatcher implementation MUST check to determine if an Event
 * is marked as stopped after each listener is called.  If it is then it should
 * return immediately without calling any further Listeners.
 * Dispatcher实现必须检查,以确定在调用每个侦听器后是否会停止一个事件。
 */
interface StoppableEventInterface
{
    /**
     * Is propagation stopped?
	 * 传播停止了吗？
     *
     * This will typically only be used by the Dispatcher to determine if the
     * previous listener halted propagation.
	 * 这通常只由Dispatcher使用,以确定前一个侦听器是否停止传播。
     *
     * @return bool
     *   True if the Event is complete and no further listeners should be called.
     *   False to continue calling listeners.
     */
    public function isPropagationStopped() : bool;
}
