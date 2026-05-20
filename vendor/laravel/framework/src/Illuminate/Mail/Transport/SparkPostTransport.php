<?php
/**
 * Illuminate，电子邮件，传输，SparkPost 传输
 */

namespace Illuminate\Mail\Transport;

use Swift_Mime_SimpleMessage;
use GuzzleHttp\ClientInterface;

class SparkPostTransport extends Transport
{
    /**
     * Guzzle client instance.
	 * Guzzle客户端实例
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The SparkPost API key.
	 * SparkPost API密钥
     *
     * @var string
     */
    protected $key;

    /**
     * Transmission options.
	 * 传输选项
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new SparkPost transport instance.
	 * 创建一个新的SparkPost传输实例
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @param  array  $options
     * @return void
     */
    public function __construct(ClientInterface $client, $key, $options = [])
    {
        $this->key = $key;
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $recipients = $this->getRecipients($message);

        $message->setBcc([]);

        $response = $this->client->post($this->getEndpoint(), [
            'headers' => [
                'Authorization' => $this->key,
            ],
            'json' => array_merge([
                'recipients' => $recipients,
                'content' => [
                    'email_rfc822' => $message->toString(),
                ],
            ], $this->options),
        ]);

        $message->getHeaders()->addTextHeader(
            'X-SparkPost-Transmission-ID', $this->getTransmissionId($response)
        );

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get all the addresses this message should be sent to.
	 * 获取此消息应发送到的所有地址。
     *
     * Note that SparkPost still respects CC, BCC headers in raw message itself.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getRecipients(Swift_Mime_SimpleMessage $message)
    {
        $recipients = [];

        foreach ((array) $message->getTo() as $email => $name) {
            $recipients[] = ['address' => compact('name', 'email')];
        }

        foreach ((array) $message->getCc() as $email => $name) {
            $recipients[] = ['address' => compact('name', 'email')];
        }

        foreach ((array) $message->getBcc() as $email => $name) {
            $recipients[] = ['address' => compact('name', 'email')];
        }

        return $recipients;
    }

    /**
     * Get the transmission ID from the response.
	 * 从响应中获取传输ID
     *
     * @param  \GuzzleHttp\Psr7\Response  $response
     * @return string
     */
    protected function getTransmissionId($response)
    {
        return object_get(
            json_decode($response->getBody()->getContents()), 'results.id'
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
	 * 设置传输所使用的API密钥。
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }

    /**
     * Get the SparkPost API endpoint.
	 * 获取SparkPost API端点
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getOptions()['endpoint'] ?? 'https://api.sparkpost.com/api/v1/transmissions';
    }

    /**
     * Get the transmission options being used by the transport.
	 * 获取传输所使用的传输选项
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the transmission options being used by the transport.
	 * 设置运输使用的传输选项
     *
     * @param  array  $options
     * @return array
     */
    public function setOptions(array $options)
    {
        return $this->options = $options;
    }
}
