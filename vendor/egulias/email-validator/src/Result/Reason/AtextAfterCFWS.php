<?php
/**
 * Egulias，电子邮件验证器，结果，理由，CFWS 后的Atext
 */

namespace Egulias\EmailValidator\Result\Reason;

class AtextAfterCFWS implements Reason
{
    public function code() : int
    {
        return 133;
    }

    public function description() : string
    {
        return 'ATEXT found after CFWS';
    }
}
