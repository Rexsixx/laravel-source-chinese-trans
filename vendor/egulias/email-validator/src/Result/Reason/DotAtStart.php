<?php
/**
 * Egulias，电子邮件验证器，结果，理由，点开始
 */

namespace Egulias\EmailValidator\Result\Reason;

class DotAtStart implements Reason
{
    public function code() : int
    {
        return 141;
    }

    public function description() : string
    {
        return "Starts with a DOT";
    }
}
