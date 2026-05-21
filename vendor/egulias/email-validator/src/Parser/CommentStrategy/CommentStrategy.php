<?php
/**
 * Egulias，EmailValidator，分析程序，评论策略，Comment Strategy
 */

namespace Egulias\EmailValidator\Parser\CommentStrategy;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\Result;

interface CommentStrategy
{
    /**
     * Return "true" to continue, "false" to exit
	 * 返回“true”继续，返回“false”退出。
     */
    public function exitCondition(EmailLexer $lexer, int $openedParenthesis) : bool;

    public function endOfLoopValidations(EmailLexer $lexer) : Result;

    public function getWarnings() : array;
}
