<?php
/**
 * Illuminate，基础，控制台，预先设置，无
 */

namespace Illuminate\Foundation\Console\Presets;

use Illuminate\Filesystem\Filesystem;

class None extends Preset
{
    /**
     * Install the preset.
	 * 安装预设
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages();
        static::updateBootstrapping();

        tap(new Filesystem, function ($filesystem) {
            $filesystem->deleteDirectory(resource_path('assets/js/components'));
            $filesystem->delete(resource_path('assets/sass/_variables.scss'));
            $filesystem->deleteDirectory(base_path('node_modules'));
            $filesystem->deleteDirectory(public_path('css'));
            $filesystem->deleteDirectory(public_path('js'));
        });
    }

    /**
     * Update the given package array.
	 * 更新给定的包数组
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        unset(
            $packages['bootstrap-sass'],
            $packages['jquery'],
            $packages['vue'],
            $packages['babel-preset-react'],
            $packages['react'],
            $packages['react-dom']
        );

        return $packages;
    }

    /**
     * Write the stubs for the Sass and JavaScript files.
	 * 编写Sass和JavaScript文件的存根
     *
     * @return void
     */
    protected static function updateBootstrapping()
    {
        file_put_contents(resource_path('assets/sass/app.scss'), ''.PHP_EOL);
        copy(__DIR__.'/none-stubs/app.js', resource_path('assets/js/app.js'));
    }
}
