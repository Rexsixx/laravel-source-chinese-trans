<?php
/**
 * Psy，代码清理器，无返回值
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

use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name\FullyQualified as FullyQualifiedName;

/**
 * A class used internally by CodeCleaner to represent input, such as
 * non-expression statements, with no return value.
 * 由CodeCleaner在内部使用的类,表示输入,如非表达式语句,没有返回值。
 *
 * Note that user code returning an instance of this class will act like it
 * has no return value, so you prolly shouldn't do that.
 */
class NoReturnValue
{
    /**
     * Get PhpParser AST expression for creating a new NoReturnValue.
	 * 获取PhpParser AST表达式,以创建一个新的NoReturnValue。
     *
     * @return PhpParser\Node\Expr\New_
     */
    public static function create()
    {
        return new New_(new FullyQualifiedName('Psy\CodeCleaner\NoReturnValue'));
    }
}
