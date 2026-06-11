<?php
/**
 * Egulias，电子邮件验证器，结果，理由，CR No LF
 */

namespace Egulias\EmailValidator\Result\Reason;

class CRNoLF implements Reason
{
    public function code() : int
    {
        return 150;
    }

    public function description() : string
    {
        return 'Missing LF after CR';
    }
}
