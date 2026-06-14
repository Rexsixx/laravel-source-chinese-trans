<?php
/**
 * Symfony，组件，路由，匹配程序，转储，匹配器转储接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher\Dumper;

use Symfony\Component\Routing\RouteCollection;

/**
 * MatcherDumperInterface is the interface that all matcher dumper classes must implement.
 * MatcherDumperInterface是所有匹配器转储类必须实现的接口。
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface MatcherDumperInterface
{
    /**
     * Dumps a set of routes to a string representation of executable code
     * that can then be used to match a request against these routes.
	 * 将一组路由转换为可执行代码的字符串表示形式，以便后续用于将请求与这些路由进行匹配。
     *
     * @return string Executable code
     */
    public function dump(array $options = []);

    /**
     * Gets the routes to dump.
	 * 获取要转储的路由
     *
     * @return RouteCollection A RouteCollection instance
     */
    public function getRoutes();
}
