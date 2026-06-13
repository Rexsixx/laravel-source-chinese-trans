<?php
/**
 * Egulias，电子邮件验证器，分析程序，ID 左部分
 */

namespace Egulias\EmailValidator\Parser;

use Egulias\EmailValidator\Result\Result;
use Egulias\EmailValidator\Result\InvalidEmail;
use Egulias\EmailValidator\Result\Reason\CommentsInIDRight;

class IDLeftPart extends LocalPart
{
    protected function parseComments(): Result
    {
       return new InvalidEmail(new CommentsInIDRight(), ((array) $this->lexer->token)['value']);
    }
}
