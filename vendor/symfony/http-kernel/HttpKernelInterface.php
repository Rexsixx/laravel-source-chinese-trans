<?php
/**
 * Symfony，组件，HttpKernel，Http 内核接口
 */

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HttpKernelInterface handles a Request to convert it to a Response.
 * HttpKernelInterface处理请求并将其转换为响应。
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface HttpKernelInterface
{
    public const MASTER_REQUEST = 1;
    public const SUB_REQUEST = 2;

    /**
     * Handles a Request to convert it to a Response.
	 * 处理请求以将其转换为响应。
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param int  $type  The type of the request
     *                    (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true);
}
