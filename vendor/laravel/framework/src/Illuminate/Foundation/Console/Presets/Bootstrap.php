<?php
/**
 * Illuminate，基础，控制台，预先设置，引导
 */

namespace Illuminate\Foundation\Console\Presets;

class Bootstrap extends Preset
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
        static::updateSass();
        static::removeNodeModules();
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
        return [
            'bootstrap-sass' => '^3.3.7',
            'jquery' => '^3.1.1',
        ] + $packages;
    }

    /**
     * Update the Sass files for the application.
	 * 更新应用程序的Sass文件
     *
     * @return void
     */
    protected static function updateSass()
    {
        copy(__DIR__.'/bootstrap-stubs/_variables.scss', resource_path('assets/sass/_variables.scss'));
        copy(__DIR__.'/bootstrap-stubs/app.scss', resource_path('assets/sass/app.scss'));
    }
}
