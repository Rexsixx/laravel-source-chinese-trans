<?php
/**
 * Illuminate，契约，Mail，可邮寄的
 */

namespace Illuminate\Contracts\Mail;

use Illuminate\Contracts\Mail\Mailable as MailableContract;

interface Mailer
{
    /**
     * Begin the process of mailing a mailable class instance.
	 * 开始邮寄可邮寄类实例的过程
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function to($users);

    /**
     * Begin the process of mailing a mailable class instance.
	 * 开始邮寄可邮寄类实例的过程
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function bcc($users);

    /**
     * Send a new message when only a raw text part.
	 * 发送一个新的消息时，只有一个原始文本部分。
     *
     * @param  string  $text
     * @param  mixed  $callback
     * @return void
     */
    public function raw($text, $callback);

    /**
     * Send a new message using a view.
	 * 使用视图发送新消息
     *
     * @param  string|array|MailableContract  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @return void
     */
    public function send($view, array $data = [], $callback = null);

    /**
     * Get the array of failed recipients.
	 * 获取失败收件人的数组
     *
     * @return array
     */
    public function failures();
}
