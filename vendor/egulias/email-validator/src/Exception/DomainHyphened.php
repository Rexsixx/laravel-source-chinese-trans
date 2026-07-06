<?php
/**
 * Egulias，电子邮件验证器，异常，域字符
 */

namespace Egulias\EmailValidator\Exception;

class DomainHyphened extends InvalidEmail
{
    const CODE = 144;
    const REASON = "Hyphen found in domain";
}
