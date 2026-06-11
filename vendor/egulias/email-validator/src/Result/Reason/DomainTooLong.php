<?php
/**
 * Egulias，电子邮件验证器，结果，理由，域名太长
 */

namespace Egulias\EmailValidator\Result\Reason;

class DomainTooLong implements Reason
{
    public function code() : int
    {
        return 244;
    }

    public function description() : string
    {
        return 'Domain is longer than 253 characters';
    }
}
