<?php
/**
 * Illuminate，广播，广播事件
 */

namespace Illuminate\Broadcasting;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Broadcasting\Broadcaster;

class BroadcastEvent implements ShouldQueue
{
    use Queueable;

    /**
     * The event instance.
	 * 事件实例 
     *
     * @var mixed
     */
    public $event;

    /**
     * Create a new job handler instance.
	 * 创建一个新的作业处理程序实例
     *
     * @param  mixed  $event
     * @return void
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Handle the queued job.
	 * 处理排队作业
     *
     * @param  \Illuminate\Contracts\Broadcasting\Broadcaster  $broadcaster
     * @return void
     */
    public function handle(Broadcaster $broadcaster)
    {
        $name = method_exists($this->event, 'broadcastAs')
                ? $this->event->broadcastAs() : get_class($this->event);

        $broadcaster->broadcast(
            Arr::wrap($this->event->broadcastOn()), $name,
            $this->getPayloadFromEvent($this->event)
        );
    }

    /**
     * Get the payload for the given event.
	 * 获取给定事件的有效负载
     *
     * @param  mixed  $event
     * @return array
     */
    protected function getPayloadFromEvent($event)
    {
        if (method_exists($event, 'broadcastWith')) {
            return array_merge(
                $event->broadcastWith(), ['socket' => data_get($event, 'socket')]
            );
        }

        $payload = [];

        foreach ((new ReflectionClass($event))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $payload[$property->getName()] = $this->formatProperty($property->getValue($event));
        }

        unset($payload['broadcastQueue']);

        return $payload;
    }

    /**
     * Format the given value for a property.
	 * 为属性设置给定值的格式
     *
     * @param  mixed  $value
     * @return mixed
     */
    protected function formatProperty($value)
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        return $value;
    }

    /**
     * Get the display name for the queued job.
	 * 获取排队作业的显示名称
     *
     * @return string
     */
    public function displayName()
    {
        return get_class($this->event);
    }

    /**
     * Prepare the instance for cloning.
	 * 为克隆准备实例
     *
     * @return void
     */
    public function __clone()
    {
        $this->event = clone $this->event;
    }
}
