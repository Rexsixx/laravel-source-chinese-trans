<?php
/**
 * Symfony，组件，Http内核，依赖注入，记录器通过
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\DependencyInjection;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Log\Logger;

/**
 * Registers the default logger if necessary.
 * 如果需要,注册默认的记录器。
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class LoggerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias(LoggerInterface::class, 'logger')
            ->setPublic(false);

        if ($container->has('logger')) {
            return;
        }

        $container->register('logger', Logger::class)
            ->setPublic(false);
    }
}
