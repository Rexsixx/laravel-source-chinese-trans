<?php
/**
 * Symfony，组件，路由选择，异常，无效参数异常
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
 * Exception thrown when a parameter is not valid.
 * 当参数无效时抛出异常。
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class InvalidParameterException extends \InvalidArgumentException implements ExceptionInterface
{
}
