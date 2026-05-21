<?php
/**
 * 配置，会话
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver	默认会话驱动程序
    |--------------------------------------------------------------------------
    |
    | This option controls the default session "driver" that will be used on
    | requests. By default, we will use the lightweight native driver but
    | you may specify any of the other wonderful drivers provided here.
	| 此选项用于控制在请求中将使用的默认会话“驱动程序”。
    |
    | Supported: "file", "cookie", "database", "apc",
    |            "memcached", "redis", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime	会话生命周期
    |--------------------------------------------------------------------------
    |
    | Here you may specify the number of minutes that you wish the session
    | to be allowed to remain idle before it expires. If you want them
    | to immediately expire on the browser closing, set that option.
	| 在此您可以指定，在会话未被使用而处于闲置状态的时间达到多少分钟时，该会话就会自动结束。
    |
    */

    'lifetime' => env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption	会话加密
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted before it is stored. All encryption will be run
    | automatically by Laravel and you can use the Session like normal.
	| 此选项能让您轻松设定，即在存储所有会话数据之前，需对其进行加密处理。
    |
    */

    'encrypt' => false,

    /*
    |--------------------------------------------------------------------------
    | Session File Location		会话文件位置
    |--------------------------------------------------------------------------
    |
    | When using the native session driver, we need a location where session
    | files may be stored. A default has been set for you but a different
    | location may be specified. This is only needed for file sessions.
	| 在使用原生会话驱动程序时，我们需要一个可以存放会话文件的位置。
    |
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection	会话数据库连接
    |--------------------------------------------------------------------------
    |
    | When using the "database" or "redis" session drivers, you may specify a
    | connection that should be used to manage these sessions. This should
    | correspond to a connection in your database configuration options.
	| 在使用“数据库”或“redis”会话驱动程序时，您可以指定一个连接来用于管理这些会话。
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Session Database Table	会话数据库表
    |--------------------------------------------------------------------------
    |
    | When using the "database" session driver, you may specify the table we
    | should use to manage the sessions. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
	| 在使用“数据库”会话驱动程序时，您可以指定应使用哪个表来管理会话。
    |
    */

    'table' => 'sessions',

    /*
    |--------------------------------------------------------------------------
    | Session Cache Store	会话缓存存储
    |--------------------------------------------------------------------------
    |
    | When using the "apc" or "memcached" session drivers, you may specify a
    | cache store that should be used for these sessions. This value must
    | correspond with one of the application's configured cache stores.
	| 在使用“apc”或“memcached”这种会话驱动程序时，您可以指定一个缓存存储区域，用于存储这些会话数据。
    |
    */

    'store' => null,

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery	会话清扫Lottery
    |--------------------------------------------------------------------------
    |
    | Some session drivers must manually sweep their storage location to get
    | rid of old sessions from storage. Here are the chances that it will
    | happen on a given request. By default, the odds are 2 out of 100.
	| 某些会话驱动程序必须手动清理其存储位置，以清除存储中的旧会话数据。
    |
    */

    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Name	会话Cookie名称
    |--------------------------------------------------------------------------
    |
    | Here you may change the name of the cookie used to identify a session
    | instance by ID. The name specified here will get used every time a
    | new session cookie is created by the framework for every driver.
	| 在这里，您可以更改用于通过 ID 标识会话实例的 Cookie 的名称。
    |
    */

    'cookie' => env(
        'SESSION_COOKIE',
        str_slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Path	会话Cookie路径
    |--------------------------------------------------------------------------
    |
    | The session cookie path determines the path for which the cookie will
    | be regarded as available. Typically, this will be the root path of
    | your application but you are free to change this when necessary.
	| 会话 Cookie 的路径决定了该 Cookie 被视为有效的访问范围。
    |
    */

    'path' => '/',

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Domain		会话Cookie域
    |--------------------------------------------------------------------------
    |
    | Here you may change the domain of the cookie used to identify a session
    | in your application. This will determine which domains the cookie is
    | available to in your application. A sensible default has been set.
	| 在这里，您可以更改用于标识您应用程序中会话的 Cookie 的所属范围。
    |
    */

    'domain' => env('SESSION_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | HTTPS Only Cookies	HTTPS只有cookie
    |--------------------------------------------------------------------------
    |
    | By setting this option to true, session cookies will only be sent back
    | to the server if the browser has a HTTPS connection. This will keep
    | the cookie from being sent to you if it can not be done securely.
	| 将此选项设置为“真”后，只有当浏览器与服务器建立的是 HTTPS 连接时，才会将会话 cookie 发送回服务器。
    |
    */

    'secure' => env('SESSION_SECURE_COOKIE', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP Access Only	HTTP访问
    |--------------------------------------------------------------------------
    |
    | Setting this value to true will prevent JavaScript from accessing the
    | value of the cookie and the cookie will only be accessible through
    | the HTTP protocol. You are free to modify this option if needed.
	| 将此值设为“真”将阻止 JavaScript 访问该 Cookie 的值，此后该 Cookie 只能通过 HTTP 协议进行访问。
    |
    */

    'http_only' => true,

    /*
    |--------------------------------------------------------------------------
    | Same-Site Cookies		同址Cookies
    |--------------------------------------------------------------------------
    |
    | This option determines how your cookies behave when cross-site requests
    | take place, and can be used to mitigate CSRF attacks. By default, we
    | do not enable this as other CSRF protection services are in place.
	| 此选项决定了在跨站请求发生时您的 Cookie 的行为方式，并可用于防范 CSRF 攻击。
    |
    | Supported: "lax", "strict"
    |
    */

    'same_site' => null,

];
