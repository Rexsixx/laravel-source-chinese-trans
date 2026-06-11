<?php
/**
 * Egulias，电子邮件验证器，结果，理由，域 Hyphened
 */

namespace Egulias\EmailValidator\Result\Reason;

class DomainHyphened extends DetailedReason
{
    public function code() : int
    {
        return 144;
    }

    public function description() : string
    {
        return 'S_HYPHEN found in domain';
    }
}
