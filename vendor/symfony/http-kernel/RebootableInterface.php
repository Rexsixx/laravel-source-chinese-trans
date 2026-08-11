<?php
/**
 * Symfony，组件，Http内核，重启接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

/**
 * Allows the Kernel to be rebooted using a temporary cache directory.
 * 允许内核使用临时缓存目录重新启动。
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface RebootableInterface
{
    /**
     * Reboots a kernel.
	 * 重新引导一个内核。
     *
     * The getCacheDir() method of a rebootable kernel should not be called
     * while building the container. Use the %kernel.cache_dir% parameter instead.
     *
     * @param string|null $warmupDir pass null to reboot in the regular cache directory
     */
    public function reboot($warmupDir);
}
