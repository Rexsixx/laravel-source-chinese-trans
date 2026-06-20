<?php declare(strict_types=1);

/**
 * SebastianBergmann，环境，操作系统
 */

/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

final class OperatingSystem
{
    /**
     * Returns PHP_OS_FAMILY (if defined (which it is on PHP >= 7.2)).
     * Returns a string (compatible with PHP_OS_FAMILY) derived from PHP_OS otherwise.
	 * 返回PHP_OS_FAMILY(如果定义(它在PHP > = 7.2))。
	 * 返回来自PHP_OS的字符串(与PHP_OS_FAMILY兼容)。
     */
    public function getFamily(): string
    {
        if (\defined('PHP_OS_FAMILY')) {
            return \PHP_OS_FAMILY;
        }

        if (\DIRECTORY_SEPARATOR === '\\') {
            return 'Windows';
        }

        switch (\PHP_OS) {
            case 'Darwin':
                return 'Darwin';

            case 'DragonFly':
            case 'FreeBSD':
            case 'NetBSD':
            case 'OpenBSD':
                return 'BSD';

            case 'Linux':
                return 'Linux';

            case 'SunOS':
                return 'Solaris';

            default:
                return 'Unknown';
        }
    }
}
