<?php
/**
 * Egulias，EmailValidator，异常，No DNS 记录
 */

namespace Egulias\EmailValidator\Exception;

class NoDNSRecord extends InvalidEmail
{
    const CODE = 5;
    const REASON = 'No MX or A DSN record was found for this email';
}
