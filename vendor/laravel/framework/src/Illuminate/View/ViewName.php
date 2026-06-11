<?php
/**
 * Illuminate，视图，视图名称
 */

namespace Illuminate\View;

class ViewName
{
    /**
     * Normalize the given event name.
	 * 将给定事件名称规范化
     *
     * @param  string  $name
     * @return string
     */
    public static function normalize($name)
    {
        $delimiter = ViewFinderInterface::HINT_PATH_DELIMITER;

        if (strpos($name, $delimiter) === false) {
            return str_replace('/', '.', $name);
        }

        [$namespace, $name] = explode($delimiter, $name);

        return $namespace.$delimiter.str_replace('/', '.', $name);
    }
}
