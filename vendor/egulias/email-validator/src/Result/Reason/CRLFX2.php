<?php
/**
 * Egulias，电子邮件验证器，结果，理由，CRLFX2
 */

namespace Egulias\EmailValidator\Result\Reason;

class CRLFX2 implements Reason
{
    public function code() : int
    {
        return 148;
    }

    public function description() : string
    {
        return 'CR  LF tokens found twice';
    }
}
