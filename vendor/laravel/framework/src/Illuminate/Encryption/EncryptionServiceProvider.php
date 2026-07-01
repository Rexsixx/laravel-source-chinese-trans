<?php
/**
 * Illuminate，加密，加密服务提供商
 */

namespace Illuminate\Encryption;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
	 * 注册服务提供者
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('encrypter', function ($app) {
            $config = $app->make('config')->get('app');

            // If the key starts with "base64:", we will need to decode the key before handing
            // it off to the encrypter. Keys may be base-64 encoded for presentation and we
            // want to make sure to convert them back to the raw bytes before encrypting.
			// 如果密钥以“base64：”开头，那么在将其传递给加密器之前，我们需要先对其进行解码。
			// 密钥可能经过Base-64编码以进行显示，我们希望在加密前将其转换回原始字节。
            if (Str::startsWith($key = $this->key($config), 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            return new Encrypter($key, $config['cipher']);
        });
    }

    /**
     * Extract the encryption key from the given configuration.
	 * 从给定的配置中提取加密密钥
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function key(array $config)
    {
        return tap($config['key'], function ($key) {
            if (empty($key)) {
                throw new RuntimeException(
                    'No application encryption key has been specified.'
                );
            }
        });
    }
}
