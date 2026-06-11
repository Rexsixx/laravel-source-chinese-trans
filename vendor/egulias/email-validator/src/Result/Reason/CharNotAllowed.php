<?php
/**
 * Egulias，电子邮件验证器，结果，理由，不允许的字符
 */

namespace Egulias\EmailValidator\Result\Reason;

class CharNotAllowed implements Reason
{
    public function code() : int
    {
        return 1;
    }

    public function description() : string
    {
        return "Character not allowed";
    }
}
