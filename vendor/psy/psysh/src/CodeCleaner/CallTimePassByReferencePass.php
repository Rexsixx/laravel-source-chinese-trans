<?php
/**
 * Psy，代码清洁器，调用时间通过参考传递
 */

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2018 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeCleaner;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use Psy\Exception\FatalErrorException;

/**
 * Validate that the user did not use the call-time pass-by-reference that causes a fatal error.
 * 验证用户没有使用调用时传递引用导致致命错误。
 *
 * As of PHP 5.4.0, call-time pass-by-reference was removed, so using it will raise a fatal error.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class CallTimePassByReferencePass extends CodeCleanerPass
{
    const EXCEPTION_MESSAGE = 'Call-time pass-by-reference has been removed';

    /**
     * Validate of use call-time pass-by-reference.
	 * 验证使用调用时传递引用
     *
     * @throws RuntimeException if the user used call-time pass-by-reference
     *
     * @param Node $node
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof FuncCall && !$node instanceof MethodCall && !$node instanceof StaticCall) {
            return;
        }

        foreach ($node->args as $arg) {
            if ($arg->byRef) {
                throw new FatalErrorException(self::EXCEPTION_MESSAGE, 0, E_ERROR, null, $node->getLine());
            }
        }
    }
}
