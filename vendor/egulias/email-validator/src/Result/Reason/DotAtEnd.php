<?php
/**
 * Egulias，电子邮件验证器，结果，理由，点结束
 */

namespace Egulias\EmailValidator\Result\Reason;

class DotAtEnd implements Reason
{
    public function code() : int
    {
        return 142;
    }

    public function description() : string
    {
        return 'Dot at the end';
    }
}
