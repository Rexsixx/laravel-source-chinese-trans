<?php
/**
 * BeyondCode，辅助
 */

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
	 * 获取配置路径。
     *
     * This is a polyfill for the missing shorthand function in lumen.
	 * 这是一个在lumen中丢失的速记函数的多填充。
     *
     * @param  string  $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->basePath('config').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}
