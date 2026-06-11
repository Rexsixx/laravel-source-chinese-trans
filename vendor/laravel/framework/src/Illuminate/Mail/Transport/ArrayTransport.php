<?php
/**
 * Illuminate，电子邮件，传送，数组传送
 */

namespace Illuminate\Mail\Transport;

use Swift_Mime_SimpleMessage;
use Illuminate\Support\Collection;

class ArrayTransport extends Transport
{
    /**
     * The collection of Swift Messages.
	 * Swift消息的集合
     *
     * @var \Illuminate\Support\Collection
     */
    protected $messages;

    /**
     * Create a new array transport instance.
	 * 创建一个新的数组传输实例
     *
     * @return void
     */
    public function __construct()
    {
        $this->messages = new Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $this->messages[] = $message;

        return $this->numberOfRecipients($message);
    }

    /**
     * Retrieve the collection of messages.
	 * 检索消息集合
     *
     * @return \Illuminate\Support\Collection
     */
    public function messages()
    {
        return $this->messages;
    }

    /**
     * Clear all of the messages from the local collection.
	 * 从本地集合中清除所有消息
     *
     * @return \Illuminate\Support\Collection
     */
    public function flush()
    {
        return $this->messages = new Collection;
    }
}
