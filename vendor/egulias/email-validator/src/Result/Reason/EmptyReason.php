<?php
/**
 * Egulias，电子邮件验证器，结果，理由，空原因
 */

namespace Egulias\EmailValidator\Result\Reason;

class EmptyReason implements Reason
{
    public function code() : int
    {
        return 0;
    }

    public function description() : string
    {
        return 'Empty reason';
    }
}
