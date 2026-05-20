<?php
/**
 * Illuminate，电子邮件，等待邮件
 */

namespace Illuminate\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;

class PendingMail
{
    /**
     * The mailer instance.
	 * 邮件实例
     *
     * @var \Illuminate\Mail\Mailer
     */
    protected $mailer;

    /**
     * The locale of the message.
	 * 消息的区域设置
     *
     * @var array
     */
    protected $locale;

    /**
     * The "to" recipients of the message.
	 * 消息的“to”收件人
     *
     * @var array
     */
    protected $to = [];

    /**
     * The "cc" recipients of the message.
	 * 邮件的“抄送”收件人
     *
     * @var array
     */
    protected $cc = [];

    /**
     * The "bcc" recipients of the message.
	 * 消息的“密件抄送”收件人
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * Create a new mailable mailer instance.
	 * 创建一个新的可邮件邮件实例
     *
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set the locale of the message.
	 * 设置消息的区域设置
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Set the recipients of the message.
	 * 设置邮件的收件人
     *
     * @param  mixed  $users
     * @return $this
     */
    public function to($users)
    {
        $this->to = $users;

        return $this;
    }

    /**
     * Set the recipients of the message.
	 * 设置邮件的收件人
     *
     * @param  mixed  $users
     * @return $this
     */
    public function cc($users)
    {
        $this->cc = $users;

        return $this;
    }

    /**
     * Set the recipients of the message.
	 * 设置邮件的收件人
     *
     * @param  mixed  $users
     * @return $this
     */
    public function bcc($users)
    {
        $this->bcc = $users;

        return $this;
    }

    /**
     * Send a new mailable message instance.
	 * 发送一个新的可邮件消息实例
     *
     * @param  \Illuminate\Mail\Mailable  $mailable
     * @return mixed
     */
    public function send(Mailable $mailable)
    {
        if ($mailable instanceof ShouldQueue) {
            return $this->queue($mailable);
        }

        return $this->mailer->send($this->fill($mailable));
    }

    /**
     * Send a mailable message immediately.
	 * 立即发送可发送的消息
     *
     * @param  \Illuminate\Mail\Mailable  $mailable
     * @return mixed
     */
    public function sendNow(Mailable $mailable)
    {
        return $this->mailer->send($this->fill($mailable));
    }

    /**
     * Push the given mailable onto the queue.
	 * 将给定的mailable推到队列上
     *
     * @param  \Illuminate\Mail\Mailable  $mailable
     * @return mixed
     */
    public function queue(Mailable $mailable)
    {
        $mailable = $this->fill($mailable);

        if (isset($mailable->delay)) {
            return $this->mailer->later($mailable->delay, $mailable);
        }

        return $this->mailer->queue($mailable);
    }

    /**
     * Deliver the queued message after the given delay.
	 * 在给定延迟后发送队列消息
     *
     * @param  \DateTimeInterface|\DateInterval|int  $delay
     * @param  \Illuminate\Mail\Mailable  $mailable
     * @return mixed
     */
    public function later($delay, Mailable $mailable)
    {
        return $this->mailer->later($delay, $this->fill($mailable));
    }

    /**
     * Populate the mailable with the addresses.
	 * 用地址填充邮件
     *
     * @param  \Illuminate\Mail\Mailable  $mailable
     * @return \Illuminate\Mail\Mailable
     */
    protected function fill(Mailable $mailable)
    {
        return $mailable->to($this->to)
                        ->cc($this->cc)
                        ->bcc($this->bcc)
                        ->locale($this->locale);
    }
}
