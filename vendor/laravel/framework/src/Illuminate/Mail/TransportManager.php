<?php
/**
 * Illuminate，电子邮件，传输管理器
 */

namespace Illuminate\Mail;

use Aws\Ses\SesClient;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Illuminate\Support\Manager;
use GuzzleHttp\Client as HttpClient;
use Swift_SmtpTransport as SmtpTransport;
use Illuminate\Mail\Transport\LogTransport;
use Illuminate\Mail\Transport\SesTransport;
use Illuminate\Mail\Transport\ArrayTransport;
use Swift_SendmailTransport as MailTransport;
use Illuminate\Mail\Transport\MailgunTransport;
use Illuminate\Mail\Transport\MandrillTransport;
use Illuminate\Mail\Transport\SparkPostTransport;
use Swift_SendmailTransport as SendmailTransport;

class TransportManager extends Manager
{
    /**
     * Create an instance of the SMTP Swift Transport driver.
	 * 创建SMTP Swift传输驱动程序的实例
     *
     * @return \Swift_SmtpTransport
     */
    protected function createSmtpDriver()
    {
        $config = $this->app->make('config')->get('mail');

        // The Swift SMTP transport instance will allow us to use any SMTP backend
        // for delivering mail such as Sendgrid, Amazon SES, or a custom server
        // a developer has available. We will just pass this configured host.
        $transport = new SmtpTransport($config['host'], $config['port']);

        if (isset($config['encryption'])) {
            $transport->setEncryption($config['encryption']);
        }

        // Once we have the transport we will check for the presence of a username
        // and password. If we have it we will set the credentials on the Swift
        // transporter instance so that we'll properly authenticate delivery.
        if (isset($config['username'])) {
            $transport->setUsername($config['username']);

            $transport->setPassword($config['password']);
        }

        // Next we will set any stream context options specified for the transport
        // and then return it. The option is not required any may not be inside
        // the configuration array at all so we'll verify that before adding.
        if (isset($config['stream'])) {
            $transport->setStreamOptions($config['stream']);
        }

        return $transport;
    }

    /**
     * Create an instance of the Sendmail Swift Transport driver.
	 * 创建Sendmail Swift Transport驱动程序的实例
     *
     * @return \Swift_SendmailTransport
     */
    protected function createSendmailDriver()
    {
        return new SendmailTransport($this->app['config']['mail']['sendmail']);
    }

    /**
     * Create an instance of the Amazon SES Swift Transport driver.
	 * 创建一个Amazon SES Swift Transport驱动程序的实例
     *
     * @return \Illuminate\Mail\Transport\SesTransport
     */
    protected function createSesDriver()
    {
        $config = array_merge($this->app['config']->get('services.ses', []), [
            'version' => 'latest', 'service' => 'email',
        ]);

        return new SesTransport(new SesClient(
            $this->addSesCredentials($config)
        ));
    }

    /**
     * Add the SES credentials to the configuration array.
	 * 将SES凭据添加到配置阵列
     *
     * @param  array  $config
     * @return array
     */
    protected function addSesCredentials(array $config)
    {
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }

    /**
     * Create an instance of the Mail Swift Transport driver.
	 * 创建邮件Swift传输驱动程序的实例
     *
     * @return \Swift_SendmailTransport
     */
    protected function createMailDriver()
    {
        return new MailTransport;
    }

    /**
     * Create an instance of the Mailgun Swift Transport driver.
	 * 创建Mailgun Swift Transport驱动程序的实例
     *
     * @return \Illuminate\Mail\Transport\MailgunTransport
     */
    protected function createMailgunDriver()
    {
        $config = $this->app['config']->get('services.mailgun', []);

        return new MailgunTransport(
            $this->guzzle($config),
            $config['secret'], $config['domain']
        );
    }

    /**
     * Create an instance of the Mandrill Swift Transport driver.
	 * 创建一个Mandrill Swift Transport驱动程序的实例
     *
     * @return \Illuminate\Mail\Transport\MandrillTransport
     */
    protected function createMandrillDriver()
    {
        $config = $this->app['config']->get('services.mandrill', []);

        return new MandrillTransport(
            $this->guzzle($config), $config['secret']
        );
    }

    /**
     * Create an instance of the SparkPost Swift Transport driver.
	 * 创建SparkPost Swift运输驱动程序的实例
     *
     * @return \Illuminate\Mail\Transport\SparkPostTransport
     */
    protected function createSparkPostDriver()
    {
        $config = $this->app['config']->get('services.sparkpost', []);

        return new SparkPostTransport(
            $this->guzzle($config), $config['secret'], $config['options'] ?? []
        );
    }

    /**
     * Create an instance of the Log Swift Transport driver.
	 * 创建一个日志快速传输驱动程序的实例。
     *
     * @return \Illuminate\Mail\Transport\LogTransport
     */
    protected function createLogDriver()
    {
        return new LogTransport($this->app->make(LoggerInterface::class));
    }

    /**
     * Create an instance of the Array Swift Transport Driver.
	 * 创建Array Swift Transport Driver的实例
     *
     * @return \Illuminate\Mail\Transport\ArrayTransport
     */
    protected function createArrayDriver()
    {
        return new ArrayTransport;
    }

    /**
     * Get a fresh Guzzle HTTP client instance.
	 * 获取一个新的Guzzle HTTP客户端实例
     *
     * @param  array  $config
     * @return \GuzzleHttp\Client
     */
    protected function guzzle($config)
    {
        return new HttpClient(Arr::add(
            $config['guzzle'] ?? [], 'connect_timeout', 60
        ));
    }

    /**
     * Get the default mail driver name.
	 * 获取默认邮件驱动程序名称
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['mail.driver'];
    }

    /**
     * Set the default mail driver name.
	 * 设置默认的邮件驱动程序名称
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['mail.driver'] = $name;
    }
}
