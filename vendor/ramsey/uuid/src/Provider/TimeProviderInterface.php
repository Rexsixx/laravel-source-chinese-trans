<?php
/**
 * Ramsey，Uuid，提供商，时间提供者接口
 */

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://benramsey.com/projects/ramsey-uuid/ Documentation
 * @link https://packagist.org/packages/ramsey/uuid Packagist
 * @link https://github.com/ramsey/uuid GitHub
 */

namespace Ramsey\Uuid\Provider;

/**
 * TimeProviderInterface provides functionality to get the time from a specific
 * type of time provider
 * TimeProviderInterface 提供了从特定类型的计时器获取时间的功能。
 */
interface TimeProviderInterface
{
    /**
     * Returns a timestamp array
	 * 返回时间戳数组
     *
     * @return int[] Array guaranteed to contain `sec` and `usec` components of a timestamp
     */
    public function currentTime();
}
