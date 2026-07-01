<?php
/**
 * Egulias，电子邮件验证器，异常，无域名部件
 */

namespace Egulias\EmailValidator\Exception;

class NoDomainPart extends InvalidEmail
{
    const CODE = 131;
    const REASON = "No Domain part";
}
