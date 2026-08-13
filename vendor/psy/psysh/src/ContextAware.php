<?php
/**
 * Psy，上下文感知
 */

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2023 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy;

/**
 * ContextAware interface.
 * 上下文感知接口
 *
 * This interface is used to pass the Shell's context into commands and such
 * which require access to the current scope variables.
 * 这个接口用于将Shell的上下文传递到命令中,这样就需要访问当前的范围变量。
 */
interface ContextAware
{
    /**
     * Set the Context reference.
	 * 设置上下文引用
     *
     * @param Context $context
     */
    public function setContext(Context $context);
}
