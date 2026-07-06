<?php
/**
 * Egulias，电子邮件验证器，结果，理由，无DNS记录
 */

namespace Egulias\EmailValidator\Result\Reason;

class NoDNSRecord implements Reason 
{
    public function code() : int
    {
        return 5;
    }

    public function description() : string
    {
        return 'No MX or A DSN record was found for this email';
    }
}
