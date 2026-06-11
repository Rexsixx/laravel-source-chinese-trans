<?php
/**
 * Egulias，电子邮件验证器，结果，理由，期待 CTEXT
 */

namespace Egulias\EmailValidator\Result\Reason;

class ExpectingCTEXT implements Reason
{
    public function code() : int
    {
        return 139;
    }

    public function description() : string
    {
        return 'Expecting CTEXT';
    }
}
