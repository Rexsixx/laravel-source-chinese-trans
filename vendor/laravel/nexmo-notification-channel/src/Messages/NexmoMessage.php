<?php
/**
 * Illuminate，通知，信息，Nexmo 消息
 */

namespace Illuminate\Notifications\Messages;

class NexmoMessage
{
    /**
     * The message content.
	 * 消息内容
     *
     * @var string
     */
    public $content;

    /**
     * The phone number the message should be sent from.
	 * 应该发送信息的电话号码
     *
     * @var string
     */
    public $from;

    /**
     * The message type.
	 * 消息类型
     *
     * @var string
     */
    public $type = 'text';

    /**
     * Create a new message instance.
     *
     * @param  string  $content
     * @return void
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }

    /**
     * Set the message content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the phone number the message should be sent from.
     *
     * @param  string  $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Set the message type.
     *
     * @return $this
     */
    public function unicode()
    {
        $this->type = 'unicode';

        return $this;
    }
}
