<?php
/**
 * League，普通标记，内联，分析程序，Bang 解析器
 */

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Parser;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\InlineParserContext;

final class BangParser implements InlineParserInterface
{
    public function getCharacters(): array
    {
        return ['!'];
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $cursor = $inlineContext->getCursor();
        if ($cursor->peek() === '[') {
            $cursor->advanceBy(2);
            $node = new Text('![', ['delim' => true]);
            $inlineContext->getContainer()->appendChild($node);

            // Add entry to stack for this opener
			// 在这个opener的堆栈中添加条目
            $delimiter = new Delimiter('!', 1, $node, true, false, $cursor->getPosition());
            $inlineContext->getDelimiterStack()->push($delimiter);

            return true;
        }

        return false;
    }
}
