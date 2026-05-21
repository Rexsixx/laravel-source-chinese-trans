<?php
/**
 * 路由，通道
 */

/*
|--------------------------------------------------------------------------
| Broadcast Channels	广播通道
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
| 在这里，您可以登记您的应用程序所支持的所有事件广播通道。
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
