<?php
/**
 * Egulias，电子邮件验证器，结果，理由，无局部部分
 */

namespace Egulias\EmailValidator\Result\Reason;

class NoLocalPart implements Reason 
{
    public function code() : int
    {
        return 130;
    }

    public function description() : string
    {
        return "No local part";
    }
}
