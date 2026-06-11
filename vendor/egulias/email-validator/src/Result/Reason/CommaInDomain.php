<?php
/**
 * Egulias，电子邮件验证器，结果，理由，逗号在域中
 */

namespace Egulias\EmailValidator\Result\Reason;

class CommaInDomain implements Reason
{
    public function code() : int
    {
        return 200;
    }

    public function description() : string
    {
        return "Comma ',' is not allowed in domain part";
    }
}
