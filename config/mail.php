<?php
/**
 * 配置，mail
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver	邮件驱动器
    |--------------------------------------------------------------------------
    |
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
	| Laravel支持SMTP和PHP的“邮件”功能作为发送电子邮件的驱动程序。
    |
    | Supported: "smtp", "sendmail", "mailgun", "mandrill", "ses",
    |            "sparkpost", "log", "array"
    |
    */

    'driver' => env('MAIL_DRIVER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Address		SMTP主机地址
    |--------------------------------------------------------------------------
    |
    | Here you may provide the host address of the SMTP server used by your
    | applications. A default option is provided that is compatible with
    | the Mailgun mail service which will provide reliable deliveries.
	| 在这里,您可以提供应用程序使用的SMTP服务器的主机地址。
    |
    */

    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Port	SMTP主机端口
    |--------------------------------------------------------------------------
    |
    | This is the SMTP port used by your application to deliver e-mails to
    | users of the application. Like the host we have set this value to
    | stay compatible with the Mailgun e-mail application by default.
	| 这是您的应用程序使用的SMTP端口,向应用程序的用户发送电子邮件。
    |
    */

    'port' => env('MAIL_PORT', 587),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address		“全球”地址
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
	| 您可能希望您的应用程序发送的所有电子邮件都从同一个地址发送。
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | E-Mail Encryption Protocol	电子邮件加密协议
    |--------------------------------------------------------------------------
    |
    | Here you may specify the encryption protocol that should be used when
    | the application send e-mail messages. A sensible default using the
    | transport layer security protocol should provide great security.
	| 在这里,您可以指定在应用程序发送电子邮件消息时使用的加密协议。
    |
    */

    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Username	SMTP服务器用户名
    |--------------------------------------------------------------------------
    |
    | If your SMTP server requires a username for authentication, you should
    | set it here. This will get used to authenticate with your server on
    | connection. You may also set the "password" value below this one.
	| 如果您的SMTP服务器需要一个身份验证的用户名,您应该在这里设置它。
    |
    */

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path	Sendmail系统路径
    |--------------------------------------------------------------------------
    |
    | When using the "sendmail" driver to send e-mails, we will need to know
    | the path to where Sendmail lives on this server. A default path has
    | been provided here, which will work well on most of your systems.
	| 当使用“sendmail”驱动程序发送电子邮件时,我们需要知道sendmail生活在这个服务器上的路径。
	| 这里提供了一条默认路径,在大多数系统中都能很好地工作。
    |
    */

    'sendmail' => '/usr/sbin/sendmail -bs',

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings	Markdown邮件设置
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
	| 如果您使用基于Markdown的电子邮件渲染,您可以在这里配置您的主题和组件路径,允许您定制电子邮件的设计。
	| 或者,你可以简单地坚持Laravel的默认值!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channel	日志通道
    |--------------------------------------------------------------------------
    |
    | If you are using the "log" driver, you may specify the logging channel
    | if you prefer to keep mail messages separate from other log entries
    | for simpler reading. Otherwise, the default channel will be used.
	| 如果您使用的是“日志”驱动程序,则可以指定日志通道,如果您更喜欢将邮件消息与其他日志条目分开,以获得更简单的阅读。
	| 否则,将使用默认通道。
    |
    */

    'log_channel' => env('MAIL_LOG_CHANNEL'),

];
