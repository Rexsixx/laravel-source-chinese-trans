<?php
/**
 * League，普通标记，输入，标记输入接口
 */

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Input;

interface MarkdownInputInterface
{
    public function getContent(): string;

    /**
     * @return iterable<int, string>
     */
    public function getLines(): iterable;

    public function getLineCount(): int;
}
