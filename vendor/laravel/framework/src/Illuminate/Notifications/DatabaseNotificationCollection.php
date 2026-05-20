<?php
/**
 * Illuminate，通知，数据库通知收集
 */

namespace Illuminate\Notifications;

use Illuminate\Database\Eloquent\Collection;

class DatabaseNotificationCollection extends Collection
{
    /**
     * Mark all notifications as read.
	 * 将标记所有通知为已读
     *
     * @return void
     */
    public function markAsRead()
    {
        $this->each(function ($notification) {
            $notification->markAsRead();
        });
    }

    /**
     * Mark all notifications as unread.
	 * 标记所有通知为未读
     *
     * @return void
     */
    public function markAsUnread()
    {
        $this->each(function ($notification) {
            $notification->markAsUnread();
        });
    }
}
