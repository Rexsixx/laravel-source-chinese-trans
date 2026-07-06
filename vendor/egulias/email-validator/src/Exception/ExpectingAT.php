<?php
/**
 * Egulias，电子邮件验证器，异常，期待 AT
 */

namespace Egulias\EmailValidator\Exception;

class ExpectingAT extends InvalidEmail
{
    const CODE = 202;
    const REASON = "Expecting AT '@' ";
}
