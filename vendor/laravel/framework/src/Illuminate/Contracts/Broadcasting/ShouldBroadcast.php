<?php
/**
 * Illuminate，契约，广播，应该广播
 */

namespace Illuminate\Contracts\Broadcasting;

use Illuminate\Broadcasting\Channel;

interface ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
	 * 获取该事件应该播放的频道
     *
     * @return Channel|Channel[]
     */
    public function broadcastOn();
}
