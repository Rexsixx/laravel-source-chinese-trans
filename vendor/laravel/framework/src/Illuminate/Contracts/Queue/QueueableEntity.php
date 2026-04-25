<?php
/**
 * Illuminate，契约，队列，可排队的实体
 */

namespace Illuminate\Contracts\Queue;

interface QueueableEntity
{
    /**
     * Get the queueable identity for the entity.
	 * 获取实体的可排队标识
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the connection of the entity.
	 * 获取实体的连接
     *
     * @return string|null
     */
    public function getQueueableConnection();
}
