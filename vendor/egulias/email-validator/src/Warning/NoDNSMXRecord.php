<?php
/**
 * Egulias，电子邮件验证器，警告，No DNS MX Record
 */

namespace Egulias\EmailValidator\Warning;

class NoDNSMXRecord extends Warning
{
    const CODE = 6;

    public function __construct()
    {
        $this->message = 'No MX DSN record was found for this email';
        $this->rfcNumber = 5321;
    }
}
