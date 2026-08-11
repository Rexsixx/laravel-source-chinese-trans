<?php
/**
 * 广播信道
 */

/*
|--------------------------------------------------------------------------
| Broadcast Channels	广播信道
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
| 在此处，您可以注册应用程序支持的所有事件广播频道。
| 给定的频道授权回调用于检查经过身份验证的用户是否可以监听该频道。
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
