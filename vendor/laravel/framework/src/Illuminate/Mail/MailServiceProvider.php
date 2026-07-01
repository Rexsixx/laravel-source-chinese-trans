<?php
/**
 * Illuminate，电子邮件，电子邮件服务提供商
 */

namespace Illuminate\Mail;

use Swift_Mailer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Swift_DependencyContainer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class MailServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->registerSwiftMailer();
        $this->registerIlluminateMailer();
        $this->registerMarkdownRenderer();
    }

    /**
     * Register the Illuminate mailer instance.
	 * 注册照亮邮件实例
     *
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        $this->app->singleton('mailer', function () {
            $config = $this->app->make('config')->get('mail');

            // Once we have create the mailer instance, we will set a container instance
            // on the mailer. This allows us to resolve mailer classes via containers
            // for maximum testability on said classes instead of passing Closures.
			// 一旦我们创建了邮件发送器实例，就会在该邮件发送器上设置一个容器实例。
			// 这使得我们能够通过容器来确定邮件处理类，从而在这些类上实现最大程度的可测试性，而无需传递闭包。
            $mailer = new Mailer(
                $this->app['view'],
                $this->app['swift.mailer'],
                $this->app['events']
            );

            if ($this->app->bound('queue')) {
                $mailer->setQueue($this->app['queue']);
            }

            // Next we will set all of the global addresses on this mailer, which allows
            // for easy unification of all "from" addresses as well as easy debugging
            // of sent messages since they get be sent into a single email address.
			// 接下来，我们将为这个邮件发送器设置所有全局地址，这使得所有“发件人”地址能够轻松统一，
			// 同时也能方便地对已发送的邮件进行调试，因为它们会被发送到一个单一的电子邮件地址中。
            foreach (['from', 'reply_to', 'to'] as $type) {
                $this->setGlobalAddress($mailer, $config, $type);
            }

            return $mailer;
        });
    }

    /**
     * Set a global address on the mailer by type.
	 * 按类型在邮件上设置全局地址
     *
     * @param  \Illuminate\Mail\Mailer  $mailer
     * @param  array  $config
     * @param  string  $type
     * @return void
     */
    protected function setGlobalAddress($mailer, array $config, $type)
    {
        $address = Arr::get($config, $type);

        if (is_array($address) && isset($address['address'])) {
            $mailer->{'always'.Str::studly($type)}($address['address'], $address['name']);
        }
    }

    /**
     * Register the Swift Mailer instance.
	 * 注册Swift Mailer实例
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerSwiftTransport();

        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.
		// 一旦我们完成了传输器的注册，我们就会注册实际的 Swift 信件发送器实例，并传入传输器实例。
		// 这样，如果需要的话，我们可以在应用程序启动时覆盖这个传输器实例。
        $this->app->singleton('swift.mailer', function () {
            if ($domain = $this->app->make('config')->get('mail.domain')) {
                Swift_DependencyContainer::getInstance()
                                ->register('mime.idgenerator.idright')
                                ->asValue($domain);
            }

            return new Swift_Mailer($this->app['swift.transport']->driver());
        });
    }

    /**
     * Register the Swift Transport instance.
	 * 注册Swift Transport实例
     *
     * @return void
     */
    protected function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function () {
            return new TransportManager($this->app);
        });
    }

    /**
     * Register the Markdown renderer instance.
	 * 注册Markdown渲染器实例
     *
     * @return void
     */
    protected function registerMarkdownRenderer()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/resources/views' => $this->app->resourcePath('views/vendor/mail'),
            ], 'laravel-mail');
        }

        $this->app->singleton(Markdown::class, function () {
            $config = $this->app->make('config');

            return new Markdown($this->app->make('view'), [
                'theme' => $config->get('mail.markdown.theme', 'default'),
                'paths' => $config->get('mail.markdown.paths', []),
            ]);
        });
    }

    /**
     * Get the services provided by the provider.
	 * 获取提供者提供的服务
     *
     * @return array
     */
    public function provides()
    {
        return [
            'mailer', 'swift.mailer', 'swift.transport', Markdown::class,
        ];
    }
}
