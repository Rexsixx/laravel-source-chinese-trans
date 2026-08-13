<?php
/**
 * Egulias，EmailValidator，异常，域名不接受邮件
 */

namespace Egulias\EmailValidator\Exception;

class DomainAcceptsNoMail extends InvalidEmail
{
    const CODE = 154;
    const REASON = 'Domain accepts no mail (Null MX, RFC7505)';
}