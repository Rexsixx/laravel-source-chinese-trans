<?php
/**
 * Egulias，电子邮件验证器，结果，理由，RFC 警告
 */

namespace Egulias\EmailValidator\Result\Reason;

class RFCWarnings implements Reason
{
    public function code() : int
    {
        return 997;
    }

    public function description() : string
    {
        return 'Warnings found after validating';
    }
}
