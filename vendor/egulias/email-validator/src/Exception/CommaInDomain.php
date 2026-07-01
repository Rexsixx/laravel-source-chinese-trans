<?php
/**
 * Egulias，电子邮件验证器，异常，逗号在域中
 */

namespace Egulias\EmailValidator\Exception;

class CommaInDomain extends InvalidEmail
{
    const CODE = 200;
    const REASON = "Comma ',' is not allowed in domain part";
}
