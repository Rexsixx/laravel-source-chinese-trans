<?php
/**
 * Egulias，电子邮件验证器，结果，理由，期望文本
 */

namespace Egulias\EmailValidator\Result\Reason;

class ExpectingDTEXT implements Reason
{
    public function code() : int
    {
        return 129;
    }

    public function description() : string
    {
        return 'Expecting DTEXT';
    }
}
