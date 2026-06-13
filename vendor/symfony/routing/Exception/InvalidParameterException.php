<?php
/**
 * Symfony，组件，路由，异常，无效参数异常
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
 * 参数无效时引发的异常。
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class InvalidParameterException extends \InvalidArgumentException implements ExceptionInterface
{
}
