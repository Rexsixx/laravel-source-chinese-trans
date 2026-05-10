<?php
/**
 * Illuminate，支持，测试，Fake，未决邮件 Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\PendingMail;

class PendingMailFake extends PendingMail
{
    /**
     * Create a new instance.
	 * 创建一个新的实例
     *
     * @param  \Illuminate\Support\Testing\Fakes\MailFake  $mailer
     * @return void
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send a new mailable message instance.
	 * 发送一个新的mailable消息实例
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function send(Mailable $mailable)
    {
        return $this->sendNow($mailable);
    }

    /**
     * Send a mailable message immediately.
	 * 立即发送邮件
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function sendNow(Mailable $mailable)
    {
        $this->mailer->send($this->fill($mailable));
    }

    /**
     * Push the given mailable onto the queue.
	 * 将给定的mailable推到队列上
     *
     * @param  \Illuminate\Mail\Mailable $mailable
     * @return mixed
     */
    public function queue(Mailable $mailable)
    {
        return $this->mailer->queue($this->fill($mailable));
    }
}
