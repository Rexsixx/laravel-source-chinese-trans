<?php
/**
 * Ramsey，Uuid，提供商，时间，系统时间提供程序
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

namespace Ramsey\Uuid\Provider\Time;

use Ramsey\Uuid\Provider\TimeProviderInterface;

/**
 * SystemTimeProvider uses built-in PHP functions to provide the time
 * SystemTimeProvider使用内置的PHP函数来提供时间
 */
class SystemTimeProvider implements TimeProviderInterface
{
    /**
     * Returns a timestamp array
	 * 返回时间戳数组
     *
     * @return int[] Array containing `sec` and `usec` components of a timestamp
     */
    public function currentTime()
    {
        return gettimeofday();
    }
}
