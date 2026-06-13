<?php
/**
 * Egulias，电子邮件验证器，结果，理由，期望域文字关闭
 */

namespace Egulias\EmailValidator\Result\Reason;

class ExpectingDomainLiteralClose implements Reason
{
    public function code() : int
    {
        return 137;
    }

    public function description() : string
    {
        return "Closing bracket ']' for domain literal not found";
    }
}
