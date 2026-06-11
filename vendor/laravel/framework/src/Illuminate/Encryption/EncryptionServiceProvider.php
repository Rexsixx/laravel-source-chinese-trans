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
			// 如果关键从“base64”开始,那么我们就需要在将密钥解码到加密器之前需要解码密钥。
			// 密钥可能是base-64编码的表示,我们想要确保在加密之前将它们转换回原始字节。
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
