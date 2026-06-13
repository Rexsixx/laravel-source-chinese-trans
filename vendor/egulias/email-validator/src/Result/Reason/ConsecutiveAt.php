<?php
/**
 * Egulias，电子邮件验证器，结果，理由，连续
 */

namespace Egulias\EmailValidator\Result\Reason;

class ConsecutiveAt implements Reason
{
    public function code() : int
    {
        return 128;
    }

    public function description() : string
    {
        return '@ found after another @';
    }

}
