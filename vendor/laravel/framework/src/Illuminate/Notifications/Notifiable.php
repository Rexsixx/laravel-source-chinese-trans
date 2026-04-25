<?php
/**
 * Illuminate，电子邮件，应通知的
 */

namespace Illuminate\Notifications;

trait Notifiable
{
    use HasDatabaseNotifications, RoutesNotifications;
}
