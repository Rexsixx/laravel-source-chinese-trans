<?php
/**
 * Illuminate，电子邮件，传输，Mailgun 传输
 */

namespace Illuminate\Mail\Transport;

use Swift_Mime_SimpleMessage;
use GuzzleHttp\ClientInterface;

class MailgunTransport extends Transport
{
    /**
     * Guzzle client instance.
	 * Guzzle 客户端实例
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The Mailgun API key.
	 * Mailgun API密钥
     *
     * @var string
     */
    protected $key;

    /**
     * The Mailgun domain.
	 * Mailgun域
     *
     * @var string
     */
    protected $domain;

    /**
     * The Mailgun API end-point.
	 * Mailgun API端点
     *
     * @var string
     */
    protected $url;

    /**
     * Create a new Mailgun transport instance.
	 * 创建一个新的Mailgun传输实例
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  string  $domain
     * @return void
     */
    public function __construct(ClientInterface $client, $key, $domain)
    {
        $this->key = $key;
        $this->client = $client;
        $this->setDomain($domain);
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $to = $this->getTo($message);

        $message->setBcc([]);

        $this->client->post($this->url, $this->payload($message, $to));

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the HTTP payload for sending the Mailgun message.
	 * 获取用于发送Mailgun消息的HTTP有效负载
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @param  string  $to
     * @return array
     */
    protected function payload(Swift_Mime_SimpleMessage $message, $to)
    {
        return [
            'auth' => [
                'api',
                $this->key,
            ],
            'multipart' => [
                [
                    'name' => 'to',
                    'contents' => $to,
                ],
                [
                    'name' => 'message',
                    'contents' => $message->toString(),
                    'filename' => 'message.mime',
                ],
            ],
        ];
    }

    /**
     * Get the "to" payload field for the API request.
	 * 获取API请求的“to”有效负载字段
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return string
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return collect($this->allContacts($message))->map(function ($display, $address) {
            return $display ? $display." <{$address}>" : $address;
        })->values()->implode(',');
    }

    /**
     * Get all of the contacts for the message.
	 * 获取该消息的所有联系人
     *
     * @param  \Swift_Mime_SimpleMessage  $message
     * @return array
     */
    protected function allContacts(Swift_Mime_SimpleMessage $message)
    {
        return array_merge(
            (array) $message->getTo(), (array) $message->getCc(), (array) $message->getBcc()
        );
    }

    /**
     * Get the API key being used by the transport.
	 * 获取传输所使用的API密钥
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
	 * 设置被传输使用的API密钥
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }

    /**
     * Get the domain being used by the transport.
	 * 获取被运输所使用的域
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set the domain being used by the transport.
	 * 设置被传输的域
     *
     * @param  string  $domain
     * @return string
     */
    public function setDomain($domain)
    {
        $this->url = 'https://api.mailgun.net/v3/'.$domain.'/messages.mime';

        return $this->domain = $domain;
    }
}
