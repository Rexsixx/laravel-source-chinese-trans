<?php
/**
 * Symfony，契约，HttpClient，异常，异常接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Contracts\HttpClient\Exception;

/**
 * The base interface for all exceptions in the contract.
 * 契约中所有异常的基接口。
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
interface ExceptionInterface extends \Throwable
{
}
