<?php
/**
 * Egulias，电子邮件验证器，结果，理由，最后的 CRLF
 */

namespace Egulias\EmailValidator\Result\Reason;

class CRLFAtTheEnd implements Reason
{
    public const CODE = 149;
    public const REASON = "CRLF at the end";

    public function code() : int
    {
        return 149;
    }

    public function description() : string
    {
        return 'CRLF at the end';
    }
}
