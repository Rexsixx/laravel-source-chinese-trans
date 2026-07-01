<?php
/**
 * 配置，hashing
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver	默认哈希驱动程序
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that will be used to hash
    | passwords for your application. By default, the bcrypt algorithm is
    | used; however, you remain free to modify this option if you wish.
	| 此选项控制将用于您的应用程序的散列密码的默认散列驱动器。
	| 默认情况下,使用bcrypt算法;但是,如果您愿意,您可以自由修改该选项。
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options	Bcrypt选项
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Bcrypt algorithm. This will allow you
    | to control the amount of time it takes to hash the given password.
	| 在这里,您可以指定在使用Bcrypt算法时使用的配置选项。
	| 这将允许您控制用于散列给定密码的时间。
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options		Argon选项
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | passwords are hashed using the Argon algorithm. These will allow you
    | to control the amount of time it takes to hash the given password.
	| 在这里,您可以指定在使用氩气算法使用时使用的配置选项。
	| 这些将允许您控制用于散列给定密码的时间。
    |
    */

    'argon' => [
        'memory' => 1024,
        'threads' => 2,
        'time' => 2,
    ],

];
