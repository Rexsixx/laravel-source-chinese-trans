<?php
/**
 * 配置，auth
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults	身份验证默认值
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
	| 此选项可控制您应用程序的默认身份验证“防护”设置以及密码重置功能。
	| 你可以根据需要更改这些默认设置，但它们对大多数应用程序来说都是一个完美的起点。
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards		认证警卫
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
	| 接下来，您可以为您的应用程序定义每一个身份验证守卫。
	| 当然，这里已经为您预设了一个出色的默认配置，该配置使用会话存储以及 Eloquent 用户提供程序。
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
	| 所有身份验证驱动程序都有一个用户提供程序。
	| 这定义了用户如何从数据库或其他用于持久化用户数据的应用程序所使用的存储机制中实际获取。
    |
    | Supported: "session", "token"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers	用户提供者
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
	| 所有身份验证驱动程序都有一个用户提供程序。
	| 这定义了如何从您的数据库或应用程序使用的其他存储机制中检索到用户的数据。
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
	| 如果您有多个用户表或模型,您可以配置代表每个模型/表的多个源。
	| 这些消息来源可能会被分配给你定义的任何额外的身份验证保护。
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],

        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords	重新设置密码
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
	| 如果您在应用程序中存在多个用户表或模型，并且希望根据不同的用户类型设置不同的密码重置设置，
	| 那么您可以指定多个密码重置配置。
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
	| 过期时间是重置令牌应该被认为有效的时间。这个安全特性使令牌保持短命,
	| 所以他们没有足够的时间来猜测。你可以在需要的时候改变它。
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
