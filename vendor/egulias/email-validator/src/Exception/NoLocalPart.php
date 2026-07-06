<?php
/**
 * Egulias，电子邮件验证器，异常，无本地部件
 */

namespace Egulias\EmailValidator\Exception;

class NoLocalPart extends InvalidEmail
{
    const CODE = 130;
    const REASON = "No local part";
}
