<?php
/**
 * Symfony，组件，Http内核，缓存Warmer，Cache Warmer 接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\CacheWarmer;

/**
 * Interface for classes able to warm up the cache.
 * 可用于加速缓存的类的接口。
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface CacheWarmerInterface extends WarmableInterface
{
    /**
     * Checks whether this warmer is optional or not.
	 * 检查这个温度是否可选。
     *
     * Optional warmers can be ignored on certain conditions.
     *
     * A warmer should return true if the cache can be
     * generated incrementally and on-demand.
     *
     * @return bool true if the warmer is optional, false otherwise
     */
    public function isOptional();
}
