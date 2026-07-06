<?php
/**
 * Egulias，电子邮件验证器，结果，理由，未闭合引用字符串
 */

namespace Egulias\EmailValidator\Result\Reason;

class UnclosedQuotedString implements Reason
{
    public function code() : int
    {
        return 145;
    }

    public function description() : string
    {
        return "Unclosed quoted string";
    }
}
