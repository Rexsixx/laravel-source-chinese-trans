<?php
/**
 * Egulias，电子邮件验证器，结果，理由，无域部分
 */

namespace Egulias\EmailValidator\Result\Reason;

class NoDomainPart implements Reason
{
    public function code() : int
    {
        return 131;
    }

    public function description() : string
    {
        return 'No domain part found';
    }
}
