<?php
/**
 * Illuminate，控制台，检测应用程序命名空间
 */

namespace Illuminate\Console;

use Illuminate\Container\Container;

trait DetectsApplicationNamespace
{
    /**
     * Get the application namespace.
	 * 获取应用程序命名空间
     *
     * @return string
     */
    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }
}
