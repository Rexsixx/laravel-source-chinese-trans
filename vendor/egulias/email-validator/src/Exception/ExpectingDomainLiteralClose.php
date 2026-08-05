<?php
/**
 * Egulias，电子邮件验证器，异常，期望域文字关闭
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingDomainLiteralClose extends InvalidEmail
{
    const CODE = 137;
    const REASON = "Closing bracket ']' for domain literal not found";
}
