<?php
/**
 * Illuminate，电子邮件，运送，Ses 运送
 */

namespace Illuminate\Mail\Transport;

use Aws\Ses\SesClient;
use Swift_Mime_SimpleMessage;

class SesTransport extends Transport
{
    /**
     * The Amazon SES instance.
	 * Amazon SES实例
     *
     * @var \Aws\Ses\SesClient
     */
    protected $ses;

    /**
     * Create a new SES transport instance.
	 * 创建一个新的SES传输实例
     *
     * @param  \Aws\Ses\SesClient  $ses
     * @return void
     */
    public function __construct(SesClient $ses)
    {
        $this->ses = $ses;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $headers = $message->getHeaders();

        $headers->addTextHeader('X-SES-Message-ID', $this->ses->sendRawEmail([
            'Source' => key($message->getSender() ?: $message->getFrom()),
            'RawMessage' => [
                'Data' => $message->toString(),
            ],
        ])->get('MessageId'));

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }
}
