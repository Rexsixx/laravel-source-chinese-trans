<?php
/**
 * Egulias，电子邮件验证器，结果，理由，连续点
 */

namespace Egulias\EmailValidator\Result\Reason;

class ConsecutiveDot implements Reason
{
    public function code() : int
    {
        return 132;
    }

    public function description() : string
    {
        return 'Concecutive DOT found';
    }
}
