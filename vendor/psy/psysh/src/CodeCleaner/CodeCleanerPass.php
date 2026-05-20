<?php
/**
 * Psy，代码清洁器，代码清洁器通过
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

use PhpParser\NodeVisitorAbstract;

/**
 * A CodeCleaner pass is a PhpParser Node Visitor.
 * CodeCleaner pass是PhpParser节点访问者
 */
abstract class CodeCleanerPass extends NodeVisitorAbstract
{
    // Wheee!
}
