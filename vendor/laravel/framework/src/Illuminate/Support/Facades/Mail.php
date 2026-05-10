<?php
/**
 * Illuminate，支持，门面，Facade
 */

namespace Illuminate\Support\Facades;

use Illuminate\Support\Testing\Fakes\MailFake;

/**
 * @method static \Illuminate\Mail\PendingMail to($users)
 * @method static \Illuminate\Mail\PendingMail bcc($users)
 * @method static void raw(string $text, $callback)
 * @method static void send(string|array|\Illuminate\Contracts\Mail\Mailable $view, array $data = [], \Closure|string $callback = null)
 * @method static array failures()
 * @method static mixed queue(string|array|\Illuminate\Contracts\Mail\Mailable $view, string $queue = null)
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, string|array|\Illuminate\Contracts\Mail\Mailable $view, string $queue = null)
 *
 * @see \Illuminate\Mail\Mailer
 */
class Mail extends Facade
{
    /**
     * Replace the bound instance with a fake.
	 * 用假的方式替换绑定的实例
     *
     * @return void
     */
    public static function fake()
    {
        static::swap(new MailFake);
    }

    /**
     * Get the registered name of the component.
	 * 获取组件的注册名称
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mailer';
    }
}
