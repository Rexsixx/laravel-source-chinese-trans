<?php
/**
 * Symfony，组件，路由，异常，缺少必选参数异常
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Exception;

/**
 * Exception thrown when a route cannot be generated because of missing
 * mandatory parameters.
 * 由于缺少必需参数而无法生成路由时抛出异常。
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class MissingMandatoryParametersException extends \InvalidArgumentException implements ExceptionInterface
{
}
