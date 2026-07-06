<?php
/**
 * Symfony，组件，路由选择，生成器，Dumper，生成器Dumper
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Generator\Dumper;

use Symfony\Component\Routing\RouteCollection;

/**
 * GeneratorDumper is the base class for all built-in generator dumpers.
 * GeneratorDumper是所有内置发电机dumpers的基类。
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class GeneratorDumper implements GeneratorDumperInterface
{
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
