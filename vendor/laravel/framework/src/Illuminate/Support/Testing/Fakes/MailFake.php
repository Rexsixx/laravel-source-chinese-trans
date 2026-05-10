<?php
/**
 * Illuminate，支持，测试，Fake，Mail Fake
 */

namespace Illuminate\Support\Testing\Fakes;

use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Mail\Mailable;
use PHPUnit\Framework\Assert as PHPUnit;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailFake implements Mailer
{
    /**
     * All of the mailables that have been sent.
	 * 所有被发送的邮差
     *
     * @var array
     */
    protected $mailables = [];

    /**
     * All of the mailables that have been queued.
	 * 所有被排队的邮件
     *
     * @var array
     */
    protected $queuedMailables = [];

    /**
     * Assert if a mailable was sent based on a truth-test callback.
	 * 断言如果通过trutest callback发送邮件
     *
     * @param  string  $mailable
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertSent($mailable, $callback = null)
    {
        if (is_numeric($callback)) {
            return $this->assertSentTimes($mailable, $callback);
        }

        $message = "The expected [{$mailable}] mailable was not sent.";

        if (count($this->queuedMailables) > 0) {
            $message .= ' Did you mean to use assertQueued() instead?';
        }

        PHPUnit::assertTrue(
            $this->sent($mailable, $callback)->count() > 0,
            $message
        );
    }

    /**
     * Assert if a mailable was sent a number of times.
	 * 断言是否邮寄的时间有很多次
     *
     * @param  string  $mailable
     * @param  int  $times
     * @return void
     */
    protected function assertSentTimes($mailable, $times = 1)
    {
        PHPUnit::assertTrue(
            ($count = $this->sent($mailable)->count()) === $times,
            "The expected [{$mailable}] mailable was sent {$count} times instead of {$times} times."
        );
    }

    /**
     * Determine if a mailable was not sent based on a truth-test callback.
	 * 确定是否可以基于trutest callback发送邮件
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotSent($mailable, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->sent($mailable, $callback)->count() === 0,
            "The unexpected [{$mailable}] mailable was sent."
        );
    }

    /**
     * Assert that no mailables were sent.
	 * 断言没有发送邮件
     *
     * @return void
     */
    public function assertNothingSent()
    {
        PHPUnit::assertEmpty($this->mailables, 'Mailables were sent unexpectedly.');
    }

    /**
     * Assert if a mailable was queued based on a truth-test callback.
	 * 断言如果通过对trutest callback来排队,如果发送邮件是排队的。
     *
     * @param  string  $mailable
     * @param  callable|int|null  $callback
     * @return void
     */
    public function assertQueued($mailable, $callback = null)
    {
        if (is_numeric($callback)) {
            return $this->assertQueuedTimes($mailable, $callback);
        }

        PHPUnit::assertTrue(
            $this->queued($mailable, $callback)->count() > 0,
            "The expected [{$mailable}] mailable was not queued."
        );
    }

    /**
     * Assert if a mailable was queued a number of times.
	 * 断言如果一个邮件被排队了数次
     *
     * @param  string  $mailable
     * @param  int  $times
     * @return void
     */
    protected function assertQueuedTimes($mailable, $times = 1)
    {
        PHPUnit::assertTrue(
            ($count = $this->queued($mailable)->count()) === $times,
            "The expected [{$mailable}] mailable was queued {$count} times instead of {$times} times."
        );
    }

    /**
     * Determine if a mailable was not queued based on a truth-test callback.
	 * 确定是否基于trutest callback而不排队
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return void
     */
    public function assertNotQueued($mailable, $callback = null)
    {
        PHPUnit::assertTrue(
            $this->queued($mailable, $callback)->count() === 0,
            "The unexpected [{$mailable}] mailable was queued."
        );
    }

    /**
     * Assert that no mailables were queued.
	 * 断言没有收件人排队
     *
     * @return void
     */
    public function assertNothingQueued()
    {
        PHPUnit::assertEmpty($this->queuedMailables, 'Mailables were queued unexpectedly.');
    }

    /**
     * Get all of the mailables matching a truth-test callback.
	 * 获取所有匹配超测试回调的邮差
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function sent($mailable, $callback = null)
    {
        if (! $this->hasSent($mailable)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->mailablesOf($mailable)->filter(function ($mailable) use ($callback) {
            return $callback($mailable);
        });
    }

    /**
     * Determine if the given mailable has been sent.
	 * 确定是否发送了邮件
     *
     * @param  string  $mailable
     * @return bool
     */
    public function hasSent($mailable)
    {
        return $this->mailablesOf($mailable)->count() > 0;
    }

    /**
     * Get all of the queued mailables matching a truth-test callback.
	 * 获取匹配一个trutest callback的排队邮件
     *
     * @param  string  $mailable
     * @param  callable|null  $callback
     * @return \Illuminate\Support\Collection
     */
    public function queued($mailable, $callback = null)
    {
        if (! $this->hasQueued($mailable)) {
            return collect();
        }

        $callback = $callback ?: function () {
            return true;
        };

        return $this->queuedMailablesOf($mailable)->filter(function ($mailable) use ($callback) {
            return $callback($mailable);
        });
    }

    /**
     * Determine if the given mailable has been queued.
	 * 确定给定的mailable是否已经排队
     *
     * @param  string  $mailable
     * @return bool
     */
    public function hasQueued($mailable)
    {
        return $this->queuedMailablesOf($mailable)->count() > 0;
    }

    /**
     * Get all of the mailed mailables for a given type.
	 * 把所有寄给的邮差都寄给一个给定的类型
     *
     * @param  string  $type
     * @return \Illuminate\Support\Collection
     */
    protected function mailablesOf($type)
    {
        return collect($this->mailables)->filter(function ($mailable) use ($type) {
            return $mailable instanceof $type;
        });
    }

    /**
     * Get all of the mailed mailables for a given type.
	 * 把所有寄给的邮差都寄给一个给定的类型
     *
     * @param  string  $type
     * @return \Illuminate\Support\Collection
     */
    protected function queuedMailablesOf($type)
    {
        return collect($this->queuedMailables)->filter(function ($mailable) use ($type) {
            return $mailable instanceof $type;
        });
    }

    /**
     * Begin the process of mailing a mailable class instance.
	 * 开始发送一个可邮寄的类实例的过程
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function to($users)
    {
        return (new PendingMailFake($this))->to($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
	 * 开始发送一个可邮寄的类实例的过程
     *
     * @param  mixed  $users
     * @return \Illuminate\Mail\PendingMail
     */
    public function bcc($users)
    {
        return (new PendingMailFake($this))->bcc($users);
    }

    /**
     * Send a new message when only a raw text part.
	 * 当只有原始文本部分时,发送一个新消息。
     *
     * @param  string  $text
     * @param  \Closure|string  $callback
     * @return int
     */
    public function raw($text, $callback)
    {
        //
    }

    /**
     * Send a new message using a view.
	 * 使用视图发送新消息
     *
     * @param  string|array  $view
     * @param  array  $data
     * @param  \Closure|string  $callback
     * @return void
     */
    public function send($view, array $data = [], $callback = null)
    {
        if (! $view instanceof Mailable) {
            return;
        }

        if ($view instanceof ShouldQueue) {
            return $this->queue($view, $data);
        }

        $this->mailables[] = $view;
    }

    /**
     * Queue a new e-mail message for sending.
	 * 排队发送新的电子邮件信息
     *
     * @param  string|array  $view
     * @param  string|null  $queue
     * @return mixed
     */
    public function queue($view, $queue = null)
    {
        if (! $view instanceof Mailable) {
            return;
        }

        $this->queuedMailables[] = $view;
    }

    /**
     * Get the array of failed recipients.
	 * 获取失败收件人的数组
     *
     * @return array
     */
    public function failures()
    {
        return [];
    }
}
